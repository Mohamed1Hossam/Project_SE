<?php
require_once '../config.php';
require_once '../Modules/Volunteer.php';
require_once '../Modules/User.php';
require_once '../../includes/PHPMailer/src/PHPMailer.php';
require_once '../../includes/PHPMailer/src/SMTP.php';
require_once '../../includes/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $skills     = $_POST['skills'];

    $user = new User($conn);
    $user_id = $user->createVolunteerUser($email, $first_name, $last_name, $phone);

    if ($user_id) {
        $volunteer = new Volunteer($conn);
        $success = $volunteer->addVolunteer($user_id, $skills);
        $message = $success ? "Volunteer registered successfully!" : "Failed to register volunteer.";

        if ($success) {
            // Send thank-you email to the user
            $subject = "Thank You for Joining Us as a Volunteer!";
            $userMessage = "
            <html>
            <head>
                <title>Welcome to Children of the Land</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    h2 { color: #16a085; }
                    p { margin-bottom: 15px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Welcome to Children of the Land</h2>
                    <p>Dear $first_name $last_name,</p>
                    <p>Thank you for registering as a volunteer with Children of the Land. We are thrilled to have you join our community of changemakers.</p>
                    <p>Your skills and dedication will make a significant impact on our mission to empower communities and bring positive change.</p>
                    <p>If you have any questions or need further assistance, feel free to contact us at any time.</p>
                    <p>Warm regards,<br>
                    The Children of the Land Team</p>
                </div>
            </body>
            </html>
            ";

            try {
                $mail = new PHPMailer(true);

                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'yahiasand@gmail.com';
                $mail->Password   = 'ugna pome glng eriv';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                // Recipients
                $mail->setFrom('noreply@childerenoftheland.org', 'Children of the Land');
                $mail->addAddress($email, "$first_name $last_name");

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $userMessage;
                $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $userMessage));

                $mail->send();
            } catch (Exception $e) {
                $message .= " However, the confirmation email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    } else {
        $message = "Failed to create user.";
    }
}
?>