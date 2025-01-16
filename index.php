<?php
// index.php

session_start();

// Enable error reporting for debugging (optional, remove in production)
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

// Get the action and method
$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// Ensure the database connection is established
try {
    $database = Database::getInstance();
    $db = $database->getConnection(); // Initialize the database connection
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

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
        
    }
}

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


// Handle the "Show Beneficiaries" action
if (isset($_GET['action']) && $_GET['action'] === 'show_beneficiaries') {
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
        $content = ob_get_clean();
        $title = "Beneficiaries";
        require 'views/layout.php';
        exit;
    } catch (Exception $e) {
        die("Error retrieving beneficiaries: " . $e->getMessage());
    }
}

// Default message for no action specified
if (!isset($_GET['action'])) {
    $content = "<p>Welcome to the Donation Project. Use the navigation to explore the system.</p>";
    $title = "Welcome";
    require 'views/layout.php';
    exit;
}

?>
