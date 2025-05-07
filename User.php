<?php
class User {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "CharityManagement");
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function getProfile($userId) {
        $sql = "
            SELECT u.user_id, u.email, u.first_name, u.last_name, u.phone_number, u.is_donor, u.is_volunteer
            FROM User u
            WHERE u.user_id = ?
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $error = "Prepare failed: " . $this->conn->error;
            error_log($error);
            throw new Exception($error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            error_log("No user found for user_id: $userId");
        }
        $profile = $result->fetch_assoc();
        $stmt->close();
        return $profile;
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $stmt = $this->conn->prepare("SELECT password FROM User WHERE user_id = ?");
        if (!$stmt) {
            $error = "Prepare failed: " . $this->conn->error;
            error_log($error);
            throw new Exception($error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result) {
            return "User not found.";
        }

        if (!password_verify($currentPassword, $result['password'])) {
            return "Current password is incorrect.";
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE User SET password = ? WHERE user_id = ?");
        if (!$stmt) {
            $error = "Prepare failed: " . $this->conn->error;
            error_log($error);
            throw new Exception($error);
        }
        $stmt->bind_param("si", $hashedPassword, $userId);
        $success = $stmt->execute();
        $stmt->close();

        return $success ? true : "Failed to update password.";
    }

    public function __destruct() {
        $this->conn->close();
    }
}