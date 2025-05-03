<?php include 'db.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Campaign</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap.min.css">
</head>
<body>
<h2 class="text-center ">Add New Campaign</h2>



<form method="post" class="container" action="">

  <div class="mb-3">
    <label for="campaignName" class="form-label">Campaign Name</label>
    <input class="form-control" type="text" name="name" id="campaignName" placeholder="Campaign Name" required>
  </div>

  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" placeholder="Description" class="form-control" id="description" rows="3" required></textarea>
  </div>

  <div class="mb-3">
    <label for="targetAmount" class="form-label">Target Amount</label>
    <input type="number" name="target_amount" placeholder="Target Amount" class="form-control" id="targetAmount" required>
  </div>

  <div class="mb-3">
    <label for="email" class="form-label">Email address</label>
    <input type="email" class="form-control" name="email" id="email" placeholder="example@example.com" required>
  </div>

  <div class="mb-3">
    <label for="startDate" class="form-label">Start Date</label>
    <input type="date" class="form-control" id="startDate" name="start_date" required>
  </div>

  <div class="mb-3">
    <label for="endDate" class="form-label">End Date</label>
    <input type="date" class="form-control" id="endDate" name="end_date" required>
  </div>

  <button type="submit" name="submit" class="btn btn-primary">Add Campaign</button>
  <p><a href="manage_campaigns.php" class="btn btn-success my-2">Manage Campaigns</a></p>
  <p><a href="dashboard.php" class="btn btn-secondary my-2">Back To Dashboard</a></p>
</form>




<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("INSERT INTO campaign (name, description, target_amount, start_date, end_date, admin_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['description'],
        $_POST['target_amount'],
        $_POST['start_date'],
        $_POST['end_date'],
        1 // Replace with actual logged-in admin ID
    ]);
    echo '<p class="alert  position-fixed top-0 start-10 end-10   alert-success alert-dismissible fade show mb-0 rounded-0" role="alert">✅ Campaign added successfully!👌👌👌👌</p>';
}
?>
<script src="bootstrap.bundle.min.js"></script>
</body>
</html>

