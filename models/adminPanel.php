<?php

session_start();

// Include necessary files
require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Proxy/ProxyAdminAccess.php';


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
