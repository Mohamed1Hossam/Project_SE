<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Event Details</title>
  <link rel="stylesheet" href="../charity_project/bootstrap.min.css">
  <link rel="stylesheet" href="../Style.css"/>
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css"/>
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
      <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search...">
        <button onclick="handleSearch()">🔍</button>
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

<main class="event-details-container">
  <section class="event-section card">
    <h2>Event Name</h2>
    <strong><p id="eventName"></p></strong>
  </section>

  <section class="event-section card">
    <h2>Description</h2>
    <strong><p id="eventDescription"></p></strong>
  </section>

  <section class="event-section card">
    <h2>Location</h2>
    <strong><p id="eventLocation"></p></strong>
  </section>

  <section class="event-section card">
    <h2>Date</h2>
    <strong><p id="eventDate"></p></strong>
  </section>

  <section class="event-section card">
    <h2>Time</h2>
    <strong><p id="eventTime"></p></strong>
  </section>

  <section class="event-section card">
    <h2>Campaign ID</h2>
    <strong><p id="campaignId"></p></strong>
  </section>

  <div style="text-align:center; margin: 20px 0;">
    <button class="detailsButton" id="goToCampaign">Go to Campaign Details</button>
    <button class="detailsButton" id="donateNow">Donate Now</button>
  </div>
</main>

<script>
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');

  fetch(`../Controllers/event_details.php?id=${id}`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data) {
        const e = data.data;
        document.getElementById('eventName').textContent = e.name;
        document.getElementById('eventDescription').textContent = e.description;
        document.getElementById('eventLocation').textContent = e.location;
        document.getElementById('eventDate').textContent = e.date.split(' ')[0];
        document.getElementById('eventTime').textContent = e.date.split(' ')[1];
        document.getElementById('campaignId').textContent = e.campaign_id;

        document.getElementById('goToCampaign').onclick = () =>
          window.location.href = `get_campaign_details.php?campaign_id=${e.campaign_id}`;
        document.getElementById('donateNow').onclick = () =>
          window.location.href = `process_donation.php?campaign_id=${e.campaign_id}`;
      } else {
        document.querySelector('main').innerHTML = `<p style="padding: 20px; color: red;">${data.error}</p>`;
      }
    })
    .catch(error => {
      document.querySelector('main').innerHTML = `<p style="padding: 20px; color: red;">Error: ${error}</p>`;
    });
</script>

</body>
</html>
