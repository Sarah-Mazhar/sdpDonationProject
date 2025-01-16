<?php
class AddUserCommand implements Command {
    private $db;
    private $userData;

    public function __construct($userData) {
        $this->db = Database::getInstance()->getConnection(); // Use the Database singleton
        $this->userData = $userData;
    }

    public function execute() {
        $stmt = $this->db->prepare("INSERT INTO users (email, password, type) VALUES (?, ?, ?)");
        $stmt->execute([$this->userData['email'], $this->userData['password'], 'user']);
    }

    public function undo() {
        $stmt = $this->db->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$this->userData['email']]);
    }
}

