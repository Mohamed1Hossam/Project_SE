<?php
class Donation {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllDonationsWithDetails() {
        $sql = "SELECT 
                    u.first_name, 
                    u.last_name, 
                    c.name AS campaign_name, 
                    d.payment_method, 
                    COALESCE(otd.amount, rd.amount) AS amount
                FROM Donation d
                JOIN Donor don ON d.donor_id = don.donor_id
                JOIN User u ON don.user_id = u.user_id
                JOIN Campaign c ON d.campaign_id = c.campaign_id
                LEFT JOIN OneTime_Donation otd ON d.donation_id = otd.donation_id
                LEFT JOIN Recurring_Donation rd ON d.donation_id = rd.donation_id
                ORDER BY d.donation_date DESC";

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $donations = [];
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }

        return $donations;
    }
}
