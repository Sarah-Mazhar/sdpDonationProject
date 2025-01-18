<?php

require_once __DIR__ . '/../Observer.php';

class EmailObserver implements Observer {
    public function update($data) {
        echo "Sending email to user {$data['userId']} for donation of {$data['amountOrItem']}.\n";
    }
}
