<?php
// Payment/ThirdPartyPaymentGateway.php

class ThirdPartyPaymentGateway {
    public function processPayment($amount) {
        // Simulate payment processing
        return [
            'status' => true,
            'message' => "Processed payment of {$amount} via Third-Party Payment Gateway."
        ];
    }
}
?>