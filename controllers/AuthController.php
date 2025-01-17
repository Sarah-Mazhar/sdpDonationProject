<?php
// controllers/AuthController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/context/LoginContext.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Login method for Email or Mobile
    public function login($identifier, $password, $type) {
        // Sanitize inputs
        $identifier = htmlspecialchars(trim($identifier));
        $password = htmlspecialchars(trim($password));

        $loginContext = new LoginContext($type, $this->db);
        $user = $loginContext->authenticate($identifier, $password);

        if ($user) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['type'];

            // Redirect based on user type
            if ($user['type'] === 'user') {
                header('Location: /DonationProjecttt/views/user_dashboard.php');
            } else {
                header('Location: /DonationProjecttt/views/success.php');
            }
            exit;
        } else {
            // Redirect back to login with error message
            header('Location: /DonationProjecttt/views/login.php?error=invalid_credentials');
            exit;
        }
    }

    // Sign up a new user
    public function signup($email, $password, $mobile) {
        // Sanitize inputs
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars(trim($password));
        $mobile = htmlspecialchars(trim($mobile));

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format!";
            return;
        }

        $userModel = new User($this->db);
        if ($userModel->createUser($email, $password, $mobile)) {
            echo "Signup successful!";
        } else {
            echo "Signup failed! Email might already exist.";
        }
    }
}
