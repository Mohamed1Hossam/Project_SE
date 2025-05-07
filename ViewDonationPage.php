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
        <nav class="nav-links">
            <a href="#"><div class="icon-globe"></div><span>Campaigns</span></a>
            <a href="#"><div class="icon-calculator"></div><span>Calculate Zakat</span></a>
            <a href="#"><div class="icon-landmark"></div><span>Financial Aid</span></a>
            <a href="#"><div class="icon-user"></div><span>Profile</span></a>
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
