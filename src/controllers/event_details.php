<?php
include '../../config.php';
$eventData = null;
$error = '';

// Check if event ID is present and numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Try to get the first event ID
    $result = $conn->query("SELECT event_id FROM event LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstEventId = $row['event_id'];
        // Redirect to same page with ?id=firstEventId
        header("Location: ?id=" . $firstEventId);
        exit();
    } else {
        $error = 'No events found in the database.';
    }
} else {
    $eventId = (int) $_GET['id'];

    $stmt = $conn->prepare("SELECT event_id as id, name, description, event_date as date, location, campaign_id FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $eventData = $result->fetch_assoc();
    } else {
        $error = 'Event not found';
    }

    $stmt->close();
}

$conn->close();
?>