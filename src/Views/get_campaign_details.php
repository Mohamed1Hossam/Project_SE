<?php
// campaign_page.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Campaign Details</title>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const campaignId = new URLSearchParams(window.location.search).get("campaign_id") || 1;

      fetch(`../Controllers/campaign_details.php?campaign_id=${campaignId}`)
        .then(response => {
          if (!response.ok) throw new Error("Network response was not ok");
          return response.json();
        })
        .then(data => {
          if (!data.success) {
            document.getElementById("error-message").textContent = data.message || "Campaign not found.";
            return;
          }

          const campaign = data.campaign;
          document.getElementById("campaign-name").textContent = campaign.name;
          document.getElementById("campaign-id").textContent = campaign.campaign_id;
          document.getElementById("campaign-description").textContent = campaign.description;
          document.getElementById("campaign-goal").textContent = campaign.target_amount;
          document.getElementById("campaign-collected").textContent = campaign.total_collected;
          document.getElementById("campaign-remaining").textContent = campaign.remaining_target;
          document.getElementById("campaign-start").textContent = campaign.start_date;
          document.getElementById("campaign-end").textContent = campaign.end_date;
          document.getElementById("campaign-status").textContent = campaign.status;

          const progress = campaign.target_amount > 0 
            ? (campaign.total_collected / campaign.target_amount) * 100 
            : 0;
          document.getElementById("progress-bar").style.width = `${progress}%`;
          document.getElementById("progress-bar").textContent = `${progress.toFixed(2)}%`;

          // Events
          const eventsList = document.getElementById("events-list");
          eventsList.innerHTML = "";
          if (data.events.length === 0) {
            eventsList.innerHTML = "<li>No events available for this campaign.</li>";
          } else {
            data.events.forEach(event => {
              const li = document.createElement("li");
              li.innerHTML = `<strong><a href="get_event_details.php?id=${encodeURIComponent(event.event_id)}">${event.name}</a></strong> (${event.event_date})`;
              eventsList.appendChild(li);
            });
          }

          document.getElementById("donate-btn").href = `process_donation.php?campaign_id=${campaign.campaign_id}`;
        })
        .catch(err => {
          console.error(err);
          document.getElementById("error-message").textContent = "Failed to load campaign data.";
        });
    });
  </script>
  <link rel="stylesheet" href="../charity_project/bootstrap.min.css">
  <link rel="stylesheet" href="../Style.css" />
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
  <style>
    .progress-bar-container {
      background: #eee;
      border-radius: 8px;
      overflow: hidden;
      height: 24px;
      margin-bottom: 1rem;
    }
    .progress-bar {
      background: #4caf50;
      color: white;
      text-align: center;
      height: 100%;
      transition: width 0.4s ease;
    }
  </style>
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
    <nav class="nav-links">
      <a href="get_campaign_data.php">
        <div class="icon-globe"></div>
        <span>Campaigns</span>
      </a>
      <a href="zakatView.php">
        <div class="icon-calculator"></div>
        <span>Calculate Zakat</span>
      </a>
      <a href="Financialaid.php">
        <div class="icon-landmark"></div>
        <span>Financial Aid</span>
      </a>
      <a href="profileView.php">
        <div class="icon-user"></div>
        <span>Profile</span>
      </a>
    </nav>
  </div>
</header>


  <main class="campaign-details-container">
    <p id="error-message" style="color: red;"></p>

    <div class="campaign-section card">
      <h2 id="campaign-name">Loading...</h2>
      <p><strong>Campaign ID:</strong> <span id="campaign-id"></span></p>
      <p><strong>Description:</strong> <span id="campaign-description"></span></p>
      <p><strong>Goal:</strong> <span id="campaign-goal"></span></p>
      <p><strong>Total Collected:</strong> <span id="campaign-collected"></span></p>
      <p><strong>Remaining Target:</strong> <span id="campaign-remaining"></span></p>
      <p><strong>Progress:</strong></p>
      <div class="progress-bar-container">
        <div id="progress-bar" class="progress-bar">0%</div>
      </div>
      <p><strong>Start Date:</strong> <span id="campaign-start"></span></p>
      <p><strong>End Date:</strong> <span id="campaign-end"></span></p>
      <p><strong>Status:</strong> <span id="campaign-status"></span></p>
      <div style="text-align: center; margin: 20px 0;">
        <a class="btn btn-primary" id="donate-btn" href="#">Donate Now</a>
      </div>
    </div>

    <div class="campaign-section card">
      <h3>Events</h3>
      <ul id="events-list" style="margin-left: 20px;">
        <li>Loading events...</li>
      </ul>
    </div>
  </main>
</body>
</html>
