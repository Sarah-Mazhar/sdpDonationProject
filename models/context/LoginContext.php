<?php

require_once __DIR__ . '/../strategies/EmailAuthStrategy.php';
require_once __DIR__ . '/../strategies/MobileAuthStrategy.php';

class LoginContext {
    private $authStrategy;

    public function __construct($authType, $db) {
        if ($authType === 'email') {
            $this->authStrategy = new EmailAuthStrategy($db);
        } elseif ($authType === 'mobile') {
            $this->authStrategy = new MobileAuthStrategy($db);
        } else {
            throw new Exception("Invalid login type.");
        }
    }

    public function authenticate($identifier, $password) {
        return $this->authStrategy->authenticate($identifier, $password);
    }
}
?>
