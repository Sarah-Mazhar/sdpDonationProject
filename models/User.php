<?php
// models/User.php

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to create a new user
    public function createUser($email, $password, $mobile) {
        $sql = "INSERT INTO " . $this->table . " (email, password, mobile) VALUES (:email, :password, :mobile)";
        $stmt = $this->conn->prepare($sql);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':mobile', $mobile);

        return $stmt->execute();
    }

    // Method to get user by email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to get user by mobile
    public function getUserByMobile($mobile) {
        $sql = "SELECT * FROM " . $this->table . " WHERE mobile = :mobile";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':mobile', $mobile);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsersGroupedByType($includeSuperAdmin = false) {
        $condition = $includeSuperAdmin ? "" : "WHERE type != 'super_admin'";
        $sql = "
            SELECT id, email, type
            FROM " . $this->table . "
            $condition
            ORDER BY FIELD(type, 'donation_admin', 'payment_admin', 'user'), email
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $groupedUsers = [];
        foreach ($result as $user) {
            $groupedUsers[$user['type']][] = $user;
        }

        return $groupedUsers;
    }

    public function updateUserRole($userId, $newRole) {
        // Check if role is valid
        $validRoles = ['user', 'donation_admin', 'payment_admin', 'super_admin','coordinator'];
        if (!in_array($newRole, $validRoles)) {
            return false; // Invalid role
        }

        $sql = "UPDATE " . $this->table . " SET type = :type WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':type', $newRole);
        $stmt->bindParam(':id', $userId);

        return $stmt->execute();
    }

    public function getUsersForIterator() {
        $sql = "SELECT id, email, type FROM " . $this->table . " ORDER BY email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
