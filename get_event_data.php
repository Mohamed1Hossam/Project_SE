<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
header('Content-Type: application/json');

include 'connect.php';

$sql = "SELECT event_id as id, name, description, event_date as date, location, campaign_id FROM Event";
$result = $conn->query($sql);

$events = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

echo json_encode($events);

?>