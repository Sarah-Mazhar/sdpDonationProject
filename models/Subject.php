<?php

interface Subject {
    public function attach($observer);
    public function detach($observer);
    public function notifyObservers($data);
}
