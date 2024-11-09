<?php
// models/observers/NotificationObserver.php

require_once __DIR__ . '/../Observer.php';

class NotificationObserver implements Observer {
    public function update($data) {
        // Notify admins about the new donation
        echo "Notifying admin: User {$data['userId']} donated {$data['amountOrItem']}.\n";
    }
}
