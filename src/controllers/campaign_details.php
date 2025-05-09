<?php
// Show all PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include '../../config.php';

$response = [
    'success' => false,
    'message' => '',
    'campaign' => null,
    'events' => [],
];

// If no campaign_id, get the first available one
if (!isset($_GET['campaign_id'])) {
    $result = $conn->query("SELECT campaign_id FROM campaign LIMIT 1");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $campaignId = $row['campaign_id'];
    } else {
        $response['message'] = 'No campaigns found in the database.';
        echo json_encode($response);
        exit;
    }
} else {
    $campaignId = intval($_GET['campaign_id']);
}

// Get campaign details
$stmt = $conn->prepare("
    SELECT 
        campaign_id, 
        name, 
        description, 
        target_amount, 
        total_collected, 
        start_date, 
        end_date, 
        remaining_target, 
        status 
    FROM campaign 
    WHERE campaign_id = ?
");
$stmt->bind_param("i", $campaignId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $campaignData = $result->fetch_assoc();
    $response['campaign'] = $campaignData;
    $response['success'] = true;
    $response['message'] = 'Campaign data loaded.';
} else {
    $response['message'] = 'Campaign not found.';
    echo json_encode($response);
    exit;
}
$stmt->close();

// Get events for the campaign
$eventsQuery = $conn->prepare("SELECT event_id, name, event_date FROM event WHERE campaign_id = ?");
$eventsQuery->bind_param("i", $campaignId);
$eventsQuery->execute();
$eventsResult = $eventsQuery->get_result();
$events = [];

while ($event = $eventsResult->fetch_assoc()) {
    $events[] = $event;
}
$eventsQuery->close();

$response['events'] = $events;

echo json_encode($response);
?>
