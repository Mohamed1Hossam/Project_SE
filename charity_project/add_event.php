<?php
require 'db.php';
require 'Event.php';

// Create an Event object
$event = new Event($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $campaign_id = $_POST['campaign_id'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $status = $_POST['status'];

    // Add the event using the Event class method
    $event->addEvent($name, $campaign_id, $location, $description, $event_date, $status);

    header("Location: manage_events.php?success=1");
    exit;
}

$campaigns = $pdo->query("SELECT campaign_id, name FROM campaign")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add Event</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Event Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Campaign</label>
            <select name="campaign_id" class="form-select" required>
                <option value="">-- Select Campaign --</option>
                <?php foreach ($campaigns as $campaign): ?>
                    <option value="<?= $campaign['campaign_id'] ?>"><?= htmlspecialchars($campaign['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Event Date</label>
            <input type="datetime-local" name="event_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Event</button>
        <a href="manage_events.php" class="btn btn-success">Manage Events</a>
        <p><a href="dashboard.php" class="btn btn-secondary my-2">Back To Dashboard</a></p>
    </form>
</div>
</body>
</html>
