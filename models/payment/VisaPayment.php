<?php
// Payment/VisaPayment.php
require_once __DIR__ .'/PaymentStrategy.php';

class VisaPayment implements PaymentStrategy {
    public function pay($amount) {
        // echo "Paid $amount using Visa card.";
        return [
            'status'=> true,
            'message'=> "Paid $amount using Visa card."
        ];
    
    }
}
