<?php
require_once 'IteratorInterface.php';

class DonationIterator implements IteratorInterface {

    private $donations = [];
    private $index;

    public function __construct($donations) {
        $this->donations = $donations;
    }

    public function hasNext() {
        return $this->index < count($this->donations);
    }

    public function next() {
        if ($this->hasNext()) {
            return $this->donations[$this->index++];
        }
        return null;
    }

    public function reset() {
        $this->index = 0;
    }
}
?>