<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=donation_project', 'root', '');
    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage();
}
