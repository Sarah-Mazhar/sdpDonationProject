<?php
session_start();

require_once '../config/Database.php';
require_once '../models/User.php';
require_once '../models/Proxy/ProxyAdminAccess.php';

try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        throw new Exception("Unauthorized access. Please log in.");
    }

    $db = new Database();
    $connection = $db->getConnection();

    $user = new User($_SESSION['user_id'], $_SESSION['user_type']);
    $proxy = new ProxyAdminAccess($user, $connection);

    // Access Admin Panel
    $proxy->accessAdminPanel();
} catch (Exception $e) {
    echo "<div style='text-align: center; margin-top: 20px;'>";
    echo "<h3 style='color: red;'>Error: " . $e->getMessage() . "</h3>";
    echo "</div>";
}
