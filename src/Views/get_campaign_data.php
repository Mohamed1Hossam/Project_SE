<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="../Script.js" defer></script>
  <link rel="stylesheet" href="../../charity_project/bootstrap.min.css">
  <link rel="stylesheet" href="../Style.css" />
  <link rel="stylesheet" href="https://unpkg.com/lucide-static/font/Lucide.css" />
  <title>Campaigns</title>
</head>

<body class="home-page">
  <!-- Header/Navigation -->
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
        <a href="campaigns.html">
          <div class="icon-globe"></div>
          <span>Campaigns</span>
        </a>
        <a href="zakat-calculator.php">
          <div class="icon-calculator"></div>
          <span>Calculate Zakat</span>
        </a>
        <a href="financial_aid.php">
          <div class="icon-landmark"></div>
          <span>Financial Aid</span>
        </a>
        <a href="Profile/profile.php">
          <div class="icon-user"></div>
          <span>Profile</span>
        </a>
      </nav>
    </div>
  </header>

  <main>
    <section>
      <div class="event-table-container">
        <table id="campaignTable" class="event-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Target Amount</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="campaignTableBody">
            <!-- Data will be populated here by JavaScript -->
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    async function loadCampaigns() {
      try {
        const response = await fetch('../controllers/campaign_data.php');
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }

        const campaigns = await response.json();
        const tbody = document.getElementById('campaignTableBody');
        tbody.innerHTML = '';

        if (campaigns.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5">No campaigns found.</td></tr>';
          return;
        }

        campaigns.forEach(campaign => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${campaign.id}</td>
            <td>${campaign.name}</td>
            <td>${campaign.description}</td>
            <td>${campaign.target_amount}</td>
            <td><a href="get_campaign_details.php?campaign_id=${campaign.id}" class="btn btn-primary">View Details</a></td>
          `;
          tbody.appendChild(row);
        });
      } catch (error) {
        console.error('Failed to load campaigns:', error);
        document.getElementById('campaignTableBody').innerHTML =
          '<tr><td colspan="5">Error loading campaigns.</td></tr>';
      }
    }

    window.addEventListener('DOMContentLoaded', loadCampaigns);
  </script>
</body>

</html>
