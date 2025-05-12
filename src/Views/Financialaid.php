<?php
require_once __DIR__ . '/../Controllers/FinancialAidController.php';


if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if($_SESSION['role'] != 'user') {
  header('location: Views/login.php');
  exit;
}


$controller = new FinancialAidController();
$formSubmitted = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->submitRequest();
    if ($result) {
        $formSubmitted = $result['formSubmitted'];
        $errorMessage = $result['errorMessage'];
    }
} elseif (isset($_SESSION['form_result'])) {
    $formSubmitted = $_SESSION['form_result']['formSubmitted'];
    $errorMessage = $_SESSION['form_result']['errorMessage'];
    unset($_SESSION['form_result']); // Clear the session data after using it
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
  <title>Children of the Land - Financial Aid Request</title>
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
    
    .search-container {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 10px;
    }

    .search-container input[type="text"] {
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 20px 0 0 20px;
      outline: none;
      width: 200px;
      transition: width 0.4s ease-in-out;
    }

    .search-container button {
      padding: 8px 12px;
      border: 1px solid #ccc;
      background-color: #1abc9c;
      color: white;
      border-radius: 0 20px 20px 0;
      cursor: pointer;
      border-left: none;
    }

    .search-container input[type="text"]:focus {
      width: 250px;
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
        
        <div class="search-container">
          <input type="text" id="searchInput" placeholder="Search...">
          <button onclick="handleSearch()">🔍</button>
        </div>

        <div class="nav-links">            <a href="../Views/get_campaign_data.php">
            <div class="icon-globe"></div>
            <span>Campaigns</span>
          </a>
          
          <a href="../Views/get_event_data.php">
            <div class="icon-ticket-check"></div>
            <span>Events</span>
          </a>

          <a href="../EventCountDown.html">
            <div class="icon-clock"></div>
            <span>Countdown</span>
          </a>

          <a href="../Views/process_donation.php">
            <div class="icon-globe"></div>
            <span>Donate Now</span>
          </a>

          <a href="../Views/zakatView.php">
            <div class="icon-calculator"></div>
            <span>Calculate Zakat</span>
          </a>

          <a href="../Views/Financialaid.php">
            <div class="icon-landmark"></div>
            <span>Financial Aid</span>
          </a>

          <a href="../Views/VolunteerPage.html">
            <div class="icon-user"></div>
            <span>Join US</span>
          </a>

          <a href="../Views/profileView.php">
            <div class="icon-user"></div>
            <span>Profile</span>
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
          <a href="Homepage.html" class="return-link">Return to Homepage</a>
        </div>
      <?php else: ?>
        <!-- Financial Aid Request Form -->
        <div class="form-container" id="form-container">
          <h2>Financial Aid Request</h2>
          <p>Please fill out the form below with your financial aid request details. Our team will review your application and contact you shortly.</p>
          
          <?php if (!empty($errorMessage)): ?>
            <div class="error-message" style="display: block; margin-bottom: 20px;">
              <?php echo htmlspecialchars($errorMessage); ?>
            </div>
          <?php endif; ?>

          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="form-group">
              <label for="fullName">Full Name *</label>
              <input type="text" id="fullName" name="fullName" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number *</label>
              <input type="tel" id="phone" name="phone" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="amount">Amount Requested (USD) *</label>
              <input type="number" id="amount" name="amount" class="form-control" min="1" step="1" required>
            </div>

            <div class="form-group">
              <label for="reason">Reason for Request *</label>
              <select id="reason" name="reason" class="form-control" required>
                <option value="">Select a reason</option>
                <option value="medical">Medical Expenses</option>
                <option value="education">Education</option>
                <option value="housing">Housing</option>
                <option value="food">Food Assistance</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <label for="description">Additional Details</label>
              <textarea id="description" name="description" class="form-control" rows="5"></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Request</button>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script>
    function handleSearch() {
      const searchTerm = document.getElementById('searchInput').value;
      // Implement search functionality here
      console.log('Searching for:', searchTerm);
    }
  </script>
</body>
</html>
