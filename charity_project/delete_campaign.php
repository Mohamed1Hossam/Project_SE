
<?php
include 'db.php';

try {
    // Drop the old constraint
    $pdo->exec("ALTER TABLE volunteer DROP FOREIGN KEY volunteer_ibfk_2");

    // Add the new one with ON DELETE SET NULL
    $pdo->exec("
        ALTER TABLE volunteer
        ADD CONSTRAINT fk_volunteer_campaign
        FOREIGN KEY (campaign_id) REFERENCES campaign(campaign_id)
        ON DELETE SET NULL
    ");

    echo "Foreign key updated successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM campaign WHERE campaign_id = ?");
    $stmt->execute([$_POST['id']]);
}

header("Location: manage_campaigns.php");
exit;
?>