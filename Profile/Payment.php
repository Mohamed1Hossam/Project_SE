<?php

class Payment {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "your_new_password", "charitymangement");
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    
    public function getPaymentHistory($userId) {
        $stmt = $this->conn->prepare("
            SELECT p.amount, p.payment_method, p.payment_date, p.status, c.name AS campaign_name
            FROM payment p
            JOIN donation d ON p.donation_id = d.donation_id
            JOIN donor dr ON d.donor_id = dr.donor_id
            JOIN campaign c ON d.campaign_id = c.campaign_id
            WHERE dr.user_id = ?
            ORDER BY p.payment_date DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function __destruct() {
        $this->conn->close();
    }
}