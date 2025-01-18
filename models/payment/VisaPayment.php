<?php

require_once __DIR__ .'/PaymentStrategy.php';

class VisaPayment implements PaymentStrategy {
    public function pay($amount) {
        return [
            'status'=> true,
            'message'=> "Paid $amount using Visa card."
        ];
    
    }
}
