<?php
header('Content-Type: application/json');
include '../config.php';

$response = ['success' => false, 'data' => null, 'error' => ''];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $result = $conn->query("SELECT event_id FROM event LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['data'] = ['redirect_id' => $row['event_id']];
    } else {
        $response['error'] = 'No events found in the database.';
    }
} else {
    $eventId = (int) $_GET['id'];

    $stmt = $conn->prepare("SELECT event_id AS id, name, description, event_date AS date, location, campaign_id FROM event WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['success'] = true;
        $response['data'] = $result->fetch_assoc();
    } else {
        $response['error'] = 'Event not found.';
    }
}

echo json_encode($response);
?>
