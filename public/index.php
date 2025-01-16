cd public
<?php
// index.php

session_start();

// Enable error reporting for debugging (optional, remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DonationController.php';
require_once __DIR__ . '/../controllers/PaymentController.php';
include __DIR__ . '/../views/login.php';


// Initialize controllers
$authController = new AuthController();
$donationController = new DonationController();

// Get the action and method
$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET requests
if ($method === 'GET') {
    switch ($action) {
        case 'login':
            if (isset($_GET['login_type'])) {
                if ($_GET['login_type'] === 'email') {
                    include 'views/login.php';
                } elseif ($_GET['login_type'] === 'mobile') {
                    include 'views/login_mobile.php';
                } else {
                    echo "Invalid login type.";
                }
            }
            break;
        case 'signup':
            include 'views/signup.php';
            break;
        case 'donate':
            if (isset($_GET['donation_type'])) {
                if ($_GET['donation_type'] === 'money') {
                    include 'views/donate_money.php';
                } elseif ($_GET['donation_type'] === 'food') {
                    include 'views/donate_food.php';
                } else {
                    echo "Invalid donation type.";
                }
            }
            break;
        default:
            echo "Invalid or no action specified.";
    }
}

// Handle POST requests
elseif ($method === 'POST') {
    switch ($action) {
        case 'login':
            if (isset($_GET['login_type'])) {
                if ($_GET['login_type'] === 'email' && isset($_POST['email'], $_POST['password'])) {
                    $authController->login($_POST['email'], $_POST['password'], 'email');
                } elseif ($_GET['login_type'] === 'mobile' && isset($_POST['mobile'], $_POST['password'])) {
                    $authController->login($_POST['mobile'], $_POST['password'], 'mobile');
                } else {
                    echo "Invalid login credentials.";
                }
            }
            break;
        case 'signup':
            if (isset($_POST['email'], $_POST['password'], $_POST['mobile'])) {
                $authController->signup($_POST['email'], $_POST['password'], $_POST['mobile']);
            } else {
                echo "Please fill in all fields.";
            }
            break;
        case 'donate':
            if (isset($_GET['donation_type'])) {
                if ($_GET['donation_type'] === 'money' && isset($_POST['amount'], $_POST['payment_method'])) {
                    $donationController->donateMoney($_POST['amount'], $_POST['payment_method']);
                } elseif ($_GET['donation_type'] === 'food' && isset($_POST['foodItem'], $_POST['quantity'])) {
                    $extras = $_POST['extras'] ?? [];
                    $donationController->donateFood($_POST['foodItem'], $_POST['quantity'], $extras);
                } else {
                    echo "Invalid donation input.";
                }
            }
            break;
        default:
            echo "Invalid request.";
    }
} else {
    echo "Invalid request method.";
}
?>
