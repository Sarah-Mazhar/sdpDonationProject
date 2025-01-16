<?php

// Secure session start
session_start();

// Database Connection
require_once 'Database.php';
require_once '../models/UserIterator.php';

interface AdminInterface {
    public function accessAdminPanel();
}

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
        $users = $this->fetchUsers();
        $userIterator = new UserIterator($users);

        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Actions</th></tr>";

        while ($userIterator->hasNext()) {
            $user = $userIterator->next();
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['type']}</td>";
            echo "<td>
                <form method='post'>
                    <input type='hidden' name='user_id' value='{$user['id']}'>
                    <select name='new_role'>
                        <option value='user'>User</option>
                        <option value='donation_admin'>Donation Admin</option>
                        <option value='payment_admin'>Payment Admin</option>
                        <option value='super_admin'>Super Admin</option>
                    </select>
                    <button type='submit' name='change_role'>Change Role</button>
                </form>
              </td>";
            echo "</tr>";
        }

        echo "</table>";
    }

    public function fetchUsers() {
        $stmt = $this->db->query("SELECT id, email, type FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function changeUserRole($userId, $newRole) {
        $stmt = $this->db->prepare("UPDATE users SET type = :type WHERE id = :id");
        $stmt->execute([':type' => $newRole, ':id' => $userId]);
        echo "User role updated successfully.";
    }
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

    $db = Database::getInstance();
    $connection = $db->getConnection();
    $admin = new RealAdmin($connection);
    $admin->changeUserRole($userId, $newRole);
}

// Example Usage
try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        throw new Exception("Unauthorized access. Please log in.");
    }

    $db = Database::getInstance();
    $connection = $db->getConnection();

    $user = new User($_SESSION['user_id'], $_SESSION['user_type']);
    $proxy = new ProxyAdminAccess($user, $connection);
    $proxy->accessAdminPanel();
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
