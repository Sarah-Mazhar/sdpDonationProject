<?php

// Secure session start
session_start();

// Database Connection
require_once 'Database.php';

class ProxyAdminAccess implements AdminInterface {
    private $realAdmin;
    private $user;
    private $db;

    public function __construct(User $user, $db) {
        $this->user = $user;
        $this->db = $db;
    }

    public function accessAdminPanel() {
        if ($this->user->getType() === 'super_admin') {
            if ($this->realAdmin === null) {
                $this->realAdmin = new RealAdmin($this->db);
            }
            return $this->realAdmin->accessAdminPanel();
        } else {
            throw new Exception("Access Denied: Only super admins can access the admin panel.");
        }
    }
}

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

interface AdminInterface {
    public function accessAdminPanel();
}

class User {
    private $id;
    private $type;

    public function __construct($id, $type) {
        $this->id = $id;
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function getId() {
        return $this->id;
    }
}

// Handling role change submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['new_role'];

    $db = new Database();
    $connection = $db->getConnection();
    $admin = new RealAdmin($connection);
    $admin->changeUserRole($userId, $newRole);
}

// Example Usage
try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        throw new Exception("Unauthorized access. Please log in.");
    }

    $db = new Database();
    $connection = $db->getConnection();

    $user = new User($_SESSION['user_id'], $_SESSION['user_type']);
    $proxy = new ProxyAdminAccess($user, $connection);
    $proxy->accessAdminPanel();
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
