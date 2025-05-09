<?php

class UserLogin {
    private $conn;
    private $email;
    private $password;
    private $role;
    private $errors = [];

    public function __construct($conn, $email, $password, $role) {
        $this->conn = $conn;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public function login() {
        if ($this->role === 'admin') {
            $sql = "SELECT admin_id, email, password, first_name, last_name FROM admin WHERE email = ?";
        } elseif ($this->role === 'user') {
            $sql = "SELECT user_id, email, password, first_name, is_donor, is_volunteer FROM user WHERE email = ?";
        } else {
            $this->errors[] = "Invalid role selected.";
            return $this->errors;
        }

        // For debugging
        error_log("Login attempt: Email: {$this->email}, Role: {$this->role}");
        
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $this->email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                if ($this->role === 'admin') {
                    $admin_id = $email = $stored_password = $first_name = $last_name = '';
                    $stmt->bind_result($admin_id, $email, $stored_password, $first_name, $last_name);
                } else {
                    $user_id = $email = $stored_password = $first_name = '';
                    $is_donor = $is_volunteer = 0;
                    $stmt->bind_result($user_id, $email, $stored_password, $first_name, $is_donor, $is_volunteer);
                }

                if ($stmt->fetch()) {
                    // Check if the password is hashed or plain text
                    $password_correct = password_verify($this->password, $stored_password);
                    
                    // In case passwords are stored in plain text (for testing only - not secure)
                    if (!$password_correct && $this->password === $stored_password) {
                        $password_correct = true;
                    }
                    
                    if ($password_correct) {
                        // Don't start a new session here since it's already started in login.php
                        $_SESSION["loggedin"] = true;
                        $_SESSION["email"] = $email;

                        if ($this->role === 'admin') {
                            $_SESSION["admin_id"] = $admin_id;
                            $_SESSION["first_name"] = $first_name;
                            $_SESSION["last_name"] = $last_name;
                            $_SESSION["name"] = $first_name . ' ' . $last_name;
                            $_SESSION["role"] = "admin";
                            header("location: charity_project/dashboard.php"); // Original correct path
                        } else {
                            $_SESSION["user_id"] = $user_id;
                            $_SESSION["first_name"] = $first_name;
                            $_SESSION["is_donor"] = $is_donor;
                            $_SESSION["is_volunteer"] = $is_volunteer;
                            $_SESSION["role"] = "user";
                            header("location: HomePage.html");
                        }
                        exit();
                    } else {
                        error_log("Password verification failed for: {$this->email}");
                        $this->errors[] = "Incorrect password.";
                    }
                }
            } else {
                $this->errors[] = "No account found with that email address.";
            }
            $stmt->close();
        } else {
            $this->errors[] = "Database error: unable to prepare statement.";
        }

        return $this->errors;
    }
}
?>