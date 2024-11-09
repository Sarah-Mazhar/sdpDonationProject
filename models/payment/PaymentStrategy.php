<?php
// Payment/PaymentStrategy.php

interface PaymentStrategy {
    public function pay($amount);
}
