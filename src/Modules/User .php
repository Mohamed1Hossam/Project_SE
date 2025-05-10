<?php
// User.php - Model for User data operations

class User {
    private $conn;
    private $user_id;

    public function __construct($conn, $user_id = null) {
        $this->conn = $conn;
        $this->user_id = $user_id;
    }

    public function getProfile() {
        if (!$this->user_id) {
            return null;
        }

        $query = "SELECT email, first_name, last_name, phone_number, is_donor, is_volunteer 
                  FROM User 
                  WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();

        return $profile ?: null;
    }

    public function changePassword($current_password, $new_password) {
        // Verify current password
        $query = "SELECT password FROM User WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($current_password, $user['password'])) {
            return ['error' => 'Current password is incorrect'];
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE User SET password = ? WHERE user_id = ?";
        $update_stmt = $this->conn->prepare($update_query);
        $update_stmt->bind_param("si", $hashed_password, $this->user_id);

        if ($update_stmt->execute()) {
            $update_stmt->close();
            return ['success' => 'Password changed successfully'];
        } else {
            $update_stmt->close();
            return ['error' => 'Failed to change password'];
        }
    }
}
