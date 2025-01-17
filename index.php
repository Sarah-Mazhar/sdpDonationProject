<?php
session_start();

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DonationController.php';

// Initialize controllers
$authController = new AuthController();
$donationController = new DonationController();

// Ensure the database connection is established
try {
    $database = Database::getInstance();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to handle invalid input
function invalidInput($message = "Invalid input.") {
    echo $message;
    exit;
}

// Handle GET requests
function handleGetRequest($action) {
    global $authController, $donationController;

    switch ($action) {
        case 'login':
            handleLoginView();
            break;

        case 'signup':
            include 'views/signup.php';
            break;

        case 'donate':
            handleDonationView();
            break;

        case 'print_receipt':
            handleReceiptView();
            break;

        case 'show_beneficiaries':
            showBeneficiaries();
            break;

        default:
            invalidInput("Invalid action.");
    }
}

// Handle POST requests
function handlePostRequest($action) {
    global $authController, $donationController;

    switch ($action) {
        case 'login':
            handleLogin();
            break;

        case 'signup':
            handleSignup();
            break;

        case 'add_payment':
            handleAddPayment();
            break;

        case 'delete_payment':
            handleDeletePayment();
            break;

        case 'donate':
            handleDonation();
            break;

        default:
            invalidInput("Invalid action.");
    }
}

// GET Handlers
function handleLoginView() {
    if (isset($_GET['login_type'])) {
        if ($_GET['login_type'] === 'email') {
            include 'views/login.php';
        } elseif ($_GET['login_type'] === 'mobile') {
            include 'views/login_mobile.php';
        } else {
            invalidInput("Invalid login type.");
        }
    } else {
        invalidInput("Login type not specified.");
    }
}

function handleDonationView() {
    if (isset($_GET['donation_type'])) {
        if ($_GET['donation_type'] === 'money') {
            include 'views/donate_money.php';
        } elseif ($_GET['donation_type'] === 'food') {
            include 'views/donate_food.php';
        } else {
            invalidInput("Invalid donation type.");
        }
    } else {
        invalidInput("Donation type not specified.");
    }
}

function handleReceiptView() {
    $type = $_GET['type'] ?? null;
    if ($type === 'money') {
        $receipt = $_SESSION['money_receipt'] ?? "No receipt available for money donation.";
        echo nl2br($receipt);
    } elseif ($type === 'food') {
        $receipt = $_SESSION['food_receipt'] ?? "No receipt available for food donation.";
        echo nl2br($receipt);
    } else {
        invalidInput("Invalid receipt type.");
    }
}

function showBeneficiaries() {
    global $db;

    try {
        $stmt = $db->query("SELECT * FROM beneficiaries");
        $beneficiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        ?>
        <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
            <h1 class="text-center mb-4">Beneficiaries</h1>
            <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Needs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($beneficiaries as $beneficiary): ?>
                        <tr>
                            <td><?= htmlspecialchars($beneficiary['id']) ?></td>
                            <td><?= htmlspecialchars($beneficiary['name']) ?></td>
                            <td><?= htmlspecialchars($beneficiary['needs']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        echo ob_get_clean();
    } catch (Exception $e) {
        die("Error retrieving beneficiaries: " . $e->getMessage());
    }
}

// POST Handlers
function handleLogin() {
    global $authController;

    if (isset($_GET['login_type'])) {
        if ($_GET['login_type'] === 'email' && isset($_POST['email'], $_POST['password'])) {
            $authController->login($_POST['email'], $_POST['password'], 'email');
        } elseif ($_GET['login_type'] === 'mobile' && isset($_POST['mobile'], $_POST['password'])) {
            $authController->login($_POST['mobile'], $_POST['password'], 'mobile');
        } else {
            invalidInput("Invalid login credentials.");
        }
    } else {
        invalidInput("Login type not specified.");
    }
}

function handleSignup() {
    global $authController;

    if (isset($_POST['email'], $_POST['password'], $_POST['mobile'])) {
        $authController->signup($_POST['email'], $_POST['password'], $_POST['mobile']);
    } else {
        invalidInput("Please fill in all fields.");
    }
}

function handleAddPayment() {
    require_once __DIR__ . '/controllers/PaymentController.php';
    $paymentController = new PaymentController();

    if (isset($_POST['amount'], $_POST['method'])) {
        $paymentController->addPayment($_POST['amount'], $_POST['method']);
    } else {
        invalidInput("Invalid input for adding payment.");
    }
}

function handleDeletePayment() {
    require_once __DIR__ . '/controllers/PaymentController.php';
    $paymentController = new PaymentController();

    if (isset($_POST['payment_id'])) {
        $paymentController->deletePayment($_POST['payment_id']);
    } else {
        invalidInput("Invalid input for deleting payment.");
    }
}

function handleDonation() {
    global $donationController;

    if (isset($_GET['donation_type'])) {
        if ($_GET['donation_type'] === 'money' && isset($_POST['amount'], $_POST['payment_method'])) {
            $donationController->donateMoney($_POST['amount'], $_POST['payment_method']);
        } elseif ($_GET['donation_type'] === 'food' && isset($_POST['foodItem'], $_POST['quantity'])) {
            $extras = $_POST['extras'] ?? [];
            $donationController->donateFood($_POST['foodItem'], $_POST['quantity'], $extras);
        } else {
            invalidInput("Invalid donation input.");
        }
    } else {
        invalidInput("Donation type not specified.");
    }
}

// Main entry point
$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    handleGetRequest($action);
} elseif ($method === 'POST') {
    handlePostRequest($action);
} else {
    echo "Invalid request method.";
}