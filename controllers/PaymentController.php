<?php
require_once __DIR__ . '/../models/payment/ProtectivePaymentProxy.php';
require_once __DIR__ . '/../models/payment/PaymentAdminInterface.php';
require_once __DIR__ . '/../models/payment/RealPaymentAdmin.php';
require_once __DIR__ . '/../models/payment/PaymentManager.php';

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

            if (!in_array($method, ['cash', 'visa'])) {
                echo "Invalid payment method.";
                return;
            }

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
