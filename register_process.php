<?php
// signup.php - Handle user registration
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values and sanitize
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last_name = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = trim(mysqli_real_escape_string($conn, $_POST['phone_number']));
    
    // Validate input
    $errors = [];
    
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    if ($password != $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($phone_number)) {
        $errors[] = "Phone number is required";
    }
    
    // Check if email already exists
    $sql = "SELECT user_id FROM User WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "This email is already registered";
            }
        }
        $stmt->close();
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert into User table - set is_donor and is_volunteer to default 0
            $sql = "INSERT INTO User (email, password, first_name, last_name, phone_number, is_donor, is_volunteer) VALUES (?, ?, ?, ?, ?, 0, 0)";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssss", $email, $hashed_password, $first_name, $last_name, $phone_number);
                $stmt->execute();
                $user_id = $stmt->insert_id;
                $stmt->close();
                
                // Commit the transaction
                $conn->commit();
                
                $_SESSION["signup_success"] = "Registration successful! Please log in.";
                
                header("location: LoginPage.html");
                exit;
            }
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>