<?php
require_once '../models/Event.php';

class EventController {
    private $eventModel;

    public function __construct($db) {
        $this->eventModel = new Event($db);
    }

    public function displayEventsPage() {
        $months = $this->eventModel->getAllMonths();
        require_once '../views/events.php';
    }

    public function displayEventsByMonth($month) {
        $events = $this->eventModel->getEventsByMonth($month);
        require_once '../views/events.php';
    }
}
?>
