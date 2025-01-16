<?php
require_once '../models/Event.php';

class EventController {
    private $eventModel;

    public function __construct($db) {
        $this->eventModel = new Event($db);
    }

    public function displayEventsPage() {
        // Fetch months for the dropdown menu
        $months = $this->eventModel->getAllMonths();
        require_once '../views/events.php';  // This will be the view file for rendering
    }

    public function displayEventsByMonth($month) {
        $events = $this->eventModel->getEventsByMonth($month);
        require_once '../views/events.php';  // This will be the same view, just filtered
    }
}
?>
