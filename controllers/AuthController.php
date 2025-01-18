<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/context/LoginContext.php';

class AuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($identifier, $password, $type) {
        $identifier = htmlspecialchars(trim($identifier));
        $password = htmlspecialchars(trim($password));

        $loginContext = new LoginContext($type, $this->db);
        $user = $loginContext->authenticate($identifier, $password);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['type'];
            if ($user['type'] === 'user') {
                header('Location: /DonationProjecttt/views/user_dashboard.php');
            } else {
                header('Location: /DonationProjecttt/views/success.php');
            }
            exit;
        } else {
            header('Location: /DonationProjecttt/views/login.php?error=invalid_credentials');
            exit;
        }
    }

    public function signup($email, $password, $mobile) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars(trim($password));
        $mobile = htmlspecialchars(trim($mobile));
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