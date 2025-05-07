<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Initialize variables for form processing
$formSubmitted = false;
$errorMessage = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Require PHPMailer
    
    // Check if PHPMailer is installed via Composer
    if (file_exists('vendor/autoload.php')) {
        require 'vendor/autoload.php';
    } else {
        // If not using Composer, include the PHPMailer files directly
        require 'includes/PHPMailer/src/Exception.php';
        require 'includes/PHPMailer/src/PHPMailer.php';
        require 'includes/PHPMailer/src/SMTP.php';
    }
    
    // Set admin email address
    $admin_email = "mahmoudmohamedds20@gmail.com"; // Replace with actual admin email
    
    // Get form data
    $fullName = $_POST['fullName'] ?? 'Not provided';
    $email = $_POST['email'] ?? 'Not provided';
    $phone = $_POST['phone'] ?? 'Not provided';
    $amount = $_POST['amount'] ?? 'Not provided';
    $reason = $_POST['reason'] ?? 'Not provided';
    $description = $_POST['description'] ?? 'Not provided';
    
    // Create email subject
    $subject = "New Financial Aid Request: $fullName";
    
    // Create email body for admin
    $emailBody = "
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
            <h2>New Financial Aid Request Submitted</h2>
            <p>The following financial aid request has been submitted through the CharityHub website:</p>
            
            <div class='details'>
                <div class='section'>
                    <span class='label'>Applicant Name:</span> $fullName
                </div>
                <div class='section'>
                    <span class='label'>Email Address:</span> $email
                </div>
                <div class='section'>
                    <span class='label'>Phone Number:</span> $phone
                </div>
                <div class='section'>
                    <span class='label'>Amount Requested:</span> $$amount
                </div>
                <div class='section'>
                    <span class='label'>Reason for Aid:</span> $reason
                </div>
                <div class='section'>
                    <span class='label'>Detailed Description:</span>
                    <p>$description</p>
                </div>
            </div>
            
            <p>Please contact the applicant directly to follow up on this request.</p>
            <p>This is an automated message from the CharityHub Financial Aid System.</p>
        </div>
    </body>
    </html>
    ";
    
    // Create confirmation email body for user
    $userMessage = "
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
            <p>Dear $fullName,</p>
            <p>Thank you for submitting your financial aid request to childerenoftheland. We have received your application for financial assistance.</p>
            
            <p><strong>Request Details:</strong></p>
            <ul>
                <li>Reason for Aid: $reason</li>
                <li>Amount Requested: $$amount</li>
            </ul>
            
            <p>Our team will review your request and contact you shortly via email or phone. If you have any questions, please don't hesitate to contact us.</p>
            
            <p>Warm regards,<br>
            The childerenoftheland Team</p>
        </div>
    </body>
    </html>
    ";
    
    try {
        // Send email to admin
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server
        $mail->SMTPAuth   = true;                                 // Enable SMTP authentication
        $mail->Username   = 'mahmoudmohamedds20@gmail.com';               // SMTP username (your Gmail address)
        $mail->Password   = 'ggak mgjp fqlu psir';                  // SMTP password (Gmail app password)
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
        $mail->setFrom('noreply@childerenoftheland.org', 'childerenoftheland');
        $mail->addAddress($admin_email);                          // Add admin as recipient
        $mail->addReplyTo($email, $fullName);                     // Set reply-to as applicant
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $emailBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailBody));
        
        // Send to admin
        $mail->send();
        
        // Send confirmation email to user (using same $mail object)
        $mail->clearAddresses(); // Clear previous recipient (admin)
        $mail->addAddress($email, $fullName); // Add applicant's email
        $mail->Subject = "Your Financial Aid Request - childerenoftheland";
        $mail->Body    = $userMessage;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $userMessage));
        
        // Send to user
        $mail->send();
        
        $formSubmitted = true;
    } catch (Exception $e) {
        $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $formSubmitted = false;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
  <title>childerenoftheland - Financial Aid Request</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
    
    body {
      background-color: #f5f5f5;
      color: #333;
    }
    
    header {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
      position: sticky;
      top: 0;
    }
    
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #333;
      text-decoration: none;
    }
    
    .logo-icon {
      color: #16a085;
      font-size: 24px;
    }
    
    .logo-text h1 {
      font-size: 20px;
      font-weight: 600;
      margin: 0;
    }
    
    .logo-text p {
      font-size: 12px;
      color: #666;
    }
    
    .nav-links {
      display: flex;
      gap: 20px;
    }
    
    .nav-links a {
      text-decoration: none;
      color: #333;
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 14px;
    }
    
    main {
      padding: 40px 0;
    }
    
    .form-container {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      padding: 30px;
      max-width: 700px;
      margin: 0 auto;
    }
    
    .form-container h2 {
      margin-bottom: 10px;
      color: #333;
      font-size: 24px;
    }
    
    .form-container p {
      margin-bottom: 25px;
      color: #666;
      font-size: 14px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #444;
      font-size: 14px;
    }
    
    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      transition: border-color 0.3s;
    }
    
    .form-control:focus {
      outline: none;
      border-color: #16a085;
    }
    
    textarea.form-control {
      resize: vertical;
      min-height: 120px;
    }
    
    .submit-btn {
      display: block;
      width: 100%;
      padding: 14px;
      background-color: #16a085;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    
    .submit-btn:hover {
      background-color: #138a72;
    }
    
    .form-submitted {
      text-align: center;
      padding: 40px 20px;
    }
    
    .form-submitted h3 {
      color: #16a085;
      margin-bottom: 15px;
      font-size: 22px;
    }
    
    .form-submitted p {
      margin-bottom: 20px;
      color: #555;
    }
    
    .return-link {
      display: inline-block;
      color: #16a085;
      text-decoration: none;
      font-weight: 500;
    }
    
    .return-link:hover {
      text-decoration: underline;
    }
    
    .error-message {
      color: #e74c3c;
      font-size: 12px;
      margin-top: 5px;
      display: none;
    }
    
    .input-error {
      border-color: #e74c3c;
    }
    
    @media (max-width: 768px) {
      .form-container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="container">
      <nav class="navbar">
        <a href="HomePage.html" class="logo">
          <div class="logo-icon">
          <div class="icon-heart"></div>
          </div>
          <div class="logo-text">
            <h1>Children of the <span>Land</span></h1>
            <p>Empowering communities together</p>
          </div>
        </a>
        <div class="nav-links">
          <a href="get_campaign_details.php">
          <div class="icon-globe"></div>
            Campaigns
          </a>
          
          <a href="get_event_details.php">
          <div class="icon-ticket-check"></div>
            Events
          </a>
          
          <a href="process_donation.php">
          <div class="icon-globe"></div>
            Donate Now
          </a>

          <a href="zakat-calculator.php">
          <div class="icon-calculator"></div>
            Calculate Zakat
          </a>
          <a href="financial_aid.php">
          <div class="icon-landmark"></div>
            Financial Aid
          </a>
          <a href="profile.php">
          <div class="icon-user"></div>
            Profile
          </a>
        </div>
      </nav>
    </div>
  </header>

  <main>

    <div class="container">
      <?php if ($formSubmitted): ?>
        <!-- Success message when form is submitted successfully -->
        <div class="form-container form-submitted" id="success-message">
          <h3>Request Submitted Successfully!</h3>
          <p>Thank you for submitting your financial aid request. Our team will review your information and contact you within 2-3 business days.</p>
          <p>A confirmation email has been sent to your provided email address.</p>
          <a href="#" class="return-link">Return to Home</a>
        </div>
      <?php else: ?>
        <!-- Form container when not yet submitted or if there was an error -->
        <div class="form-container" id="form-container">
          <h2>Financial Aid Request</h2>
          <p>Please fill out this form with accurate information to help us assess your situation.</p>
          
          <?php if ($errorMessage): ?>
            <div style="color: #e74c3c; margin-bottom: 15px; padding: 10px; background-color: #fadbd8; border-radius: 5px;">
              <?php echo $errorMessage; ?>
            </div>
          <?php endif; ?>
          
          <form id="financial-aid-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
              <label for="fullName">Full Name</label>
              <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter your full name" required>
              <div class="error-message" id="fullName-error">Please enter your full name</div>
            </div>
            
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required>
              <div class="error-message" id="email-error">Please enter a valid email address</div>
            </div>
            
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="Your phone number" required>
              <div class="error-message" id="phone-error">Please enter a valid phone number</div>
            </div>
            
            <div class="form-group">
              <label for="amount">Amount Needed ($)</label>
              <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount needed" min="1" required>
              <div class="error-message" id="amount-error">Please enter a valid amount</div>
            </div>
            
            <div class="form-group">
              <label for="reason">Reason for Aid</label>
              <select class="form-control" id="reason" name="reason" required>
                <option value="" disabled selected>Select a reason</option>
                <option value="Medical">Medical Expenses</option>
                <option value="Education">Educational Support</option>
                <option value="Housing">Housing/Rent Assistance</option>
                <option value="Utilities">Utility Bills</option>
                <option value="Food">Food Assistance</option>
                <option value="Disability">Disability Support</option>
                <option value="Emergency">Emergency Relief</option>
                <option value="Other">Other (Please specify in description)</option>
              </select>
              <div class="error-message" id="reason-error">Please select a reason</div>
            </div>
            
            <div class="form-group">
              <label for="description">Detailed Description</label>
              <textarea class="form-control" id="description" name="description" placeholder="Please provide details about your situation..." required></textarea>
              <div class="error-message" id="description-error">Please provide a detailed description</div>
            </div>
            <button type="submit" class="submit-btn">Submit Request</button>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    // Client-side form validation
    const form = document.getElementById('financial-aid-form');
    
    if (form) {
      form.addEventListener('submit', function(e) {
        // Reset error messages
        document.querySelectorAll('.error-message').forEach(el => {
          el.style.display = 'none';
        });
        document.querySelectorAll('.form-control').forEach(el => {
          el.classList.remove('input-error');
        });
        
        // Validate form
        let isValid = true;
        
        const fullName = document.getElementById('fullName');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');
        const amount = document.getElementById('amount');
        const reason = document.getElementById('reason');
        const description = document.getElementById('description');
        
        if (!fullName.value.trim()) {
          document.getElementById('fullName-error').style.display = 'block';
          fullName.classList.add('input-error');
          isValid = false;
        }
        
        if (!email.value.trim() || !validateEmail(email.value)) {
          document.getElementById('email-error').style.display = 'block';
          email.classList.add('input-error');
          isValid = false;
        }
        
        if (!phone.value.trim()) {
          document.getElementById('phone-error').style.display = 'block';
          phone.classList.add('input-error');
          isValid = false;
        }
        
        if (!amount.value || amount.value <= 0) {
          document.getElementById('amount-error').style.display = 'block';
          amount.classList.add('input-error');
          isValid = false;
        }
        
        if (!reason.value) {
          document.getElementById('reason-error').style.display = 'block';
          reason.classList.add('input-error');
          isValid = false;
        }
        
        if (!description.value.trim()) {
          document.getElementById('description-error').style.display = 'block';
          description.classList.add('input-error');
          isValid = false;
        }
        
        if (!isValid) {
          e.preventDefault(); // Prevent form submission if validation fails
        }
      });
    }
    
    function validateEmail(email) {
      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    }
  </script>
</body>
</html>