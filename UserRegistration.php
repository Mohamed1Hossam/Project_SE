<?php
class UserRegistration {
    private $conn;
    private $first_name;
    private $last_name;
    private $email;
    private $password;
    private $confirm_password;
    private $phone_number;
    private $is_donor;
    private $is_volunteer;
    private $errors = [];

    public function __construct($conn, $first_name, $last_name, $email, $password, $confirm_password, $phone_number, $is_donor = false, $is_volunteer = false) {
        $this->conn = $conn;
        $this->first_name = trim($first_name);
        $this->last_name = trim($last_name);
        $this->email = trim($email);
        $this->password = $password;
        $this->confirm_password = $confirm_password;
        $this->phone_number = trim($phone_number);
        $this->is_donor = $is_donor;
        $this->is_volunteer = $is_volunteer;
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
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format.";
        }
        if (empty($this->password) || strlen($this->password) < 6) {
            $this->errors[] = "Password is required and must be at least 6 characters.";
        }
        if ($this->password != $this->confirm_password) {
            $this->errors[] = "Passwords do not match.";
        }
        if (empty($this->phone_number)) {
            $this->errors[] = "Phone number is required.";
        } elseif (!preg_match('/^[0-9+ ]{7,15}$/', $this->phone_number)) {
            // More flexible pattern - allows 7-15 digits, plus signs, and spaces
            $this->errors[] = "Please enter a valid phone number.";
        }

        // Debugging - Optional
        error_log("Validation complete. Errors: " . (empty($this->errors) ? "None" : implode(", ", $this->errors)));

        // If there are no validation errors, proceed with registration
        if (empty($this->errors)) {
            // Check if email already exists
            $sql = "SELECT user_id FROM User WHERE email = ?";
            if ($stmt = $this->conn->prepare($sql)) {
                $stmt->bind_param("s", $this->email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $this->errors[] = "This email is already registered.";
                    return $this->errors;
                }
                $stmt->close();
            } else {
                $this->errors[] = "Database error: " . $this->conn->error;
                return $this->errors;
            }

            // Hash the password and insert into the database
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

            // Insert new user into the User table
            $sql = "INSERT INTO User (email, password, first_name, last_name, phone_number, is_donor, is_volunteer) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            if ($stmt = $this->conn->prepare($sql)) {
                // Convert booleans to integers (0 or 1) for database
                $is_donor_int = $this->is_donor ? 1 : 0;
                $is_volunteer_int = $this->is_volunteer ? 1 : 0;
                
                // Bind parameters
                $stmt->bind_param("sssssii", 
                    $this->email, 
                    $hashed_password, 
                    $this->first_name, 
                    $this->last_name, 
                    $this->phone_number, 
                    $is_donor_int, 
                    $is_volunteer_int
                );
                
                // Debugging - Optional
                error_log("Attempting to insert user: {$this->email}");
                
                if ($stmt->execute()) {
                    error_log("User registered successfully: {$this->email}");
                    return []; // No errors, registration successful
                } else {
                    $this->errors[] = "Database error during registration: " . $stmt->error;
                    error_log("Registration error: " . $stmt->error);
                }
                $stmt->close();
            } else {
                $this->errors[] = "Database prepare error: " . $this->conn->error;
                error_log("Database prepare error: " . $this->conn->error);
            }
        }

        return $this->errors;
    }
}
?>