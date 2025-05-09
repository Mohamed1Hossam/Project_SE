<?php
require_once 'config.php';

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

        // Send success response
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you for your donation, ' . htmlspecialchars($name) . '!',
            'data' => [
                'donation_id' => $donationId,
                'amount' => $amount,
                'type' => $donationType,
                'date' => date('Y-m-d H:i:s')
            ]
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    $conn->close();
    exit;
}
?>

<!-- HTML PART (for GET requests) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Page</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <link rel="stylesheet" href="charity_project/bootstrap.min.css">
    <script src="Script.js" defer></script>
</head>
<body class="home-page">
<header>
    <div class="container header-content">
        <div class="logo">
            <div class="logo-circle">
                <div class="icon-heart"></div>
            </div>
            <div class="logo-text">
                <h1>Children of the <span>Land</span></h1>
                <p>Empowering communities together</p>
            </div>
        </div>
        <nav class="nav-links">
            <a href="get_campaign_data.php"><div class="icon-globe"></div><span>Campaigns</span></a>
            <a href="#"><div class="icon-calculator"></div><span>Calculate Zakat</span></a>
            <a href="#"><div class="icon-landmark"></div><span>Financial Aid</span></a>
            <a href="#"><div class="icon-user"></div><span>Profile</span></a>
        </nav>
    </div>
</header>
<main>
    <div class="donation-page-container">
        <h1 class="donation-title">Donation Page</h1>
        <div id="donationFormContainer" class="donation-form-container">
            <form id="donationForm">
                <input type="hidden" id="campaignId" name="campaignId" value="<?php echo isset($_GET['campaign_id']) ? htmlspecialchars($_GET['campaign_id']) : ''; ?>">
                <div class="form-group">
                    <label for="donorName">Name:</label>
                    <input type="text" id="donorName" name="donorName" required>
                </div>
                <div class="form-group">
                    <label for="donorEmail">Email:</label>
                    <input type="email" id="donorEmail" name="donorEmail" required>
                </div>
                <div class="form-group">
                    <label for="donationAmount">Amount to Donate(EGP):</label>
                    <input type="number" id="donationAmount" name="donationAmount" min="1" required>
                </div>

                <div class="form-group">
                    <label>Donation Type:</label>
                    <div class="donation-type-options">
                        <label><input type="radio" name="donationType" value="onetime"> One-Time</label>
                        <label><input type="radio" name="donationType" value="recurring"> Recurring</label>
                    </div>
                </div>

                <div id="recurringOptions" class="form-group hidden">
                    <label>Recurring Frequency:</label>
                    <div class="recurring-options">
                        <label><input type="radio" name="recurringFrequency" value="weekly"> Weekly</label>
                        <label><input type="radio" name="recurringFrequency" value="monthly"> Monthly</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Payment Method:</label>
                    <div class="payment-options">
                        <label><input type="radio" name="paymentMethod" value="credit_card"> Credit Card</label>
                        <label><input type="radio" name="paymentMethod" value="paypal"> PayPal</label>
                    </div>
                </div>
                <div id="creditCardFields" class="hidden">
                    <div class="form-group">
                        <label for="cardNumber">Card Number:</label>
                        <input type="text" id="cardNumber" name="cardNumber" pattern="^[0-9]{16}$" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label for="cardExpiry">Expiry Date (MM/YY):</label>
                        <input type="text" id="cardExpiry" name="cardExpiry" pattern="^(0[1-9]|1[0-2])\/[0-9]{2}$" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cardCvv">CVV:</label>
                        <input type="password" id="cardCvv" name="cardCvv" pattern="^[0-9]{3}$" maxlength="3">
                    </div>
                </div>
                <div id="paypalFields" class="hidden">
                    <div class="form-group">
                        <label for="paypalEmail">PayPal Email:</label>
                        <input type="email" id="paypalEmail" name="paypalEmail">
                    </div>
                    <div class="form-group">
                        <label for="paypalPassword">PayPal Password:</label>
                        <input type="password" id="paypalPassword" name="paypalPassword">
                    </div>
                </div>
                <button class="btnd" type="submit">Donate Now</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>