<?php
class Database
{
    private $host = "localhost";
    private $db_name = 'blog';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $conn;

    public function connect()
    {
        $this->conn = null;
        try {

            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            die("Không thể kết nối database. Vui lòng kiểm tra cấu hình.");
        }
        return $this->conn;
    }
}
