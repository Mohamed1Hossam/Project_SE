<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php

include 'connect.php';
$eventId = $_GET['id'] ?? null;

if ($eventId === null) {
    echo json_encode(['error' => 'Event ID is required']);
    exit;
}

$stmt = $conn->prepare("SELECT event_id as id, name, description, event_date as date, location, campaign_id FROM event WHERE event_id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $eventData = $result->fetch_assoc();
    echo json_encode($eventData);
} else {
    echo json_encode(['error' => 'Event not found']);
}

$stmt->close(); 
$conn->close(); 
?>