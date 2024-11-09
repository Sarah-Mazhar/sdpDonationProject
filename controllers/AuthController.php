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
        $loginContext = new LoginContext($type, $this->db);
        $user = $loginContext->authenticate($identifier, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: /DonationProjecttt/views/success.php');
            exit;
        } else {
            echo "Login failed!";
        }
    }

    // Sign up a new user
    public function signup($email, $password, $mobile) {
        $userModel = new User($this->db);
        if ($userModel->createUser($email, $password, $mobile)) {
            echo "Signup successful!";
        } else {
            echo "Signup failed!";
        }
    }
}
?>
