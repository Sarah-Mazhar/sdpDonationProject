<?php
// index.php

session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DonationController.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;
$authController = new AuthController();
$donationController = new DonationController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests (display forms)
    if ($action === 'login' && isset($_GET['login_type'])) {
        if ($_GET['login_type'] === 'email') {
            // Render Email Login form
            include 'views/login.php';
        } elseif ($_GET['login_type'] === 'mobile') {
            // Render Mobile Login form
            include 'views/login_mobile.php';
        }
    } elseif ($action === 'signup') {
        // Render Signup form
        include 'views/signup.php';
    } elseif ($action === 'donate' && isset($_GET['donation_type'])) {
        if ($_GET['donation_type'] === 'money') {
            // Render Money Donation form
            include 'views/donate_money.php';
        } elseif ($_GET['donation_type'] === 'food') {
            // Render Food Donation form
            include 'views/donate_food.php';
        }
    } else {
        // Default page or invalid action
        echo "Invalid or no action specified.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST requests (process login, signup, or donations)

    // Login for Email
    if ($action === 'login' && isset($_GET['login_type']) && $_GET['login_type'] === 'email') {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $authController->login($_POST['email'], $_POST['password'], 'email');
        } else {
            echo "Invalid email or password.";
        }
    }
    // Login for Mobile
    elseif ($action === 'login' && isset($_GET['login_type']) && $_GET['login_type'] === 'mobile') {
        if (isset($_POST['mobile']) && isset($_POST['password'])) {
            $authController->login($_POST['mobile'], $_POST['password'], 'mobile');
        } else {
            echo "Invalid mobile number or password.";
        }
    }
    // Sign Up
    elseif ($action === 'signup') {
        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['mobile'])) {
            $authController->signup($_POST['email'], $_POST['password'], $_POST['mobile']);
        } else {
            echo "Please fill in all fields.";
        }
    }
    // Money Donation
    elseif ($action === 'donate' && isset($_GET['donation_type']) && $_GET['donation_type'] === 'money') {
        if (isset($_POST['amount'])) {
            $donationController->donateMoney($_POST['amount'], $_POST['payment_method']);
        } else {
            echo "Please enter a donation amount.";
        }
    }
    // Food Donation with Extras
    elseif ($action === 'donate' && isset($_GET['donation_type']) && $_GET['donation_type'] === 'food') {
        if (isset($_POST['foodItem']) && isset($_POST['quantity'])) {
            // Retrieve extras as an array, if selected
            $extras = isset($_POST['extras']) ? $_POST['extras'] : [];
            $donationController->donateFood($_POST['foodItem'], $_POST['quantity'], $extras);
        } else {
            echo "Please enter valid food item and quantity.";
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid request method.";
}
?>
