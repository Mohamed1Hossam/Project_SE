<?php
require_once 'db.php'; // Make sure db.php is properly included to have access to PDO
require_once 'Campaign.php'; // Include the Campaign class

// Check if an ID is provided via GET (like a delete confirmation)
if (isset($_GET['id'])) {
    $campaignId = $_GET['id'];

    // Ensure the ID is a valid integer
    if (is_numeric($campaignId)) {
        // Create a new Campaign object
        $campaign = new Campaign($pdo);

        // Call delete method of the Campaign class to delete the campaign and update volunteers
        if ($campaign->delete($campaignId)) {
            // Redirect to manage_campaigns.php after successful deletion
            echo '<p class="alert alert-success">Campaign deleted successfully!</p>';
            header("Location: manage_campaigns.php");
            exit;
        } else {
            // Error message if delete fails
            echo '<p class="alert alert-danger">Failed to delete the campaign and update volunteers.</p>';
        }
    } else {
        echo '<p class="alert alert-danger">Invalid campaign ID.</p>';
    }
} else {
    // If no ID is passed, redirect to manage_campaigns.php
    echo '<p class="alert alert-danger">Campaign ID not provided.</p>';
    header("Location: manage_campaigns.php");
    exit;
}
?>
