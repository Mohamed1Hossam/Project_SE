<?php
// Show all PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

$campaignData = null;
$errorMessage = '';

// Check if campaign_id is in the URL
if (!isset($_GET['campaign_id'])) {
    // Get the first available campaign ID
    $result = $conn->query("SELECT campaign_id FROM campaign LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstCampaignId = $row['campaign_id'];
        // Redirect to the same page with campaign_id
        header("Location: ?campaign_id=" . $firstCampaignId);
        exit();
    } else {
        $errorMessage = 'No campaigns found in the database.';
    }
} else {
    $campaignId = $_GET['campaign_id'];

    // Get campaign details
    $stmt = $conn->prepare("SELECT campaign_id, name, description, target_amount, total_collected, start_date, end_date, remaining_target, status FROM campaign WHERE campaign_id = ?");
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $campaignData = $result->fetch_assoc();
    } else {
        $errorMessage = 'Campaign not found.';
    }

    // Get events for the campaign
    $eventsQuery = $conn->prepare("SELECT event_id, name, event_date FROM event WHERE campaign_id = ?");
    $eventsQuery->bind_param("i", $campaignId);
    $eventsQuery->execute();
    $eventsResult = $eventsQuery->get_result();
    $events = [];

    while ($event = $eventsResult->fetch_assoc()) {
        $events[] = $event;
    }

    $stmt->close();
    $eventsQuery->close();
}
?>

<!-- The rest of your HTML (unchanged) -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Campaign Details</title>
  <script src="Script.js"></script>
  <link rel="stylesheet" href="charity_project/bootstrap.min.css">
  <link rel="stylesheet" href="Style.css" />
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
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
      <nav class="nav-links">
        <a href="get_campaign_data.php">
          <div class="icon-globe"></div>
          <span>Campaigns</span>
        </a>
        <a href="#">
          <div class="icon-calculator"></div>
          <span>Calculate Zakat</span>
        </a>
        <a href="#">
          <div class="icon-landmark"></div>
          <span>Financial Aid</span>
        </a>
        <a href="#">
          <div class="icon-user"></div>
          <span>Profile</span>
        </a>
      </nav>
    </div>
  </header>

  <main class="campaign-details-container">
    <?php if ($campaignData): ?>
      <div class="campaign-section card">
        <h2 id="campaign-name"><?= htmlspecialchars($campaignData['name']) ?></h2>
        <p><strong>Campaign ID:</strong> <span><?= htmlspecialchars($campaignData['campaign_id']) ?></span></p>
        <p class="campaign-description"><strong>Description:</strong> <span><?= htmlspecialchars($campaignData['description']) ?></span></p>
        <p><strong>Goal:</strong> <span><?= htmlspecialchars($campaignData['target_amount']) ?></span></p>
        <p><strong>Total Collected:</strong> <span><?= htmlspecialchars($campaignData['total_collected']) ?></span></p>
        <p><strong>Remaining Target:</strong> <span><?= htmlspecialchars($campaignData['remaining_target']) ?></span></p>
        <?php
  $goalAmount = floatval(preg_replace('/[^\d.]/', '', $campaignData['target_amount']));
  $progressAmount = floatval($campaignData['total_collected']);
  $progressPercentage = $goalAmount > 0 ? ($progressAmount / $goalAmount) * 100 : 0;
?>
<p>
  <strong>Current Progress:</strong> <?= number_format($progressPercentage, 2) ?>%
</p>

       
        <div class="progress-bar" style="margin-bottom: 20px;">
          <div class="progress" id="campaign-progress" style="width: <?= $progressPercentage ?>%;">
            <?= number_format($progressPercentage, 2) ?>%
          </div>
        </div>
        <p><strong>Start Date:</strong> <span><?= htmlspecialchars($campaignData['start_date']) ?></span></p>
        <p><strong>End Date:</strong> <span><?= htmlspecialchars($campaignData['end_date']) ?></span></p>
        <p><strong>Status:</strong> <span><?= htmlspecialchars($campaignData['status']) ?></span></p>
        <div style="text-align:center; margin: 20px 0;">
          <button class="detailsButton" onclick="window.location.href='process_donation.php?campaign_id=<?= htmlspecialchars($campaignData['campaign_id']) ?>'">Donate Now</button>
        </div>
      </div>

      <!-- Display events -->
      <div class="campaign-section card">
        <h3>Events</h3>
        <ul id="events-list" style="margin-left: 20px;">
          <?php if (count($events) > 0): ?>
            <?php foreach ($events as $event): ?>
              <li><strong><a href="get_event_details.php?id=<?= urlencode($event['event_id'] ?? '') ?>"><?= htmlspecialchars($event['name']) ?></strong></a> (<?= htmlspecialchars($event['event_date']) ?>)</li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>No events available for this campaign.</li>
          <?php endif; ?>
        </ul>
      </div>
    <?php else: ?>
      <p style="padding: 20px; color: red;"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>
  </main>
</body>
</html>
