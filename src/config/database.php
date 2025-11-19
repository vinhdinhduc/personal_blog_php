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
    public function loadEnv()
    {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;

                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                if ($name === 'DB_HOST') $this->host = $value;
                if ($name === 'DB_NAME') $this->db_name = $value;
                if ($name === 'DB_USER') $this->username = $value;
                if ($name === 'DB_PASS') $this->password = $value;
            }
        }
    }
}
