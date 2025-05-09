<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../../config.php';

$events = [];
$errorMessage = '';

// Handle JSON response if requested via fetch
if (isset($_GET['json'])) {
    header('Content-Type: application/json');

    // Get the campaign_id if provided in the query string
    $campaignId = isset($_GET['campaign_id']) ? intval($_GET['campaign_id']) : null;

    if ($campaignId) {
        // Fetch events related to the specific campaign_id
        $stmt = $conn->prepare("SELECT event_id AS id, name, description, campaign_id FROM event WHERE campaign_id = ?");
        $stmt->bind_param("i", $campaignId);
    } else {
        // Fetch all events if no campaign_id is provided
        $stmt = $conn->prepare("SELECT event_id AS id, name, description, campaign_id FROM event");
    }

    // Execute the query and retrieve the results
    $stmt->execute();
    $result = $stmt->get_result();

    // Store the events in the $events array
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    // Return the events data in JSON format
    echo json_encode(['success' => true, 'events' => $events]);
    exit;
}

// Otherwise, load for HTML page rendering
$sql = "SELECT event_id as id, name, description, campaign_id FROM event";
$result = $conn->query($sql);

// Check if there are any events
if ($result && $result->num_rows > 0) {
    // Fetch each event and add it to the $events array
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} else {
    // If no events are found, set the error message
    $errorMessage = 'No events found.';
}
?>