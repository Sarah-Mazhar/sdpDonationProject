<?php
// models/observers/LogObserver.php

require_once __DIR__ . '/../Observer.php';

class LogObserver implements Observer {
    public function update($data) {
        // Log the donation details
        error_log("User {$data['userId']} donated {$data['amountOrItem']}.\n");
    }
}
