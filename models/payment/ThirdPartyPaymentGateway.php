<?php

class ThirdPartyPaymentGateway {
    public function processPayment($amount) {
        return [
            'status' => true,
            'message' => "Processed payment of {$amount} via Third-Party Payment Gateway."
        ];
    }
}
?>