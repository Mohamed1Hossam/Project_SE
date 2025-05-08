<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if ($data === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }

    // Extract and sanitize input data
    $name = trim($data['donorName']);
    $email = trim($data['donorEmail']);
    $amount = $data['donationAmount'];
    $paymentMethod = $data['paymentMethod'];
    $donationType = $data['donationType']; // 'onetime' or 'recurring'
    $recurringFrequency = isset($data['recurringFrequency']) ? $data['recurringFrequency'] : null;

    // Validate required fields
    if (empty($name) || empty($email) || empty($amount) || empty($paymentMethod) || empty($donationType)) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields are required.']);
        exit;
    }

    if (!is_numeric($amount) || $amount <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid donation amount.']);
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert or update donor
        $stmt = $conn->prepare("INSERT INTO donor (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
        $stmt->close();

        // Retrieve donor_id
        $stmt = $conn->prepare("SELECT donor_id FROM donor WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($donor_id);
        $stmt->fetch();
        $stmt->close();

        if (!$donor_id) {
            throw new Exception('Failed to retrieve donor ID.');
        }

        // Insert into donation table
        $stmt = $conn->prepare("INSERT INTO donation (donor_id, amount, payment_method, donation_type, donation_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("idss", $donor_id, $amount, $paymentMethod, $donationType);
        $stmt->execute();
        $donation_id = $stmt->insert_id;
        $stmt->close();

        if (!$donation_id) {
            throw new Exception('Failed to insert donation.');
        }

        // Insert into specific donation type table
        if ($donationType === 'onetime') {
            $stmt = $conn->prepare("INSERT INTO onetime_donation (donation_id) VALUES (?)");
            $stmt->bind_param("i", $donation_id);
            $stmt->execute();
            $stmt->close();
        } elseif ($donationType === 'recurring') {
            if (empty($recurringFrequency)) {
                throw new Exception('Recurring frequency is required for recurring donations.');
            }
            $stmt = $conn->prepare("INSERT INTO recurring_donation (donation_id, frequency) VALUES (?, ?)");
            $stmt->bind_param("is", $donation_id, $recurringFrequency);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception('Invalid donation type.');
        }

        // Commit transaction
        $conn->commit();

        http_response_code(200);
        echo json_encode(['message' => 'Thank you for your donation, ' . htmlspecialchars($name) . '!']);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

    $conn->close();
    exit;
}
?>

<!-- HTML PART (for GET requests) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Page</title>
    <link rel="stylesheet" href="Style.css">
    <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
    <script src="Script.js" defer></script>
</head>
<body class="home-page">
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
    <div class="donation-page-container">
        <h1 class="donation-title">Donation Page</h1>
        <div id="donationFormContainer" class="donation-form-container">
            <form id="donationForm">
                <div class="form-group">
                    <label for="donorName">Name:</label>
                    <input type="text" id="donorName" name="donorName" required>
                </div>
                <div class="form-group">
                    <label for="donorEmail">Email:</label>
                    <input type="email" id="donorEmail" name="donorEmail" required>
                </div>
                <div class="form-group">
                    <label for="donationAmount">Amount to Donate(EGP):</label>
                    <input type="number" id="donationAmount" name="donationAmount" min="1" required>
                </div>

                <div class="form-group">
                    <label>Donation Type:</label>
                    <div class="donation-type-options">
                        <label><input type="radio" name="donationType" value="onetime"> One-Time</label>
                        <label><input type="radio" name="donationType" value="recurring"> Recurring</label>
                    </div>
                </div>

                <div id="recurringOptions" class="form-group hidden">
                    <label>Recurring Frequency:</label>
                    <div class="recurring-options">
                        <label><input type="radio" name="recurringFrequency" value="weekly"> Weekly</label>
                        <label><input type="radio" name="recurringFrequency" value="monthly"> Monthly</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Payment Method:</label>
                    <div class="payment-options">
                        <label><input type="radio" name="paymentMethod" value="credit_card"> Credit Card</label>
                        <label><input type="radio" name="paymentMethod" value="paypal"> PayPal</label>
                    </div>
                </div>
                <div id="creditCardFields" class="hidden">
                    <div class="form-group">
                        <label for="cardNumber">Card Number:</label>
                        <input type="text" id="cardNumber" name="cardNumber" pattern="^[0-9]{16}$" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label for="cardExpiry">Expiry Date (MM/YY):</label>
                        <input type="text" id="cardExpiry" name="cardExpiry" pattern="^(0[1-9]|1[0-2])\/[0-9]{2}$" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cardCvv">CVV:</label>
                        <input type="password" id="cardCvv" name="cardCvv" pattern="^[0-9]{3}$" maxlength="3">
                    </div>
                </div>
                <div id="paypalFields" class="hidden">
                    <div class="form-group">
                        <label for="paypalEmail">PayPal Email:</label>
                        <input type="email" id="paypalEmail" name="paypalEmail">
                    </div>
                    <div class="form-group">
                        <label for="paypalPassword">PayPal Password:</label>
                        <input type="password" id="paypalPassword" name="paypalPassword">
                    </div>
                </div>
                <button class="btnd" type="submit">Donate Now</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>