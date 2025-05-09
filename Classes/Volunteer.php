
<?php
class Volunteer {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addVolunteer($user_id, $skills) {
        $sql = "INSERT INTO Volunteer (user_id, Skills) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $skills);
        return $stmt->execute();
    }
}
