<?php
require_once 'config.php';
require_once 'Classes/Donation.php';

$donation = new Donation($conn);
$donationList = $donation->getAllDonationsWithDetails();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Donations</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
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
        .donation-table-wrapper {
            margin-left: auto;
            margin-right: auto;
            min-height: 100vh;
            padding-top: 150px;
            background-color: #fff;
        }

        .donation-table {
            width: 100%;
            max-width: 900px;
            border-collapse: collapse;
            background-color: #f9f9f9;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .donation-table th, .donation-table td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .donation-table th {
            background-color: #2c3e50;
            color: white;
        }

        .donation-table tr:hover {
            background-color: #f1f1f1;
        }

        header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .container {
            max-width: 1200px;
            margin: auto;
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
          <a href="Profile/profile.php">
          <div class="icon-user"></div>
            Profile
          </a>
        </div>
      </nav>
    </div>
</header>

<div class="donation-table-wrapper">
    <table class="donation-table">
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Campaign</th>
                <th>Amount</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($donationList)): ?>
                <?php foreach ($donationList as $donation): ?>
                    <tr>
                        <td><?= htmlspecialchars($donation['first_name'] . ' ' . $donation['last_name']) ?></td>
                        <td><?= htmlspecialchars($donation['campaign_name']) ?></td>
                        <td><?= htmlspecialchars($donation['amount']) ?> EGP</td>
                        <td><?= htmlspecialchars($donation['payment_method']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No donations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
