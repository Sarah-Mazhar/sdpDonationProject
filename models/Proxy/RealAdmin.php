<?php
// models/Proxy/RealAdmin.php

require_once 'AdminInterface.php';

class RealAdmin implements AdminInterface {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function accessAdminPanel() {
        $this->displayUserList();
    }

    private function displayUserList() {
        $stmt = $this->db->query("SELECT id, email, type FROM users ORDER BY type, email");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Actions</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['type']}</td>";
            echo "<td>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='user_id' value='{$user['id']}'>";
            echo "<select name='new_role'>";
            echo "<option value='user'>User</option>";
            echo "<option value='donation_admin'>Donation Admin</option>";
            echo "<option value='payment_admin'>Payment Admin</option>";
            echo "<option value='super_admin'>Super Admin</option>";
            echo "</select>";
            echo "<button type='submit' name='change_role'>Change Role</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function changeUserRole($userId, $newRole) {
        $validRoles = ['user', 'donation_admin', 'payment_admin', 'super_admin', 'coordinator'];

        if (!in_array($newRole, $validRoles)) {
            throw new Exception("Invalid role selected.");
        }

        $stmt = $this->db->prepare("UPDATE users SET type = :type WHERE id = :id");
        $stmt->execute([':type' => $newRole, ':id' => $userId]);
        echo "User role updated successfully.";
    }
}
