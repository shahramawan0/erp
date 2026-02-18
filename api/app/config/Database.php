<?php
require_once __DIR__ . '/EnvironmentConfig.php';

class Database {
    private $host, $db_name, $username, $password, $conn;

    public function __construct() {
        date_default_timezone_set('Asia/Karachi');
        EnvironmentConfig::load();
        $this->host = EnvironmentConfig::get('DB_HOST', 'localhost');
        $this->db_name = EnvironmentConfig::get('DB_NAME', 'khawaja_traders');
        $this->username = EnvironmentConfig::get('DB_USERNAME', 'root');
        $this->password = EnvironmentConfig::get('DB_PASSWORD', '');
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username, $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>
