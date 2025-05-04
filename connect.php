<?php
// connect.php or config.php

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$host = "localhost";
$user = "root";
$pass = "your_new_password";
$db   = "login";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Set character encoding
$conn->set_charset("utf8mb4");
?>
