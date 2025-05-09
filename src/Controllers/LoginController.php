<?php
// LoginController.php - Handles login logic

require_once 'Modules/UserLogin.php';

class LoginController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handleLogin($postData) {
        $email = $postData['email'];
        $password = $postData['password'];
        $role = $postData['role'];

        $userLogin = new UserLogin($this->conn, $email, $password, $role);
        return $userLogin->login();
    }
}