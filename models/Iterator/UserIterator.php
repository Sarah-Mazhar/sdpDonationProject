<?php
require_once 'IteratorInterface.php';

class UserIterator implements IteratorInterface {
    
    private $users = [];
    private $index = 0;

    public function __construct(array $users) {
        $this->users = $users;
    }

    public function hasNext() {
        return $this->index < count($this->users);
    }

    public function next() {
        if ($this->hasNext()) {
            return $this->users[$this->index++];
        }
        return null;
    }

    public function reset() {
        $this->index = 0;
    }
}
?>