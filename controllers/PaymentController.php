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
    // public function viewPayments() {
    //     if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
    //         $this->paymentAdmin->viewPayments();
    //     } else {
    //         echo "Access denied: You are not authorized to view payments.";
    //     }
    // }

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
    

    // Add a Payment (Admin-only)
    // public function addPayment($amount, $method) {
    //     if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
    //         $amount = floatval($amount);
    //         if ($amount <= 0) {
    //             echo "Invalid payment amount.";
    //             return;
    //         }

    //         // List of valid payment methods
    //         $validMethods = ['cash', 'visa', 'third_party']; // Updated to include third-party
    //         if (!in_array($method, $validMethods)) {
    //             echo "Invalid payment method.";
    //             return;
    //         }

    //         // Handle third-party payment
    //         if ($method === 'third_party') {
    //             $thirdPartyGateway = new ThirdPartyPaymentGateway();
    //             $thirdPartyAdapter = new ThirdPartyPaymentAdapter($thirdPartyGateway);
    //             $result = $thirdPartyAdapter->pay($amount);
    //             echo $result['message']; // Output: Processed payment of <amount> via Third-Party Payment Gateway.
    //             return;
    //         }

    //         // Handle cash and visa payments
    //         $this->paymentAdmin->addPayment($amount, $method);
    //         echo "Payment of {$amount} via {$method} added successfully.";
    //     } else {
    //         echo "Access denied: You are not authorized to add payments.";
    //     }
    // }


    // public function addPayment($amount, $method) {
    //     if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
    //         // Validate amount
    //         $amount = floatval($amount);
    //         if ($amount <= 0) {
    //             echo "Invalid payment amount. Please enter a positive number.";
    //             return;
    //         }
    
    //         // List of valid payment methods
    //         $validMethods = ['cash', 'visa', 'third_party'];
    //         if (!in_array($method, $validMethods)) {
    //             echo "Invalid payment method. Allowed methods are: cash, visa, or third_party.";
    //             return;
    //         }
    
    //         // Handle third-party payment
    //         if ($method === 'third_party') {
    //             $thirdPartyGateway = new ThirdPartyPaymentGateway();
    //             $thirdPartyAdapter = new ThirdPartyPaymentAdapter($thirdPartyGateway);
    //             try {
    //                 $result = $thirdPartyAdapter->pay($amount);
    //                 echo $result['message']; // Example: Processed payment of <amount> via Third-Party Payment Gateway.
    //                 return;
    //             } catch (Exception $e) {
    //                 echo "Error processing third-party payment: " . $e->getMessage();
    //                 return;
    //             }
    //         }
    
    //         // Handle cash and visa payments
    //         try {
    //             $this->paymentAdmin->addPayment($amount, $method);
    //             echo "Payment of {$amount} via {$method} added successfully.";
    //         } catch (Exception $e) {
    //             echo "Error adding payment: " . $e->getMessage();
    //         }
    //     } else {
    //         echo "Access denied: You are not authorized to add payments.";
    //     }
    // }

    public function addPayment($amount, $method) {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            // Validate amount
            $amount = floatval($amount);
            if ($amount <= 0) {
                echo "Invalid payment amount. Please enter a positive number.";
                return;
            }
    
            // List of valid payment methods
            $validMethods = ['cash', 'visa', 'third_party'];
            if (!in_array($method, $validMethods)) {
                echo "Invalid payment method. Allowed methods are: cash, visa, or third_party.";
                return;
            }
    
            // Handle third-party payment
            if ($method === 'third_party') {
                $thirdPartyGateway = new ThirdPartyPaymentGateway();
                $thirdPartyAdapter = new ThirdPartyPaymentAdapter($thirdPartyGateway);
                try {
                    $result = $thirdPartyAdapter->pay($amount);
                    echo $result['message']; // Example: Processed payment of <amount> via Third-Party Payment Gateway.
                    return;
                } catch (Exception $e) {
                    echo "Error processing third-party payment: " . $e->getMessage();
                    return;
                }
            }
    
            // Insert payment into the database
            try {
                // Database connection
                $db = Database::getInstance()->getConnection();
    
                // Get the user ID (ensure a valid session contains this)
                $userId = $_SESSION['user_id'] ?? null;
                if (!$userId) {
                    echo "User ID not found in the session.";
                    return;
                }
    
                // Insert the payment into the donations table
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
    
    

    // Delete a Payment (Admin-only)
    // public function deletePayment($paymentId) {
    //     if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
    //         if (!is_numeric($paymentId) || $paymentId <= 0) {
    //             echo "Invalid payment ID.";
    //             return;
    //         }

    //         $this->paymentAdmin->deletePayment($paymentId);
    //         echo "Payment ID {$paymentId} deleted successfully.";
    //     } else {
    //         echo "Access denied: You are not authorized to delete payments.";
    //     }
    // }

    // public function deletePayment($paymentId) {
    //     if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
    //         // Validate the payment ID
    //         if (!is_numeric($paymentId) || intval($paymentId) <= 0) {
    //             echo "Invalid payment ID. Please provide a valid positive integer.";
    //             return;
    //         }

    //         try {
    //             // Use database connection to delete the record
    //             $db = Database::getInstance()->getConnection(); // Ensure Database is properly implemented
    //             $stmt = $db->prepare("DELETE FROM donations WHERE id = :id");
    //             $stmt->bindParam(':id', $paymentId, PDO::PARAM_INT);
    //             $stmt->execute();
    
    //             echo "Payment with ID {$paymentId} deleted successfully.";
    //         } catch (Exception $e) {
    //             // Handle exceptions gracefully
    //             echo "Error deleting payment: " . htmlspecialchars($e->getMessage());
    //         }
    
    //         // try {
    //         //     // Attempt to delete the payment
    //         //     $this->paymentAdmin->deletePayment($paymentId);
    //         //     echo "Payment with ID {$paymentId} deleted successfully.";
    //         // } catch (Exception $e) {
    //         //     // Handle exceptions gracefully
    //         //     echo "Error deleting payment: " . $e->getMessage();
    //         // }
    //     } else {
    //         // Handle unauthorized access
    //         echo "Access denied: You are not authorized to delete payments.";
    //     }
    // }


    public function deletePayment($paymentId) {
        if ($_SESSION['user_type'] === 'payment_admin' || $_SESSION['user_type'] === 'super_admin') {
            // Validate the payment ID
            if (!is_numeric($paymentId) || intval($paymentId) <= 0) {
                echo "Invalid payment ID. Please provide a valid positive integer.";
                return;
            }
    
            try {
                // Database connection
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("DELETE FROM donations WHERE id = :id");
                $stmt->bindParam(':id', $paymentId, PDO::PARAM_INT);
                $stmt->execute();
                
                // Provide feedback
                echo "Payment with ID {$paymentId} deleted successfully.";
            } catch (Exception $e) {
                echo "Error deleting payment: " . htmlspecialchars($e->getMessage());
            }
        } else {
            echo "Access denied: You are not authorized to delete payments.";
        }
    }
    
    

    // // Manage Payments (View Payments Entry Point)
    // public function managePayments() {
    //     $userType = $_SESSION['user_type'] ?? 'user';

    //     if ($userType === 'payment_admin' || $userType === 'super_admin') {
    //         $this->viewPayments();
    //     } else {
    //         echo "Access denied: You are not authorized to manage payments.";
    //     }
    // }


    // Manage Payments (View Payments Entry Point)
public function managePayments() {
    // Retrieve user type from session or set to 'user' as default
    $userType = $_SESSION['user_type'] ?? 'user';

    // Check if the user is authorized to manage payments
    if (in_array($userType, ['payment_admin', 'super_admin'])) {
        try {
            // Call the viewPayments method for authorized users
            $this->viewPayments();
        } catch (Exception $e) {
            // Handle any errors encountered while managing payments
            echo "An error occurred while managing payments: " . htmlspecialchars($e->getMessage());
        }
    } else {
        // Display an access denied message for unauthorized users
        echo "Access denied: You are not authorized to manage payments.";
    }
}


    
}
?>