<?php
// get_campaign_data.php

// Include your database connection
require_once '../../config.php'; // <-- change this to your actual path

// Set response header to JSON
header('Content-Type: application/json');

// SQL query
$sql = "SELECT campaign_id AS id, name, description, target_amount FROM campaign";
$result = $conn->query($sql);

// Prepare output
$campaigns = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $campaigns[] = $row;
    }
}

// Return JSON response
echo json_encode($campaigns);
