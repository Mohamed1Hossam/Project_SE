<?php
// connect.php is included here; make sure the file exists and $conn is defined there
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="Script.js"></script>
    <link rel="stylesheet" href="charity_project/bootstrap.min.css">
    <link rel="stylesheet" href="Style.css" />
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
        <?php
        $sql = "SELECT campaign_id as id, name, description, target_amount FROM campaign";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['target_amount']) . "</td>";
                echo "<td><a href='get_campaign_details.php?campaign_id=" . htmlspecialchars($row['id']) . "' class='btn btn-primary'>View Details</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No campaigns found.</td></tr>";
        }
        ?>
    </tbody>
</table>

            </div>
        </section>
    </main>

</body>

</html>
