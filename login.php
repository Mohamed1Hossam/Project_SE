<?php 
// login.php - User Login Handler

// Make sure these are set properly before session_start
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

session_start();  // Make sure session is started at the beginning
session_regenerate_id(true); // Prevent session fixation attacks
require_once 'config.php';      // Include the database connection
require_once 'UserLogin.php';   // Include the UserLogin class

// Debug information - comment out when fixed
function debug_to_file($data) {
    $file = fopen("login_debug.log", "a");
    fwrite($file, date("Y-m-d H:i:s") . ": " . print_r($data, true) . "\n");
    fclose($file);
}

// Clear any previous errors
if (isset($_SESSION['login_errors'])) {
    unset($_SESSION['login_errors']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get email, password, and role from the form
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];  // 'admin' or 'user'
    
    // Debug info
    debug_to_file("Login attempt: Email: $email, Role: $role");

    // Instantiate the UserLogin class with role
    $userLogin = new UserLogin($conn, $email, $password, $role);

    // Call the login method to attempt login
    $errors = $userLogin->login();

    // If there are errors, set them in the session and redirect
    if (!empty($errors)) {
        debug_to_file("Login errors: " . print_r($errors, true));
        $_SESSION['login_errors'] = $errors;
        $_SESSION['form_data'] = ['email' => $email];
        header("location: login.php");
        exit;
    }
    // Successful login handled inside UserLogin class
} else {
    // Display any errors from previous login attempts
    if (isset($_SESSION['login_errors'])) {
        $errors = $_SESSION['login_errors'];
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
        
        <!-- Display errors if any -->
        <?php if(isset($errors) && !empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form id="loginForm" action="login.php" method="POST">
            <div class="role-selection">
                <label><input type="radio" name="role" value="admin" required> Admin</label>
                <label><input type="radio" name="role" value="user" required> User</label>
            </div>
            
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
            Don't have an account? <a href="register.php" class="signup-link">Sign up</a>
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