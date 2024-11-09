<?php
// Payment/PaymentContext.php

class PaymentContext {
    private $paymentStrategy;

    // Constructor accepts a PaymentStrategy object
    public function __construct(PaymentStrategy $paymentStrategy) {
        $this->paymentStrategy = $paymentStrategy;
    }

    // This method delegates the payment processing to the strategy's pay method
    public function executePayment($amount) {
        return $this->paymentStrategy->pay($amount);
        
    }
}
