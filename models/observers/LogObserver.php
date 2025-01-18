<?php
// models/observers/LogObserver.php

require_once __DIR__ . '/../Observer.php';

class LogObserver implements Observer {
    public function update($data) {
        error_log("User {$data['userId']} donated {$data['amountOrItem']}.\n");
    }
}
