<?php

require_once 'AdminInterface.php';

class RealAdmin implements AdminInterface {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function accessAdminPanel() {
        echo "<h1>Welcome to the Admin Panel</h1>";
        $this->displayUserList();
    }

    public function displayUserList() {
        $users = $this->db->query("SELECT id, email, type FROM users")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Actions</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td><td>{$user['email']}</td><td>{$user['type']}</td>";
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
        $stmt = $this->db->prepare("UPDATE users SET type = :type WHERE id = :id");
        $stmt->execute([':type' => $newRole, ':id' => $userId]);
        echo "User role updated successfully.";
    }
}

?>
