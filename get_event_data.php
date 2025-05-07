<?php
include 'connect.php';

// Handle JSON request (e.g., for fetch)
if (isset($_GET['json'])) {
    header('Content-Type: application/json');

    $campaignId = isset($_GET['campaign_id']) ? intval($_GET['campaign_id']) : null;

    if ($campaignId) {
        $stmt = $conn->prepare("SELECT event_id AS id, name, description, campaign_id FROM event WHERE campaign_id = ?");
        $stmt->bind_param("i", $campaignId);
    } else {
        $stmt = $conn->prepare("SELECT event_id AS id, name, description, campaign_id FROM event");
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    echo json_encode($events);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="Script.js"></script>
  <link rel="stylesheet" href="Style.css" />
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
  <title>Event Page</title>
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
    <nav class="nav-links">
      <a href="campaign_page.html"><div class="icon-globe"></div><span>Campaigns</span></a>
      <a href="#"><div class="icon-calculator"></div><span>Calculate Zakat</span></a>
      <a href="#"><div class="icon-landmark"></div><span>Financial Aid</span></a>
      <a href="#"><div class="icon-user"></div><span>Profile</span></a>
    </nav>
  </div>
</header>

<main>
  <section>
    <div class="event-table-container">
      <table id="eventTable" class="event-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Campaign ID</th>
          </tr>
        </thead>
        <tbody id="eventTableBody"></tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>
