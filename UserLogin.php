<?php
class UserLogin {
    private $conn;
    private $email;
    private $password;
    private $errors = [];

    public function __construct($conn, $email, $password) {
        $this->conn = $conn;
        $this->email = $email;
        $this->password = $password;
    }

    public function login() {
        $sql = "SELECT user_id, email, password, first_name, is_donor, is_volunteer FROM User WHERE email = ?";

        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("s", $this->email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Declare variables before using in bind_result
                $user_id = $email = $stored_password = $first_name = "";
                $is_donor = $is_volunteer = 0;

                $stmt->bind_result($user_id, $email, $stored_password, $first_name, $is_donor, $is_volunteer);
                if ($stmt->fetch()) {
                    if (password_verify($this->password, $stored_password)) {
                        session_start();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["user_id"] = $user_id;
                        $_SESSION["email"] = $email;
                        $_SESSION["first_name"] = $first_name;
                        $_SESSION["is_donor"] = $is_donor;
                        $_SESSION["is_volunteer"] = $is_volunteer;
                        header("location: HomePage.html");
                        exit;
                    } else {
                        $this->errors[] = "Incorrect password.";
                    }
                }
            } else {
                $this->errors[] = "No account found with that email address.";
            }
        }

        return $this->errors;
    }
}
?>
