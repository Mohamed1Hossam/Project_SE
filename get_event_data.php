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
