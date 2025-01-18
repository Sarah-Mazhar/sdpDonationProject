<?php

require_once 'AuthStrategy.php';
require_once __DIR__ . '/../User.php';

class MobileAuthStrategy implements AuthStrategy {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function authenticate($mobile, $password) {
        $user = $this->userModel->getUserByMobile($mobile);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
?>
