<?php
require_once '../models/payment/ProtectivePaymentProxy.php';
require_once '../config/Database.php'; // Include the database connection

class PaymentManager {
    private $db; // Define the $db property

    public function __construct() {
        $database = new Database(); // Assuming Database.php has a Database class
        $this->db = $database->getConnection(); // Initialize the $db property
    }

    public function managePayments($userRole): void {
        $paymentAdmin = new ProtectivePaymentProxy(userType: $userRole);
        $paymentAdmin->viewPayments();
        $paymentAdmin->processRefund(paymentId: 202);
    }

   
}
