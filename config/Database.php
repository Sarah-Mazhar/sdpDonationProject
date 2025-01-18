<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'donation_project';
    private $username = 'root';
    private $password = '';
    private static $instance = null; 
    private $conn;

    private function __construct() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
    }

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    public function getConnection() {
        return $this->conn;
    }
}
?>
