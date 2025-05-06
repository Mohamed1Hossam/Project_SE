<?php
class UserRegistration {
    private $conn;
    private $first_name;
    private $last_name;
    private $email;
    private $password;
    private $confirm_password;
    private $phone_number;
    private $errors = [];

    public function __construct($conn, $first_name, $last_name, $email, $password, $confirm_password, $phone_number) {
        $this->conn = $conn;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password = $password;
        $this->confirm_password = $confirm_password;
        $this->phone_number = $phone_number;
    }

    public function register() {
        // Validation logic
        if (empty($this->first_name)) {
            $this->errors[] = "First name is required.";
        }
        if (empty($this->last_name)) {
            $this->errors[] = "Last name is required.";
        }
        if (empty($this->email)) {
            $this->errors[] = "Email is required.";
        }
        if (empty($this->password) || strlen($this->password) < 6) {
            $this->errors[] = "Password is required and must be at least 6 characters.";
        }
        if ($this->password != $this->confirm_password) {
            $this->errors[] = "Passwords do not match.";
        }
        if (empty($this->phone_number)) {
            $this->errors[] = "Phone number is required.";
        }

        // If there are no validation errors, proceed with registration
        if (empty($this->errors)) {
            $sql = "SELECT user_id FROM User WHERE email = ?";
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("s", $this->email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $this->errors[] = "This email is already registered.";
                } else {
                    // Hash the password and insert into database
                    $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

                    $sql = "INSERT INTO User (email, password, first_name, last_name, phone_number) VALUES (?, ?, ?, ?, ?)";
                    if ($stmt = $this->conn->prepare($sql)) {
                        $stmt->bind_param("sssss", $this->email, $hashed_password, $this->first_name, $this->last_name, $this->phone_number);
                        if ($stmt->execute()) {
                            return []; // No errors, registration successful
                        } else {
                            $this->errors[] = "Something went wrong during registration.";
                        }
                    }
                }
            }
        }

        return $this->errors;
    }
}
?>
