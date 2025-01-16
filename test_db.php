<?php
require_once __DIR__ . '/config/Database.php';

$db = Database::getInstance()->getConnection();

if ($db) {
    echo "Database connection successful.";
} else {
    echo "Failed to connect to the database.";
}
?>
