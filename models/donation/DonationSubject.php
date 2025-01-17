<?php
// models/donation/DonationSubject.php

require_once __DIR__ . '/../Subject.php';

class DonationSubject implements Subject {
    private $observers = [];

    public function attach($observer) {
        $this->observers[] = $observer;
    }

    public function detach($observer) {
        $index = array_search($observer, $this->observers);
        if ($index !== false) {
            unset($this->observers[$index]);
        }
    }

    public function notifyObservers($data) {
        // Ensure the 'amountOrItem' key exists in the $data array
        $data['amountOrItem'] = $data['amountOrItem'] ?? 'undefined';

        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }
}
