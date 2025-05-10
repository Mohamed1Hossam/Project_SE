<?php
class VolunteerEvent {
    private $conn;

            public function __construct() {
        $this->conn = new mysqli("localhost", "root", "your_new_password", "charitymangement");
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    // public function __construct() {
    //     $this->conn = Database::getInstance()->getConnection();
    // }

    public function getEventParticipation($userId) {
        $stmt = $this->conn->prepare("
            SELECT ve.role, e.name AS event_name, e.event_date, e.location, e.status
            FROM volunteer_event ve
            JOIN volunteer v ON ve.volunteer_id = v.volunteer_id
            JOIN event e ON ve.event_id = e.event_id
            WHERE v.user_id = ?
            ORDER BY e.event_date DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }
}
?>