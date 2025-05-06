<?php
// send_email.php - Simple script to send form data to admin email

// Set admin email address
$admin_email = "mahmoudmohamedm18@gmail.com"; // Replace with actual admin email

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullName = $_POST['fullName'] ?? 'Not provided';
    $email = $_POST['email'] ?? 'Not provided';
    $phone = $_POST['phone'] ?? 'Not provided';
    $amount = $_POST['amount'] ?? 'Not provided';
    $reason = $_POST['reason'] ?? 'Not provided';
    $description = $_POST['description'] ?? 'Not provided';
    
    
    // Create email subject
    $subject = "New Financial Aid Request: $fullName";
    
    // Create email body
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
    
    // Set email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: CharityHub <noreply@charityhub.org>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    
    // Send email to admin
    $emailSent = mail($admin_email, $subject, $emailBody, $headers);
    
    // Send confirmation email to applicant
    $userSubject = "Your Financial Aid Request - CharityHub";
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
            <p>Thank you for submitting your financial aid request to CharityHub. We have received your application for financial assistance.</p>
            
            <p><strong>Request Details:</strong></p>
            <ul>
                <li>Reason for Aid: $reason</li>
                <li>Amount Requested: $$amount</li>
            </ul>
            
            <p>Our team will review your request and contact you shortly via email or phone. If you have any questions, please don't hesitate to contact us.</p>
            
            <p>Warm regards,<br>
            The CharityHub Team</p>
        </div>
    </body>
    </html>
    ";
    
    // Send confirmation email to user
    $userEmailSent = mail($email, $userSubject, $userMessage, $headers);
    
    // Return JSON response
    header('Content-Type: application/json');
    
    if ($emailSent) {
        echo json_encode(['success' => true, 'message' => 'Your request has been submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'There was an error sending your request. Please try again.']);
    }
} else {
    // Not a POST request
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>