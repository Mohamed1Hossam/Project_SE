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
        header("location: login.php");  // Exact filename with space
        exit;
    } else {
        // Login successful - redirected in the UserLogin class
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CharityHub - Login</title>
    <link rel="stylesheet" href="Style.css">
    <script src="Script.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <div class="login-container">
                <div class="logo-container">
                    <div class="logo-circle">    
                        <div class="icon-heart"></div>
                    </div>
                    <div class="logo-text">
                        <h1>Children of the <span>Land</span></h1>
                        <p>Empowering communities together</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="welcome-back">
            <h1>Welcome Back</h1>
            <p>Login to continue your charitable journey</p>
        </div>
        
        <form id="loginForm" action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-container">
                    <div class="icon-mail" id="input_icon"></div>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required 
                           value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-container">
                    <div class="icon-lock"id="input_icon"></div>
                    <input type="password" id="password" name="password" placeholder="Your password" required>
                </div>
            </div>
            
            <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn-primary">Sign in</button>
        </form>
        
        <div class="divider">
            <span>Or continue with</span>
        </div>
        
        <div class="social-login">
            <button class="social-btn" type="button" id="githubLogin">
                <div class="icon-twitter" id="social-icon"></div> Twitter
            </button>
            <button class="social-btn" type="button" id="twitterLogin">
                <div class="icon-facebook" id="social-icon"></div> Facebook
            </button>
        </div>
        
        <p class="signup-prompt">
            Don't have an account? <a href="Register page.html" class="signup-link">Sign up</a>
        </p>
    </div>
    
    <div class="hero-image">
        <div class="hero-content">
            <h1 class="hero-title">"Make a difference today for a better tomorrow"</h1>
            <p class="hero-subtitle">Join thousands making a global impact</p>
        </div>
    </div>
    
</body>
</html>