<?php
class FinancialAidModel {
    private $db;
    private $mailer;

    public function __construct() {
        // Database connection can be initialized here if needed
        $this->initializeMailer();
    }    private function initializeMailer() {
        require_once __DIR__ . '/../includes/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../includes/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../includes/PHPMailer/src/SMTP.php';

        $this->mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'mahmoudmohamedds20@gmail.com';
        $this->mailer->Password = 'ggak mgjp fqlu psir';
        $this->mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        
        $this->mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
    }

    public function submitFinancialAidRequest($data) {
        try {
            // Send email to admin
            $this->sendAdminEmail($data);
            
            // Send confirmation email to user
            $this->sendUserConfirmationEmail($data);
            
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function sendAdminEmail($data) {
        $this->mailer->clearAddresses();
        $this->mailer->setFrom('noreply@childerenoftheland.org', 'childerenoftheland');
        $this->mailer->addAddress('minaessam1810@gmail.com');
        $this->mailer->addReplyTo($data['email'], $data['fullName']);
        
        $this->mailer->isHTML(true);
        $this->mailer->Subject = "New Financial Aid Request: {$data['fullName']}";
        $this->mailer->Body = $this->getAdminEmailTemplate($data);
        
        return $this->mailer->send();
    }

    private function sendUserConfirmationEmail($data) {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($data['email'], $data['fullName']);
        $this->mailer->Subject = "Your Financial Aid Request - childerenoftheland";
        $this->mailer->Body = $this->getUserEmailTemplate($data);
        
        return $this->mailer->send();
    }

    private function getAdminEmailTemplate($data) {
        return "
        <html>
        <head>
            <title>New Financial Aid Request</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                h2 { color: #16a085; }
                .details { background-color: #f5f5f5; padding: 15px; border-radius: 5px; }
                .label { font-weight: bold; }
                .section { margin-bottom: 15px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>New Financial Aid Request</h2>
                <div class='details'>
                    <div class='section'>
                        <span class='label'>Applicant Name:</span> {$data['fullName']}
                    </div>
                    <div class='section'>
                        <span class='label'>Email Address:</span> {$data['email']}
                    </div>
                    <div class='section'>
                        <span class='label'>Phone Number:</span> {$data['phone']}
                    </div>
                    <div class='section'>
                        <span class='label'>Amount Requested:</span> $" . number_format($data['amount'], 2) . "
                    </div>
                    <div class='section'>
                        <span class='label'>Reason:</span> {$data['reason']}
                    </div>
                    <div class='section'>
                        <span class='label'>Additional Details:</span><br>
                        " . nl2br(htmlspecialchars($data['description'] ?? 'No additional details provided')) . "
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }

    private function getUserEmailTemplate($data) {
        return "
        <html>
        <head>
            <title>Financial Aid Request Confirmation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                h2 { color: #16a085; }
                p { margin-bottom: 15px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h2>Financial Aid Request Confirmation</h2>
                <p>Dear {$data['fullName']},</p>
                <p>Thank you for submitting your financial aid request to childerenoftheland. We have received your application for financial assistance.</p>
                <p><strong>Amount Requested:</strong> $" . number_format($data['amount'], 2) . "</p>
                <p><strong>Reason for Request:</strong> {$data['reason']}</p>
                <p><strong>Additional Details:</strong><br>
                " . nl2br(htmlspecialchars($data['description'] ?? 'No additional details provided')) . "</p>
                <p>Our team will review your request and contact you shortly via email or phone.</p>
                <p>Warm regards,<br>
                The childerenoftheland Team</p>
            </div>
        </body>
        </html>";
    }
}
