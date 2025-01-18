<?php
require_once __DIR__ . '/../models/payment/ProtectivePaymentProxy.php';
require_once __DIR__ . '/../models/payment/PaymentAdminInterface.php';
require_once __DIR__ . '/../models/payment/RealPaymentAdmin.php';
require_once __DIR__ . '/../models/payment/PaymentManager.php';
require_once __DIR__ . '/../models/payment/ThirdPartyPaymentGateway.php';
require_once __DIR__ . '/../models/payment/ThirdPartyPaymentAdapter.php'; 

$paymentManager = new PaymentManager();

class PaymentController {
    private $paymentAdmin;

    public function __construct() {
        $userType = $_SESSION['user_type'] ?? 'user';
        $this->paymentAdmin = new ProtectivePaymentProxy($userType);
    }

    public function viewPayments() {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            $db = Database::getInstance()->getConnection();
            $query = "
                SELECT 
                    d.id, 
                    d.amount, 
                    d.user_id, 
                    u.email AS user_email, 
                    d.created_at 
                FROM donations d
                JOIN users u ON d.user_id = u.id
                WHERE d.type = 'money'
                ORDER BY d.created_at DESC
            ";
            $stmt = $db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            echo "Access denied: You are not authorized to view payments.";
            return [];
        }
    }
    
    public function addPayment($amount, $method) {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            $amount = floatval($amount);
            if ($amount <= 0) {
                echo "Invalid payment amount. Please enter a positive number.";
                return;
            }    
            $validMethods = ['cash', 'visa', 'third_party'];
            if (!in_array($method, $validMethods)) {
                echo "Invalid payment method. Allowed methods are: cash, visa, or third_party.";
                return;
            }
    
            if ($method === 'third_party') {
                $thirdPartyGateway = new ThirdPartyPaymentGateway();
                $thirdPartyAdapter = new ThirdPartyPaymentAdapter($thirdPartyGateway);
                try {
                    $result = $thirdPartyAdapter->pay($amount);
                    echo $result['message'];
                    return;
                } catch (Exception $e) {
                    echo "Error processing third-party payment: " . $e->getMessage();
                    return;
                }
            }
    
            try {
                $db = Database::getInstance()->getConnection();
                $userId = $_SESSION['user_id'] ?? null;
                if (!$userId) {
                    echo "User ID not found in the session.";
                    return;
                }
    
                $stmt = $db->prepare("
                    INSERT INTO donations (type, amount, user_id, created_at) 
                    VALUES (:type, :amount, :user_id, NOW())
                ");
                $stmt->execute([
                    ':type' => 'money',
                    ':amount' => $amount,
                    ':user_id' => $userId,
                ]);
    
                echo "Payment of {$amount} via {$method} added successfully to the database.";
            } catch (Exception $e) {
                echo "Error adding payment: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "Access denied: You are not authorized to add payments.";
        }
    }
    

    public function deletePayment($paymentId) {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            if (!is_numeric($paymentId) || intval($paymentId) <= 0) {
                echo "Invalid payment ID. Please provide a valid positive integer.";
                return;
            }
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("DELETE FROM donations WHERE id = :id");
                $stmt->bindParam(':id', $paymentId, PDO::PARAM_INT);
                $stmt->execute();                
                echo "Payment with ID {$paymentId} deleted successfully.";
            } catch (Exception $e) {
                echo "Error deleting payment: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "Access denied: You are not authorized to delete payments.";
        }
    }
    
    public function managePayments() {
        $userType = $_SESSION['user_type'] ?? 'user';
        if (in_array($userType, ['payment_admin', 'super_admin'])) {
            try {
                $this->viewPayments();
            } catch (Exception $e) {
                echo "An error occurred while managing payments: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "Access denied: You are not authorized to manage payments.";
        }
}

}
?>