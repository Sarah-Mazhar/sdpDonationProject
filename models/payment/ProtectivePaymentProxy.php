<?php
require_once 'PaymentAdminInterface.php';
require_once 'RealPaymentAdmin.php';

class ProtectivePaymentProxy implements PaymentAdminInterface {
    private $realAdmin;
    private $userRole;

    public function __construct(string $userRole) {
        $this->realAdmin = new RealPaymentAdmin();
        $this->userRole = $userRole;
    }

    public function viewPayments() {
        $this->realAdmin->viewPayments();
    }

    public function processRefund(int $paymentId) {
        if ($this->userRole === 'admin') {
            $this->realAdmin->processRefund($paymentId);
        } else {
            echo "Access denied: Insufficient permissions.";
        }
    }
}
