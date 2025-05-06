<?php
// Include the Event class and the DB connection
require 'Event.php';
require 'db.php';

// Check if event ID is provided in the POST request
if (isset($_POST['id']) && !empty($_POST['id'])) {
    // Instantiate the Event class
    $eventObj = new Event($pdo);

    // Get the event ID from the POST request
    $eventId = $_POST['id'];

    // Try to delete the event
    if ($eventObj->deleteEventById($eventId)) {
        // Redirect with a success message
        header("Location: manage_events.php?deleted=1");
    } else {
        // Redirect with an error message if something goes wrong
        header("Location: manage_events.php?error=1");
    }
    exit;  // Stop further script execution after the redirect
} else {
    // If no ID is provided, redirect with an error
    header("Location: manage_events.php?error=1");
    exit;
}
?>
