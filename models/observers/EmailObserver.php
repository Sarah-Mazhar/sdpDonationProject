<?php
// models/observers/EmailObserver.php

require_once __DIR__ . '/../Observer.php';

class EmailObserver implements Observer {
    public function update($data) {
        // Send a thank-you email
        echo "Sending email to user {$data['userId']} for donation of {$data['amountOrItem']}.\n";
    }
}
