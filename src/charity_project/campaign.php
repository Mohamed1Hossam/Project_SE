<?php
require_once 'db.php';

class Campaign {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getAll() {
        return $this->pdo->query("SELECT * FROM campaign")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($data) {
        $stmt = $this->pdo->prepare("INSERT INTO campaign (name, description, target_amount, start_date, end_date, admin_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['description'],
            $data['target_amount'],
            $data['start_date'],
            $data['end_date'],
            1 // Replace with actual admin ID if available
        ]);
    }

    public function delete($id) {
        // Begin a transaction to ensure atomicity
        $this->pdo->beginTransaction();


        try {
            // Step 1: Update the volunteers associated with the campaign
            $stmt = $this->pdo->prepare("UPDATE volunteer SET campaign_id = NULL WHERE campaign_id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                echo "Volunteers updated successfully.<br>";
            } else {
                echo "No volunteers were associated with this campaign.<br>";
            }

            // Step 2: Delete the campaign
            $stmt = $this->pdo->prepare("DELETE FROM campaign WHERE campaign_id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                echo "Campaign deleted successfully.<br>";
            } else {
                echo "No campaign was found with the provided ID.<br>";
            }

            // If both operations succeed, commit the transaction
            $this->pdo->commit();

            // Return true if deletion is successful
            return true;
        } catch (Exception $e) {
            // If something goes wrong, roll back the transaction
            $this->pdo->rollBack();
            echo "Error: " . $e->getMessage() . "<br>";
            return false;
        }
    }
}
?>
