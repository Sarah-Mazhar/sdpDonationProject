<?php
require_once 'PaymentAdminInterface.php';
require_once 'RealPaymentAdmin.php';

class ProtectivePaymentProxy implements PaymentAdminInterface {
    private $realPaymentAdmin;
    private $userType;

    public function __construct($userType) {
        $this->userType = $userType;
        $this->realPaymentAdmin = new RealPaymentAdmin();
    }

    public function viewPayments() {
        if ($this->userType === 'payment_admin' || $this->userType === 'super_admin') {
            $this->realPaymentAdmin->viewPayments();
        } else {
            echo "Access denied: Insufficient permissions.";
        }
    }

    public function processRefund(int $paymentId): void {
        if ($this->userType === 'payment_admin' || $this->userType === 'super_admin') {
            $this->realPaymentAdmin->processRefund($paymentId);
        } else {
            echo "Access denied: Insufficient permissions.";
        }
    }

    public function addPayment($amount, $method): void {
        if ($this->userType === 'payment_admin' || $this->userType === 'super_admin') {
            echo "Adding payment of $amount via $method.";
        } else {
            echo "Access denied: Insufficient permissions.";
        }
    }

    public function deletePayment($paymentId): void {
        if ($this->userType === 'payment_admin' || $this->userType === 'super_admin') {
            echo "Deleting payment ID $paymentId.";
        } else {
            echo "Access denied: Insufficient permissions.";
        }
    }
}
