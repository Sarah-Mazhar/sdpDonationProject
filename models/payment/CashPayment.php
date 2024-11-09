<?php
// Payment/CashPayment.php
require_once __DIR__ .'/PaymentStrategy.php';
class CashPayment implements PaymentStrategy {
    public function pay($amount) {
        // echo "Paid $amount using Cash.";
        return [
            'status'=> true,
            'message'=> "Paid $amount using Cash."
        ];
    }
}
