<?php
require_once '../models/payment/ProtectivePaymentProxy.php';

class PaymentManager {
    public function managePayments($userRole) {
        $paymentAdmin = new ProtectivePaymentProxy($userRole);
        $paymentAdmin->viewPayments();
        $paymentAdmin->processRefund(paymentId: 202);
    }
}

