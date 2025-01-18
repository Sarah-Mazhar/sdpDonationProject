<?php
require_once __DIR__ . '/../../config/Database.php';

class PaymentManager {
    private $db;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function managePayments($userRole): void {
        $paymentAdmin = new ProtectivePaymentProxy(userType: $userRole);
        $paymentAdmin->viewPayments();
        $paymentAdmin->processRefund(paymentId: 202);
    }

   
}
