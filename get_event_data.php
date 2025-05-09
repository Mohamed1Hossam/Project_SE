<?php
include 'config.php';

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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="Script.js"></script>
  <link rel="stylesheet" href="charity_project/bootstrap.min.css">
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
      <a href="get_campaign_data.php"><div class="icon-globe"></div><span>Campaigns</span></a>
      <a href="zakat-calculator.php"><div class="icon-calculator"></div><span>Calculate Zakat</span></a>
      <a href="financial_aid.php"><div class="icon-landmark"></div><span>Financial Aid</span></a>
      <a href="Profile/profile.php"><div class="icon-user"></div><span>Profile</span></a>
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
            <th></th>
          </tr>
        </thead>
        <tbody id="eventTableBody">
        <?php
        $sql = "SELECT event_id as id, name, description, campaign_id FROM event";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['campaign_id']) . "</td>";
                echo "<td><a href='get_event_details.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-primary'>View Details</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No events found.</td></tr>";
        }
        ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>
