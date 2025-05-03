<?php
require 'db.php';

$events = $pdo->query("
    SELECT e.*, c.name AS campaign_name 
    FROM event e 
    LEFT JOIN campaign c ON e.campaign_id = c.campaign_id
    ORDER BY e.event_date DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Event Management</h1>

    <?php if (isset($_GET['success'])): ?>
        <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Event added successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div id="success-alert" class="alert alert-warning alert-dismissible fade show" role="alert">
            🗑️ Event deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <a href="add_event.php" class="btn btn-primary mb-3">➕ Add Event</a>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Campaign</th>
            <th>Date</th>
            <th>Location</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['name']) ?></td>
                <td><?= htmlspecialchars($event['campaign_name']) ?></td>
                <td><?= htmlspecialchars($event['event_date']) ?></td>
                <td><?= htmlspecialchars($event['location']) ?></td>
                <td><?= htmlspecialchars($event['status']) ?></td>
                <td>
                    <form action="delete_event.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $event['event_id'] ?>">
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="dashboard.php" class="btn btn-secondary my-2">Back To Dashboard</a></p>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const alert = document.getElementById('success-alert');
    if (alert) {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 3000);
    }
</script>
</body>
</html>
