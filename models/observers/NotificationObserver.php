<?php

require_once __DIR__ . '/../Observer.php';

class NotificationObserver implements Observer {
    public function update($data) {
        echo "Notifying admin: User {$data['userId']} donated {$data['amountOrItem']}.\n";
    }
}
