<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
include 'connect.php';


header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $campaignId = $_GET['id'];

    $stmt = $conn->prepare("SELECT id, name, description, goal, progress, start_date, end_date FROM campaign WHERE id = ?");
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['message' => 'Campaign not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['message' => 'Campaign ID is required']);
}
?>