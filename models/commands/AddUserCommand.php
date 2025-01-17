<?php
require_once 'Command.php';

class AddUserCommand implements Command {
    private $db;
    private $userData;
    private $lastInsertedId;

    public function __construct($db, $userData) {
        $this->db = $db;
        $this->userData = $userData;
    }

    public function execute() {
        $stmt = $this->db->prepare("INSERT INTO users (email, type) VALUES (:email, :type)");
        $stmt->execute([
            ':email' => $this->userData['email'],
            ':type' => $this->userData['type']
        ]);
        $this->lastInsertedId = $this->db->lastInsertId();
    }

    public function undo() {
        if ($this->lastInsertedId) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $this->lastInsertedId]);
        }
    }

    public function redo() {
        if (!empty($this->userData)) {
            $stmt = $this->db->prepare("INSERT INTO users (id, email, type) VALUES (:id, :email, :type)");
            $stmt->execute([
                ':id' => $this->lastInsertedId,
                ':email' => $this->userData['email'],
                ':type' => $this->userData['type']
            ]);
        }
    }
}
