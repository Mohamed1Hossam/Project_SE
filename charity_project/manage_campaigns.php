<?php
require_once 'db.php';  // Make sure db.php is included
require_once 'Campaign.php'; // Include Campaign class

// Fetch all campaigns from the database
// $stmt = $pdo->query("SELECT * FROM campaign");
// $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
$campaignObj = new Campaign();
$campaigns = $campaignObj->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Campaigns</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap.min.css">
</head>
<body>
    <h2 class="text-center">Manage Campaigns</h2>
<div class="d-flex flex-column justify-content-center  align-items-center">
    <div class="text-left  w-75  ">
    <table class="table table-striped ">
        <thead>
            <tr>
                <th>Campaign Name</th>
                <th>Description</th>
                <th>Target Amount</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($campaigns as $campaign): ?>
                <tr>
                    <td><?php echo htmlspecialchars($campaign['name']); ?></td>
                    <td><?php echo htmlspecialchars($campaign['description']); ?></td>
                    <td><?php echo number_format($campaign['target_amount'], 2); ?></td>
                    <td><?php echo $campaign['start_date']; ?></td>
                    <td><?php echo $campaign['end_date']; ?></td>
                    <td>
                        <a href="delete_campaign.php?id=<?php echo $campaign['campaign_id']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this campaign?')">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="add_campaign.php" class="btn btn-primary">Add New Campaign</a>
    <p><a href="dashboard.php" class="btn btn-secondary my-2">Back To Dashboard</a></p>

</div>
</div>
</body>
</html>
