<?php
require_once 'PaymentAdminInterface.php';

class RealPaymentAdmin implements PaymentAdminInterface {
    public function viewPayments() {
        echo "Displaying all payments.";
    }

    public function processRefund(int $paymentId) {
        echo "Processing refund for payment ID $paymentId.";
    }
}
