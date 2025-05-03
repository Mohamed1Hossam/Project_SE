<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Campaigns</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap.min.css">
    
</head>
<body class=" d-flex justify-content-center align-items-center">
    <div class="w-75">
<h2>Campaigns List</h2>
<p><a href="add_campaign.php" class="btn btn-primary my-3">Add New Campaign</a></p>



<table class="table table-bordered">
        <thead>
        <tr>
        <th>ID</th><th>Name</th><th>Target</th><th>Start</th><th>End</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
    $stmt = $pdo->query("SELECT * FROM campaign");
    foreach ($stmt as $row):
    ?>
            <tr>
        <td><?= $row['campaign_id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['target_amount'] ?></td>
        <td><?= $row['start_date'] ?></td>
        <td><?= $row['end_date'] ?></td>
        <td>
            <form method="post" action="delete_campaign.php" onsubmit="return confirm('Delete this campaign?');">
                <input type="hidden" name="id" value="<?= $row['campaign_id'] ?>">
                <input type="submit" class="btn btn-danger" value="Delete">
            </form>
        </td>
    </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<p><a href="dashboard.php" class="btn btn-secondary my-2">Back To Dashboard</a></p>

 </div>
     
</body>
</html>
