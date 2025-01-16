<?php
class PaymentIterator implements Iterator {
    private $payments;
    private $position = 0;

    public function __construct(array $payments) {
        $this->payments = $payments;
    }

    public function current(): mixed {
        return $this->payments[$this->position];
    }

    public function key(): int {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function rewind(): void {
        $this->position = 0;
    }

    public function valid(): bool {
        return isset($this->payments[$this->position]);
    }
}
