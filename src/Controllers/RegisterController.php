<?php
// RegisterController.php - Handles registration logic

require_once 'Modules/UserRegistration.php';

class RegisterController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handleRegistration($postData) {
        $first_name = $postData['firstName'];
        $last_name = $postData['lastName'];
        $email = $postData['email'];
        $password = $postData['password'];
        $confirm_password = $postData['confirmPassword'];
        $phone_number = $postData['phone'];
        $is_donor = 1;
        $is_volunteer = isset($postData['is_volunteer']) ? 1 : 0;

        $registration = new UserRegistration(
            $this->conn,
            $first_name,
            $last_name,
            $email,
            $password,
            $confirm_password,
            $phone_number,
            $is_donor,
            $is_volunteer
        );

        return $registration->register();
    }
}