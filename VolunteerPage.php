<?php
require_once 'config.php';
require_once 'Model/Volunteer.php';
require_once 'Model/User.php';
require_once 'includes/PHPMailer/src/PHPMailer.php';
require_once 'includes/PHPMailer/src/SMTP.php';
require_once 'includes/PHPMailer/src/Exception.php';

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

<!DOCTYPE html>
<html>
<head>
    <title>Volunteer Registration</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            background-color: #f5f5f5;
        }



        header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #fff;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .volunteer-form-container {
            flex: 1;
            padding-top: 150px;
            padding-bottom: 50px;
        }

        .volunteer-form {
            background-color: #f7f7f7;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            margin: auto;
            box-sizing: border-box;
        }

        .volunteer-form input, .volunteer-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .volunteer-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .volunteer-form .button-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .volunteer-form button {
            background-color: #0d9488;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            text-align: center;
            color: green;
            margin-top: 10px;
        }

        footer {
            background-color: #fff;
            box-shadow: 0 -1px 5px rgba(0,0,0,0.1);
            padding: 15px 0;
            text-align: center;
            font-size: 14px;
        }

        .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .footer-logo svg {
            vertical-align: middle;
        }

        .logo-text h1 {
            margin: 0;
            font-size: 20px;
        }

        .logo-text span {
            color: #0d9488;
        }

        .nav-links a {
            /* margin-left: 20px; */
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
        }

        .nav-links span {
            margin-left: 5px;
        }

    </style>
</head>
<body>

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
                    <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search...">
                <button onclick="handleSearch()">🔍</button>
              </div>
        
        <nav class="nav-links">
        <a href="get_campaign_data.php">
                    <div class="icon-globe"></div>
                    <span>Campaigns</span>
                </a>
                
                <a href="get_event_data.php">
                        <div class="icon-ticket-check"></div>
                        <span>Events</span>
                </a>

                <a href="EventCountDown.html">
                    <div class="icon-clock"></div>
                    <span>Countdown</span>

                <a href="process_donation.php">
                    <div class="icon-globe"></div>
                    <span>Donate Now</span>
                </a>
                <a href="zakat-calculator.php">
                    <div class="icon-calculator"></div>
                    <span>Calculate Zakat</span>
                </a>
                <a href="financial_aid.php">
                    <div class="icon-landmark"></div>
                    <span>Financial Aid</span>
                </a>
                <a href="VolunteerPage.php">
                    <div class="icon-user"></div>
                    <span>Join US</span>
            </a>
                <a href="Profile/profile.php">
                    <div class="icon-user"></div>
                    <span>Profile</span>
                </a>
        </nav>
    </div>
</header>

<div class="volunteer-form-container">
    <form method="POST" class="volunteer-form">
        <h2>Volunteer Registration</h2>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <textarea name="skills" placeholder="Your Skills" rows="4" required></textarea>
        <div class="button-container">
            <button type="submit">Submit</button>
        </div>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </form>
</div>

<footer>
    <div class="container footer-content">
        <div class="copyright">© 2024 Children of the Land. All rights reserved.</div>
        <div class="footer-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#20B2AA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
            </svg>
        </div>
    </div>
</footer>

</body>
</html>
