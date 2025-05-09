<?php 
class Campaign {
    private $pdo;

    public function __construct($pdo = null) {
        if ($pdo === null) {
            require_once 'db.php';
            $this->pdo = Database::connect();
        } else {
            $this->pdo = $pdo;
        }
    }

    // Model methods for data access
    public function getAll() {
        return $this->pdo->query("SELECT * FROM campaign")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM campaign WHERE campaign_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO campaign (name, description, target_amount, start_date, end_date, admin_id, status) 
             VALUES (?, ?, ?, ?, ?, ?, 'active')"
        );
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['target_amount'],
            $data['start_date'],
            $data['end_date'],
            $data['admin_id'] ?? 1
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare(
            "UPDATE campaign 
             SET name = ?, description = ?, target_amount = ?, 
                 start_date = ?, end_date = ?, status = ? 
             WHERE campaign_id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['target_amount'],
            $data['start_date'],
            $data['end_date'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $this->pdo->beginTransaction();
        try {
            // Update volunteers to remove campaign association
            $stmt = $this->pdo->prepare("UPDATE volunteer SET campaign_id = NULL WHERE campaign_id = ?");
            $stmt->execute([$id]);

            // Delete the campaign
            $stmt = $this->pdo->prepare("DELETE FROM campaign WHERE campaign_id = ?");
            $stmt->execute([$id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function getTotalDonations($campaignId) {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(amount), 0) as total 
             FROM donation d 
             WHERE d.campaign_id = ?"
        );
        $stmt->execute([$campaignId]);
        return $stmt->fetchColumn();
    }

    public function updateCollectedAmount($campaignId, $amount) {
        $stmt = $this->pdo->prepare(
            "UPDATE campaign 
             SET total_collected = total_collected + ?,
                 remaining_target = target_amount - (total_collected + ?)
             WHERE campaign_id = ?"
        );
        return $stmt->execute([$amount, $amount, $campaignId]);
    }
}
?>
