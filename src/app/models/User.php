<?php
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../helpers/Security.php";

class User
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    //Đăng ký user
    public function register($data)
    {
        if (empty($data["fName"]) || empty($data["lName"]) || empty($data["email"]) || empty($data["password"])) {
            return ["success" => false, "message" => "Vui lòng điền đầy đủ thông tin."];
        }
        if (!Security::validateEmail($data["email"])) {
            return ["success" => false, "message" => "Email không hợp lệ."];
        }

        if (strlen($data["password"]) < 6) {
            return ["success" => false, "message" => "Mật khẩu phải có ít nhất 6 ký tự."];
        }


        //Kiểm tra email đã tồn tại chưa
        if ($this->emailExists($data["email"])) {
            return ["success" => false, "message" => "Email đã được sử dụng."];
        }
        $passwordHash = Security::hashPassword($data["password"]);
        $query = "INSERT INTO {$this->table} (first_name, last_name, password_hash,email,role) VALUES ( :fName, :lName, :passwordHash, :email ,:role)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fName", $data["fName"]);
        $stmt->bindParam(":lName", $data["lName"]);
        $stmt->bindParam(":passwordHash", $passwordHash);
        $stmt->bindParam(":email", $data["email"]);
        $role = $data["role"] ?? 'user';
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Đăng ký thành công.", " user_id" => $this->conn->lastInsertId()];
        }
        return ["success" => false, "message" => "Đăng ký thất bại. Vui lòng thử lại."];
    }

    //Login

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            return ["success" => false, "message" => "Vui lòng điền đầy đủ thông tin."];
        }

        $query = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $user = $stmt->fetch();
        if (!$user) {
            return ["success" => false, "message" => "Email hoặc mật khẩu không đúng."];
        }
        if (!Security::verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
        }

        return [
            "success" => true,
            "message" => "Đăng nhập thành công.",
            "user" => [
                "id" => $user["id"],
                "first_name" => $user["first_name"],
                "last_name" => $user["last_name"],
                "email" => $user["email"],
                "role" => $user["role"]
            ]
        ];
    }

    //Get User by ID

    public function getUserById($id)
    {
        $query = "SELECT id, first_name,last_name,email,role,created_at FROM {$this->table} WHERE id = :id ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    //Check email exists
    private function emailExists($email)
    {
        $query = "SELECT id FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    //Update user
    public function update($id, $data)
    {
        $query = "UPDATE {$this->table} SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    //Lưu remember token

    public function saveRememberToken($userId, $token)
    {
        $query = "UPDATE {$this->table} SET remember_token = :token WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function verifyRememberToken($userId, $token)
    {
        $query = "SELECT id FROM {$this->table} WHERE id = :id AND remember_token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
