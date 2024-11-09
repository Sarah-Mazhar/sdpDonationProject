<?php
// strategies/EmailAuthStrategy.php

require_once 'AuthStrategy.php';
require_once __DIR__ . '/../User.php';

class EmailAuthStrategy implements AuthStrategy {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function authenticate($email, $password) {
        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
