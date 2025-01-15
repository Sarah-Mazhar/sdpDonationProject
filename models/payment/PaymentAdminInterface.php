<?php
interface PaymentAdminInterface {
    public function viewPayments();
    public function processRefund(int $paymentId);
}
