<?php
// // login_process.php - Handle user login
// session_start();
// require_once 'config.php';

// // Process login data when form is submitted
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     // Initialize variables
//     $email = $password = "";
//     $errors = [];
    
//     // Get and sanitize input
//     $email = trim(mysqli_real_escape_string($conn, $_POST["email"]));
//     $password = $_POST["password"];
    
//     // Validate input
//     if (empty($email)) {
//         $errors[] = "Please enter your email.";
//     }
    
//     if (empty($password)) {
//         $errors[] = "Please enter your password.";
//     }
    
//     // If no validation errors, attempt to login
//     if (empty($errors)) {
//         // Prepare a select statement
//         $sql = "SELECT user_id, email, password, first_name, is_donor, is_volunteer FROM User WHERE email = ?";
        
//         if ($stmt = $conn->prepare($sql)) {
//             // Bind variables to the prepared statement as parameters
//             $stmt->bind_param("s", $email);
            
//             // Attempt to execute the prepared statement
//             if ($stmt->execute()) {
//                 // Store result
//                 $stmt->store_result();
                
//                 // Check if email exists
//                 if ($stmt->num_rows == 1) {
//                     // Bind result variables
//                     $stmt->bind_result($user_id, $email, $hashed_password, $first_name, $is_donor, $is_volunteer);
                    
//                     if ($stmt->fetch()) {
//                         // Verify password
//                         if (password_verify($password, $hashed_password)) {
//                             // Password is correct, start a new session
//                             session_start();
                            
//                             // Store data in session variables
//                             $_SESSION["loggedin"] = true;
//                             $_SESSION["user_id"] = $user_id;
//                             $_SESSION["email"] = $email;
//                             $_SESSION["first_name"] = $first_name;
//                             $_SESSION["is_donor"] = $is_donor;
//                             $_SESSION["is_volunteer"] = $is_volunteer;
                            
//                             // Redirect user to dashboard
//                             header("location: HomePage.html");
//                             exit;
//                         } else {
//                             // Password is incorrect
//                             $errors[] = "The password you entered is not valid.";
//                         }
//                     }
//                 } else {
//                     // Email doesn't exist
//                     $errors[] = "No account found with that email address.";
//                 }
//             } else {
//                 $errors[] = "Oops! Something went wrong. Please try again later.";
//             }
            
//             // Close statement
//             $stmt->close();
//         }
//     }
    
//     // If there are errors, store them in session and redirect back
//     if (!empty($errors)) {
//         $_SESSION['login_errors'] = $errors;
//         $_SESSION['form_data'] = [
//             'email' => $email
//         ];
//         header("location: login.html");
//         exit;
//     }
    
//     // Close connection
//     $conn->close();
// }
?>



<?php
// login.php - User Login Handler

session_start();  // Make sure session is started at the beginning
require_once 'config.php';  // Include the database connection
require_once 'UserLogin.php';  // Include the UserLogin class

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email and password from the form
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Instantiate the UserLogin class, passing the necessary parameters
    $userLogin = new UserLogin($conn, $email, $password);

    // Call the login method to attempt login
    $errors = $userLogin->login();

    // If there are errors, set them in the session and redirect
    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        $_SESSION['form_data'] = ['email' => $email];
        header("location: LoginPage.html");  // Adjust URL to your actual page
        exit;
    }
}
?>
