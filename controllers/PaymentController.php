<?php
require_once __DIR__ . '/../models/payment/ProtectivePaymentProxy.php';
require_once __DIR__ . '/../models/payment/PaymentAdminInterface.php';
require_once __DIR__ . '/../models/payment/RealPaymentAdmin.php';
require_once __DIR__ . '/../models/payment/PaymentManager.php';
require_once __DIR__ . '/../models/payment/ThirdPartyPaymentGateway.php'; // New
require_once __DIR__ . '/../models/payment/ThirdPartyPaymentAdapter.php'; // New

$paymentManager = new PaymentManager();

class PaymentController {
    private $paymentAdmin;

    public function __construct() {
        $userType = $_SESSION['user_type'] ?? 'user';

        // Initialize the proxy for payment administration
        $this->paymentAdmin = new ProtectivePaymentProxy($userType);
    }

    // View All Payments (Admin-only)
    public function viewPayments() {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            $this->paymentAdmin->viewPayments();
        } else {
            echo "Access denied: You are not authorized to view payments.";
        }
    }

    // Add a Payment (Admin-only)
    public function addPayment($amount, $method) {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            $amount = floatval($amount);
            if ($amount <= 0) {
                echo "Invalid payment amount.";
                return;
            }

            // List of valid payment methods
            $validMethods = ['cash', 'visa', 'third_party']; // Updated to include third-party
            if (!in_array($method, $validMethods)) {
                echo "Invalid payment method.";
                return;
            }

            // Handle third-party payment
            if ($method === 'third_party') {
                $thirdPartyGateway = new ThirdPartyPaymentGateway();
                $thirdPartyAdapter = new ThirdPartyPaymentAdapter($thirdPartyGateway);
                $result = $thirdPartyAdapter->pay($amount);
                echo $result['message']; // Output: Processed payment of <amount> via Third-Party Payment Gateway.
                return;
            }

            // Handle cash and visa payments
            $this->paymentAdmin->addPayment($amount, $method);
            echo "Payment of {$amount} via {$method} added successfully.";
        } else {
            echo "Access denied: You are not authorized to add payments.";
        }
    }

    // Delete a Payment (Admin-only)
    public function deletePayment($paymentId) {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            if (!is_numeric($paymentId) || $paymentId <= 0) {
                echo "Invalid payment ID.";
                return;
            }

            $this->paymentAdmin->deletePayment($paymentId);
            echo "Payment ID {$paymentId} deleted successfully.";
        } else {
            echo "Access denied: You are not authorized to delete payments.";
        }
    }

    // Manage Payments (View Payments Entry Point)
    public function managePayments() {
        $userType = $_SESSION['user_type'] ?? 'user';

        if ($userType === 'payment_admin' || $userType === 'super_admin') {
            $this->viewPayments();
        } else {
            echo "Access denied: You are not authorized to manage payments.";
        }
    }
}
?>