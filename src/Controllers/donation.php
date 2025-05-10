<?php
include '../config.php';
include '../Modules/Donation.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

// Add TCPDF for PDF generation
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');

// Enable mail logging
ini_set('mail.log', '/var/log/php_mail.log');
ini_set('mail.add_x_header', 'On');

// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'abdoadelfarouk4@gmail.com'); // Replace with your Gmail address
define('SMTP_PASS', 'mfia rshi kfhs bdip'); // Replace with your Gmail app password

// Enable error logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    header('Content-Type: application/json');

    // Log raw input
    $raw_input = file_get_contents("php://input");
    error_log("Raw donation input: " . $raw_input);

    $data = json_decode($raw_input, true);

    if ($data === null) {
        error_log("JSON decode failed: " . json_last_error_msg());
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
        exit;
    }

    // Extract campaign ID - try both direct form data and URL parameter
    $campaignId = null;
    if (isset($data['campaignId']) && !empty($data['campaignId'])) {
        $campaignId = intval($data['campaignId']);
        error_log("Campaign ID from form data: " . $campaignId);
    } elseif (isset($_GET['campaign_id']) && !empty($_GET['campaign_id'])) {
        $campaignId = intval($_GET['campaign_id']);
        error_log("Campaign ID from URL: " . $campaignId);
    }
    // Debug output of campaign_id to browser (not recommended in production API)
    echo isset($_GET['campaign_id']) ? htmlspecialchars($_GET['campaign_id']) : '';

    error_log("Final campaign ID to be used: " . ($campaignId ?? 'null'));

    // Extract and sanitize input data
    $name = trim($data['donorName']);
    $email = trim($data['donorEmail']);
    $amount = floatval($data['donationAmount']);
    $paymentMethod = $data['paymentMethod'];
    $donationType = $data['donationType'];
    $recurringFrequency = isset($data['recurringFrequency']) ? $data['recurringFrequency'] : null;

    // Validate required fields
    if (empty($name) || empty($email) || empty($amount) || empty($paymentMethod) || empty($donationType)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    if (!is_numeric($amount) || $amount <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid donation amount.']);
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // First check if user exists
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Create new user if doesn't exist with a default hashed password
            $defaultPassword = password_hash('guest-' . time(), PASSWORD_DEFAULT); // Create a unique default password
            $stmt = $conn->prepare("INSERT INTO user (email, first_name, password, is_donor) VALUES (?, ?, ?, 1)");
            $stmt->bind_param("sss", $email, $name, $defaultPassword);
            $stmt->execute();
            $userId = $stmt->insert_id;
        } else {
            $row = $result->fetch_assoc();
            $userId = $row['user_id'];
            // Update user to be a donor if not already
            $stmt = $conn->prepare("UPDATE user SET is_donor = 1 WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }

        // Check if donor record exists
        $stmt = $conn->prepare("SELECT donor_id FROM donor WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Create new donor record
            $stmt = $conn->prepare("INSERT INTO donor (user_id, preferred_donation_type, total_donation_amount, donation_count) VALUES (?, ?, ?, 1)");
            $stmt->bind_param("isi", $userId, $donationType, $amount);
            $stmt->execute();
            $donorId = $stmt->insert_id;
        } else {
            $row = $result->fetch_assoc();
            $donorId = $row['donor_id'];
            // Update donor statistics
            $stmt = $conn->prepare("UPDATE donor SET total_donation_amount = total_donation_amount + ?, donation_count = donation_count + 1, last_donation_date = NOW() WHERE donor_id = ?");
            $stmt->bind_param("di", $amount, $donorId);
            $stmt->execute();
        }

        // Insert donation record
        $stmt = $conn->prepare("INSERT INTO donation (donor_id, campaign_id, payment_method, donation_date, status) VALUES (?, ?, ?, NOW(), 'completed')");
        $stmt->bind_param("iis", $donorId, $campaignId, $paymentMethod);
        $stmt->execute();
        $donationId = $stmt->insert_id;

        // Insert specific donation type record
        if ($donationType === 'onetime') {
            $stmt = $conn->prepare("INSERT INTO onetime_donation (donation_id, amount) VALUES (?, ?)");
            $stmt->bind_param("id", $donationId, $amount);
            $stmt->execute();
        } elseif ($donationType === 'recurring') {
            if (empty($recurringFrequency)) {
                throw new Exception('Recurring frequency is required for recurring donations.');
            }
            // Set the interval based on frequency
            $intervalStr = $recurringFrequency === 'weekly' ? '1 WEEK' : '1 MONTH';
            $stmt = $conn->prepare("INSERT INTO recurring_donation (donation_id, amount, frequency, donation_date, next_donation_date) VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL " . $intervalStr . "))");
            $stmt->bind_param("ids", $donationId, $amount, $recurringFrequency);
            $stmt->execute();
        }

        // Update campaign total if campaign_id is provided
        if ($campaignId) {
            // First update total_collected
            $stmt = $conn->prepare("UPDATE campaign SET 
                total_collected = total_collected + ?, 
                total_direct_donations = total_direct_donations + 1
                WHERE campaign_id = ?");
            $stmt->bind_param("di", $amount, $campaignId);
            $stmt->execute();

            // Then update remaining_target using the new total_collected value
            $stmt = $conn->prepare("UPDATE campaign SET 
                remaining_target = target_amount - total_collected 
                WHERE campaign_id = ?");
            $stmt->bind_param("i", $campaignId);
            $stmt->execute();
        }

        // Insert payment record with payment_id as transaction_id
        $stmt = $conn->prepare("INSERT INTO payment (donation_id, amount, payment_method, payment_date, status) VALUES (?, ?, ?, NOW(), 'completed')");
        $stmt->bind_param("ids", $donationId, $amount, $paymentMethod);
        $stmt->execute();
        $paymentId = $stmt->insert_id;
        
        // Update the payment record with the payment_id as transaction_id
        $stmt = $conn->prepare("UPDATE payment SET transaction_id = ? WHERE payment_id = ?");
        $stmt->bind_param("si", $paymentId, $paymentId);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        // Get campaign name if campaign_id exists
        $campaignName = '';
        if ($campaignId) {
            $stmt = $conn->prepare("SELECT name FROM campaign WHERE campaign_id = ?");
            $stmt->bind_param("i", $campaignId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $campaignName = $row['name'];
            }
        }

        // After successful donation processing and before sending email
        // Generate PDF Receipt
        class MYPDF extends TCPDF {
            public function Header() {
                $this->SetFont('helvetica', 'B', 20);
                $this->Cell(0, 15, 'Donation Receipt', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            }
            
            public function Footer() {
                $this->SetY(-15);
                $this->SetFont('helvetica', 'I', 8);
                $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
            }
        }

        // Create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Children of the Land');
        $pdf->SetTitle('Donation Receipt');

        // Set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Add content to PDF
        $pdf->Ln(20);
        $pdf->Cell(0, 10, 'Thank you for your donation!', 0, 1, 'L');
        $pdf->Ln(10);

        // Donor Information
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Donor Information:', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $name, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Email: ' . $email, 0, 1, 'L');
        $pdf->Ln(10);

        // Donation Details
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Donation Details:', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Amount: EGP ' . number_format($amount, 2), 0, 1, 'L');
        $pdf->Cell(0, 10, 'Donation Type: ' . ucfirst($donationType), 0, 1, 'L');
        if ($donationType === 'recurring') {
            $pdf->Cell(0, 10, 'Frequency: ' . ucfirst($recurringFrequency), 0, 1, 'L');
            // Get next donation date
            $stmt = $conn->prepare("SELECT next_donation_date FROM recurring_donation WHERE donation_id = ?");
            $stmt->bind_param("i", $donationId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $pdf->Cell(0, 10, 'Next Donation Date: ' . date('F j, Y', strtotime($row['next_donation_date'])), 0, 1, 'L');
            }
        }
        if ($campaignName) {
            $pdf->Cell(0, 10, 'Campaign: ' . $campaignName, 0, 1, 'L');
        }
        $pdf->Cell(0, 10, 'Transaction ID: ' . $paymentId, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Date: ' . date('F j, Y'), 0, 1, 'L');
        $pdf->Ln(10);

        // Thank you message
        $pdf->SetFont('helvetica', 'I', 12);
        $pdf->Cell(0, 10, 'Thank you for your generosity and support!', 0, 1, 'L');
        $pdf->Ln(10);

        // Save PDF to a temporary file
        $pdfPath = sys_get_temp_dir() . '/donation_receipt_' . $paymentId . '.pdf';
        $pdf->Output($pdfPath, 'F');

        // Create email subject
        $subject = "Thank You for Your Donation - Children of the Land";
        
        // Create email body
        $emailBody = "
        <html>
        <head>
            <title>Thank You for Your Donation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                h2 { color: #16a085; }
                .details { background-color: #f5f5f5; padding: 15px; border-radius: 5px; }
                .label { font-weight: bold; }
                .section { margin-bottom: 15px; }
                .amount { font-size: 24px; color: #16a085; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Thank You for Your Generous Donation!</h2>
                <p>Dear " . htmlspecialchars($name) . ",</p>
                <p>We are incredibly grateful for your generous donation to Children of the Land. Your support makes a real difference in our mission to empower communities.</p>
                
                <div class='details'>
                    <div class='section'>
                        <span class='label'>Donation Amount:</span> <span class='amount'>EGP " . number_format($amount, 2) . "</span>
                    </div>
                    <div class='section'>
                        <span class='label'>Donation Type:</span> " . ucfirst($donationType) . "
                    </div>" .
                    ($donationType === 'recurring' ? "
                    <div class='section'>
                        <span class='label'>Frequency:</span> " . ucfirst($recurringFrequency) . "
                    </div>" : "") .
                    ($campaignName ? "
                    <div class='section'>
                        <span class='label'>Campaign:</span> " . htmlspecialchars($campaignName) . "
                    </div>" : "") . "
                    <div class='section'>
                        <span class='label'>Transaction ID:</span> " . $paymentId . "
                    </div>
                </div>
                
                <p>Your contribution will help us continue our work in supporting communities and making a positive impact.</p>
                
                <p>Please find your donation receipt attached to this email.</p>
                
                <p>If you have any questions about your donation, please don't hesitate to contact us.</p>
                
                <p>With sincere gratitude,<br>
                The Children of the Land Team</p>
            </div>
        </body>
        </html>";
        
        // Send email to donor
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server
        $mail->SMTPAuth   = true;                                 // Enable SMTP authentication
        $mail->Username   = 'abdoadelfarouk4@gmail.com';         // SMTP username (your Gmail address)
        $mail->Password   = 'mfia rshi kfhs bdip';               // SMTP password (Gmail app password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Enable TLS encryption
        $mail->Port       = 587;                                  // TCP port to connect to
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        
        // Recipients
        $mail->setFrom('noreply@childerenoftheland.org', 'Children of the Land');
        $mail->addAddress($email, $name);                         // Add donor as recipient
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $emailBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailBody));
        
        // Attach PDF receipt
        $mail->addAttachment($pdfPath, 'Donation_Receipt.pdf');
        
        // Send email
        $mail->send();
        
        // Delete temporary PDF file
        unlink($pdfPath);
        
        // Send success response
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your donation! A confirmation email with your receipt has been sent to your email address.']);

        $conn->close();
        exit;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error processing donation: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing your donation. Please try again.']);
        $conn->close();
        exit;
    }
}
?>