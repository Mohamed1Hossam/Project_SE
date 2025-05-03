<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM event WHERE event_id = ?");
    $stmt->execute([$_POST['id']]);
}

header("Location: manage_events.php?deleted=1");
exit;
