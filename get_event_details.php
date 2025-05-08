<?php
include 'connect.php';
$eventData = null;
$error = '';

// Check if event ID is present and numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Try to get the first event ID
    $result = $conn->query("SELECT event_id FROM event LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstEventId = $row['event_id'];
        // Redirect to same page with ?id=firstEventId
        header("Location: ?id=" . $firstEventId);
        exit();
    } else {
        $error = 'No events found in the database.';
    }
} else {
    $eventId = (int) $_GET['id'];

    $stmt = $conn->prepare("SELECT event_id as id, name, description, event_date as date, location, campaign_id FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $eventData = $result->fetch_assoc();
    } else {
        $error = 'Event not found';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Event Details</title>
  <script src="Script.js"></script>
  <link rel="stylesheet" href="Style.css"/>
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css"/>
  <style>
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
  </style>
</head>
<body class="home-page">

<header>
  <div class="container header-content">
    <div class="logo">
      <div class="logo-circle"><div class="icon-heart"></div></div>
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

<main class="event-details-container">
  <?php if ($eventData): ?>
    <section class="event-section card">
      <h2>Event Name</h2>
      <strong><p id="eventName"><?= htmlspecialchars($eventData['name']) ?></p></strong>
    </section>

    <section class="event-section card">
      <h2>Description</h2>
      <strong><p id="eventDescription"><?= htmlspecialchars($eventData['description']) ?></p></strong>
    </section>

    <section class="event-section card">
      <h2>Location</h2>
      <strong><p id="eventLocation"><?= htmlspecialchars($eventData['location']) ?></p></strong>
    </section>

    <section class="event-section card">
      <h2>Date</h2>
      <strong><p id="eventDate"><?= htmlspecialchars(date('Y-m-d', strtotime($eventData['date']))) ?></p></strong>
    </section>

    <section class="event-section card">
      <h2>Time</h2>
      <strong><p id="eventTime"><?= htmlspecialchars(date('H:i', strtotime($eventData['date']))) ?></p></strong>
    </section>

    <section class="event-section card">
      <h2>Campaign ID</h2>
      <strong><p><?= htmlspecialchars($eventData['campaign_id']) ?></p></strong>
    </section>

    <div style="text-align:center; margin: 20px 0;">
      <button class="detailsButton" onclick="window.location.href='get_campaign_details.php?campaign_id=<?= $eventData['campaign_id'] ?>'">Go to Campaign Details</button>
      <button class="detailsButton" onclick="window.location.href='process_donation.php'">Donate Now</button>
    </div>
  <?php else: ?>
    <p style="padding: 20px; color: red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
</main>

</body>
</html>
