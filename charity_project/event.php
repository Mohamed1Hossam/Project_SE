<?php
class Event {
    private $pdo;
    
    // Constructor to initialize PDO instance
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Add a new event to the database
    public function addEvent($name, $campaign_id, $location, $description, $event_date, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO event (name, campaign_id, location, description, event_date, status) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $campaign_id, $location, $description, $event_date, $status]);
    }

    // Get an event by its ID
    public function getEvent($event_id) {
        $stmt = $this->pdo->prepare("SELECT e.*, c.name AS campaign_name FROM event e
                                     JOIN campaign c ON e.campaign_id = c.campaign_id
                                     WHERE e.event_id = ?");
        $stmt->execute([$event_id]);
        return $stmt->fetch();
    }

    // Get all events
    public function getAllEvents() {
        $stmt = $this->pdo->query("SELECT e.*, c.name AS campaign_name FROM event e
                                   JOIN campaign c ON e.campaign_id = c.campaign_id");
        return $stmt->fetchAll();
    }

    // Update an event
    public function updateEvent($event_id, $name, $campaign_id, $location, $description, $event_date, $status) {
        $stmt = $this->pdo->prepare("UPDATE event SET name = ?, campaign_id = ?, location = ?, description = ?, event_date = ?, status = ? WHERE event_id = ?");
        return $stmt->execute([$name, $campaign_id, $location, $description, $event_date, $status, $event_id]);
    }

    // Delete an event
    public function deleteEventById($eventId) {
        // Prepare the DELETE statement
        $stmt = $this->pdo->prepare("DELETE FROM event WHERE event_id = ?");
        
        // Execute the query with the event_id
        return $stmt->execute([$eventId]);
    }
}
?>
