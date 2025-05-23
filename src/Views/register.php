<?php 
// register.php - User Registration View

session_start();
require_once '../config.php';
require_once '../controllers/RegisterController.php';

$registerController = new RegisterController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $registerController->handleRegistration($_POST);

    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['form_data'] = [
            'firstName' => $_POST['firstName'],
            'lastName' => $_POST['lastName'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        ];
        header('Location: register.php');
        exit;
    } else {
        $_SESSION['register_success'] = 'Registration successful! Please log in.';
        header('Location: login.php');
        exit;
    }
}

$errors = $_SESSION['register_errors'] ?? [];
unset($_SESSION['register_errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CharityHub - Register</title>
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body, html {
      height: 100%;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f3f4f6;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .register-section {
      width: 50%;
      padding: 40px 60px;
      display: flex;
      flex-direction: column;
      background-color: #ffffff;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
      height: 100vh;
      overflow-y: auto;
    }

    .hero-section {
      width: 50%;
      overflow: hidden;
    }

    .hero-section img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .content-wrapper {
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    
    .logo-container {
      display: flex;
      align-items: center;
      margin-bottom: 36px;
    }

    .logo-circle {
    width: 48px;
    height: 48px;
    background-color: #e6f7f5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.logo-icon {
    color: #0d9488;
    font-size: 1.5rem;
}
    

    .logo-text h1 {
      font-size: 28px;
      color: #222;
    }

    .logo-text span {
      color: #1b9c85;
    }

    h2 {
      font-size: 32px;
      margin-bottom: 12px;
      color: #333;
    }

    p.subtext {
      color: #666;
      margin-bottom: 32px;
      font-size: 16px;
    }

    .form-group {
      margin-bottom: 24px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
      color: #444;
      font-size: 16px;
    }

    .input-container {
      background-color: #f0f0f0;
      padding: 14px 16px;
      border-radius: 6px;
      border: 1px solid #ddd;
      display: flex;
      align-items: center;
    }

    .input-container input {
      width: 100%;
      border: none;
      background: transparent;
      font-size: 16px;
      outline: none;
    }

    .btn-primary {
      background-color: #1b9c85;
      color: #fff;
      border: none;
      padding: 16px;
      width: 100%;
      height: 52px;
      font-size: 18px;
      font-weight: 600;
      border-radius: 8px;
      margin-top: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-primary:hover {
      background-color: #158e77;
    }

    .login-prompt {
      margin-top: 32px;
      font-size: 16px;
      color: #666;
      text-align: center;
      margin-bottom: 0;
    }

    .login-link {
      color: #1b9c85;
      text-decoration: none;
      font-weight: 600;
    }

    .password-toggle {
      background: none;
      border: none;
      cursor: pointer;
      color: #666;
      margin-left: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .password-toggle:hover {
      color: #1b9c85;
    }

    .material-icons {
      font-size: 22px;
    }

    @media (max-width: 960px) {
      .container {
        flex-direction: column;
      }

      .register-section, .hero-section {
        width: 100%;
      }

      .hero-section img {
        height: 250px;
      }

      .register-section {
        padding: 30px;
        height: auto;
        min-height: 100vh;
      }
      
      .form-group {
        margin-bottom: 20px;
      }
      
      .input-container {
        padding: 12px 14px;
      }
      
      h2 {
        font-size: 28px;
      }
      
      .btn-primary {
        height: 48px;
        font-size: 16px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="register-section">
    <div class="content-wrapper">
      <div class="logo-container">
        <div class="logo-circle">
          <div class="icon-heart"></div>
        </div>
        <div class="logo-text">
          <h1>Children of the <span>Land</span></h1>
        </div>
      </div>

      <h2>Create Your Account</h2>
      <p class="subtext">Join us in making a difference</p>

      <form action="register.php" method="POST">
      <div class="form-group">
        <label for="firstName">First Name</label>
        <div class="input-container">
          <input type="text" id="firstName" name="firstName" required placeholder="Your first name">
        </div>
      </div>

      <div class="form-group">
        <label for="lastName">Last Name</label>
        <div class="input-container">
          <input type="text" id="lastName" name="lastName" required placeholder="Your last name">
        </div>
      </div>

      <div class="form-group">
        <label for="phone">Phone Number</label>
        <div class="input-container">
          <input type="tel" id="phone" name="phone" required placeholder="0 123 456 7890" pattern="[0-9+ ]+" title="Only numbers, spaces, and + are allowed">
        </div>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <div class="input-container">
          <input type="email" id="email" name="email" required placeholder="your@email.com">
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <div class="input-container">
          <input type="password" id="password" name="password" required placeholder="Create a password">
          <button type="button" class="password-toggle" onclick="togglePassword('password')">
            <span class="material-icons">visibility_off</span>
          </button>
        </div>
      </div>

      <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <div class="input-container">
          <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Repeat your password">
          <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
            <span class="material-icons">visibility_off</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary">Register</button>
    </form>

    <p class="login-prompt">
      Already have an account? <a href="login.html" class="login-link">Sign in</a>
    </p>
    </div>
  </div>

  <div class="hero-section">
    <img src="assets/Login.jpeg" alt="Charity image">
  </div>
</div>

<script>
  function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleButton = passwordInput.nextElementSibling;
    const toggleIcon = toggleButton.querySelector('.material-icons');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleIcon.textContent = 'visibility';
    } else {
      passwordInput.type = 'password';
      toggleIcon.textContent = 'visibility_off';
    }
  }
</script>

</body>
</html>