<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createVolunteerUser($email, $first_name, $last_name, $phone) {
        $sql = "INSERT INTO User (email, password, first_name, last_name, phone_number, is_volunteer)
                VALUES (?, '', ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $email, $first_name, $last_name, $phone);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
}
