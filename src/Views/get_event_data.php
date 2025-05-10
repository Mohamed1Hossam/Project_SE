<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="../Script.js"></script>
  <link rel="stylesheet" href="../charity_project/bootstrap.min.css">
  <link rel="stylesheet" href="../Style.css" />
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
      <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search...">
        <button onclick="handleSearch()">🔍</button>
      </div>
      <nav class="nav-links">
        <a href="get_campaign_data.php"><div class="icon-globe"></div><span>Campaigns</span></a>
        <a href="zakatView.php"><div class="icon-calculator"></div><span>Calculate Zakat</span></a>
        <a href="Financialaid.php"><div class="icon-landmark"></div><span>Financial Aid</span></a>
        <a href="profileView.php"><div class="icon-user"></div><span>Profile</span></a>
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
              <th>Event ID</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="eventTableBody">
            <!-- Event data will be populated here -->
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    // Function to fetch event data from the PHP file
    function fetchEventData() {
      fetch('../Controllers/event_data.php?json=true') // Replace with your PHP file URL
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const eventTableBody = document.getElementById('eventTableBody');
            eventTableBody.innerHTML = ''; // Clear existing content

            data.events.forEach(event => {
              // Create a new row for each event
              const row = document.createElement('tr');
              row.innerHTML = `
                <td>${event.id}</td>
                <td>${event.name}</td>
                <td>${event.description}</td>
                <td>${event.campaign_id}</td>
                <td><a href="get_event_details.php?id=${event.id}" class="btn btn-primary">View Details</a></td>
              `;
              eventTableBody.appendChild(row);
            });
          } else {
            // If there was an error, show a message
            document.getElementById('eventTableBody').innerHTML = '<tr><td colspan="5">No events found.</td></tr>';
          }
        })
        .catch(error => {
          console.error('Error fetching event data:', error);
          document.getElementById('eventTableBody').innerHTML = '<tr><td colspan="5">Error loading event data.</td></tr>';
        });
    }

    // Call fetchEventData on page load
    window.onload = fetchEventData;
  </script>
</body>
</html>
