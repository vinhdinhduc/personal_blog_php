<?php


require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../helpers/Security.php";
require_once __DIR__ . "/BaseModel.php";

class UserModel extends BaseModel
{
    protected $table = "users";

    // ============================================
    // AUTHENTICATION METHODS
    // ============================================

    /**
     * Đăng ký user mới
     */
    public function register($data)
    {
        // Validate
        $validation = $this->validateRegistration($data);
        if (!$validation['success']) {
            return $validation;
        }

        // Check email exists
        if ($this->emailExists($data["email"])) {
            return ["success" => false, "message" => "Email đã được sử dụng."];
        }

        // Prepare data
        $userData = [
            'first_name' => $data['first_name'] ?? $data['fName'] ?? '',
            'last_name' => $data['last_name'] ?? $data['lName'] ?? '',
            'email' => $data['email'],
            'password_hash' => Security::hashPassword($data['password']),
            'role' => $data['role'] ?? 'user',

        ];

        $userId = $this->insert($userData);

        if ($userId) {
            return [
                "success" => true,
                "message" => "Đăng ký thành công.",
                "user_id" => $userId
            ];
        }

        return ["success" => false, "message" => "Đăng ký thất bại. Vui lòng thử lại."];
    }

    /**
     * Login user
     */
    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            return ["success" => false, "message" => "Vui lòng điền đầy đủ thông tin."];
        }

        $user = $this->findOne(['email' => $email]);

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
                "role" => $user["role"],
                "avatar" => $user["avatar"] ?? ''
            ]
        ];
    }

    // ============================================
    // USER RETRIEVAL METHODS
    // ============================================

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        return $this->findById($id, 'id, first_name, last_name, email, role, avatar, created_at');
    }
    public function getFullUserById($id)
    {
        return $this->findById($id);
    }
    /**
     * Get user with stats
     */
    public function getUserWithStats($id)
    {
        $query = "SELECT u.*,
                         CONCAT(u.first_name, ' ', u.last_name) as full_name,
                         COUNT(DISTINCT p.id) as post_count,
                         COUNT(DISTINCT c.id) as comment_count
                  FROM {$this->table} u
                  LEFT JOIN posts p ON u.id = p.user_id
                  LEFT JOIN comments c ON u.id = c.user_id
                  WHERE u.id = :id
                  GROUP BY u.id";

        return $this->queryOne($query, ['id' => $id]);
    }

    /**
     * Get all users with pagination and stats
     * CHUYỂN TỪ CONTROLLER
     */
    public function getAllWithStats($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT u.*,
                         CONCAT(u.first_name, ' ', u.last_name) as full_name,
                         COUNT(DISTINCT p.id) as post_count,
                         COUNT(DISTINCT c.id) as comment_count
                  FROM {$this->table} u
                  LEFT JOIN posts p ON u.id = p.user_id
                  LEFT JOIN comments c ON u.id = c.user_id
                  GROUP BY u.id
                  ORDER BY u.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // ============================================
    // USER UPDATE METHODS
    // ============================================

    /**
     * Update user data
     */
    public function updateUser($id, $data)
    {
        // Validate
        $validation = $this->validateUpdate($data);
        if (!$validation['success']) {
            return $validation;
        }

        // Check email uniqueness
        if (isset($data['email'])) {
            $existingUser = $this->findOne(['email' => $data['email']]);
            if ($existingUser && $existingUser['id'] != $id) {
                return ['success' => false, 'message' => 'Email đã được sử dụng'];
            }
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['first_name', 'last_name', 'email', 'role', 'bio', 'phone', 'website', 'avatar', 'status'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) { // Dùng array_key_exists để cho phép null
                $updateData[$field] = $data[$field];
            }
        }

        // Handle password update
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                return ['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự'];
            }
            if ($data['password'] !== $data['password_confirm']) {
                return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp'];
            }
            $updateData['password_hash'] = Security::hashPassword($data['password']);
        }

        if ($this->updateById($id, $updateData)) {
            return ['success' => true, 'message' => 'Cập nhật thành công'];
        }

        return ['success' => false, 'message' => 'Cập nhật thất bại'];
    }

    /**
     * Change user role
     */
    public function changeRole($userId, $role, $currentUserId)
    {
        if (!in_array($role, ['user', 'admin'])) {
            return ['success' => false, 'message' => 'Role không hợp lệ'];
        }

        if ($userId == $currentUserId) {
            return ['success' => false, 'message' => 'Không thể thay đổi role của chính bạn'];
        }

        if ($this->updateById($userId, ['role' => $role])) {
            return ['success' => true, 'message' => 'Cập nhật role thành công'];
        }

        return ['success' => false, 'message' => 'Không thể cập nhật role'];
    }

    /**
     * Delete user
     */
    public function deleteUser($userId, $currentUserId)
    {
        if ($userId == $currentUserId) {
            return ['success' => false, 'message' => 'Không thể xóa chính bạn'];
        }

        if ($this->deleteById($userId)) {
            return ['success' => true, 'message' => 'Xóa người dùng thành công'];
        }

        return ['success' => false, 'message' => 'Không thể xóa người dùng'];
    }

    // ============================================
    // STATISTICS METHODS (CHUYỂN TỪ CONTROLLER)
    // ============================================

    /**
     * Lấy tổng số users
     */
    public function getTotalUsers()
    {
        return $this->count();
    }

    /**
     * Search users by keyword
     */
    public function searchUsers($keyword, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$keyword}%";

        $query = "SELECT u.*,
                         CONCAT(u.first_name, ' ', u.last_name) as full_name,
                         COUNT(DISTINCT p.id) as post_count,
                         COUNT(DISTINCT c.id) as comment_count
                  FROM {$this->table} u
                  LEFT JOIN posts p ON u.id = p.user_id
                  LEFT JOIN comments c ON u.id = c.user_id
                  WHERE u.first_name LIKE :search 
                     OR u.last_name LIKE :search
                     OR u.email LIKE :search
                  GROUP BY u.id
                  ORDER BY u.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':search', $searchTerm);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count users by search
     */
    public function countSearchUsers($keyword)
    {
        $searchTerm = "%{$keyword}%";

        $query = "SELECT COUNT(*) as total
                  FROM {$this->table}
                  WHERE first_name LIKE :search 
                     OR last_name LIKE :search
                     OR email LIKE :search";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':search', $searchTerm);
        $stmt->execute();

        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT u.*,
                         CONCAT(u.first_name, ' ', u.last_name) as full_name,
                         COUNT(DISTINCT p.id) as post_count,
                         COUNT(DISTINCT c.id) as comment_count
                  FROM {$this->table} u
                  LEFT JOIN posts p ON u.id = p.user_id
                  LEFT JOIN comments c ON u.id = c.user_id
                  WHERE u.role = :role
                  GROUP BY u.id
                  ORDER BY u.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get active users count
     */
    public function getActiveUsersCount()
    {
        return $this->count(['status' => 'active']);
    }

    /**
     * Get recent registered users
     */
    public function getRecentUsers($limit = 10)
    {
        $query = "SELECT u.*,
                         CONCAT(u.first_name, ' ', u.last_name) as full_name
                  FROM {$this->table} u
                  ORDER BY u.created_at DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null)
    {
        if ($excludeId) {
            $query = "SELECT COUNT(*) as count FROM {$this->table} 
                      WHERE email = :email AND id != :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':email' => $email]);
        }

        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Save remember token
     */
    public function saveRememberToken($userId, $token)
    {
        return $this->updateById($userId, ['remember_token' => $token]);
    }

    /**
     * Verify remember token
     */
    public function verifyRememberToken($userId, $token)
    {
        return $this->exists(['id' => $userId, 'remember_token' => $token]);
    }

    /**
     * Update user status
     */
    public function updateStatus($userId, $status)
    {
        if (!in_array($status, ['active', 'inactive', 'banned'])) {
            return ['success' => false, 'message' => 'Status không hợp lệ'];
        }

        if ($this->updateById($userId, ['status' => $status])) {
            return ['success' => true, 'message' => 'Cập nhật status thành công'];
        }

        return ['success' => false, 'message' => 'Không thể cập nhật status'];
    }

    /**
     * Ban user
     */
    public function banUser($userId, $currentUserId)
    {
        if ($userId == $currentUserId) {
            return ['success' => false, 'message' => 'Không thể ban chính bạn'];
        }

        return $this->updateStatus($userId, 'banned');
    }

    /**
     * Unban user
     */
    public function unbanUser($userId)
    {
        return $this->updateStatus($userId, 'active');
    }

    // ============================================
    // VALIDATION METHODS
    // ============================================

    /**
     * Validate registration data
     */
    private function validateRegistration($data)
    {
        $firstName = $data['first_name'] ?? $data['fName'] ?? '';
        $lastName = $data['last_name'] ?? $data['lName'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            return ["success" => false, "message" => "Vui lòng điền đầy đủ thông tin."];
        }

        if (!Security::validateEmail($email)) {
            return ["success" => false, "message" => "Email không hợp lệ."];
        }

        if (strlen($password) < 6) {
            return ["success" => false, "message" => "Mật khẩu phải có ít nhất 6 ký tự."];
        }

        return ["success" => true];
    }

    /**
     * Validate update data
     */
    private function validateUpdate($data)
    {
        if (isset($data['email']) && !Security::validateEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }

        if (isset($data['first_name']) && empty($data['first_name'])) {
            return ['success' => false, 'message' => 'Tên không được để trống'];
        }

        if (isset($data['last_name']) && empty($data['last_name'])) {
            return ['success' => false, 'message' => 'Họ không được để trống'];
        }

        return ['success' => true];
    }

    /**
     * Check if user can be deleted
     */
    public function canDelete($userId, $currentUserId)
    {
        // Không thể xóa chính mình
        if ($userId == $currentUserId) {
            return ['can_delete' => false, 'reason' => 'Không thể xóa chính bạn'];
        }

        // Kiểm tra user có posts không
        $query = "SELECT COUNT(*) as post_count FROM posts WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();

        if ($result['post_count'] > 0) {
            return [
                'can_delete' => false,
                'reason' => "User có {$result['post_count']} bài viết. Hãy xóa hoặc chuyển bài viết trước."
            ];
        }

        return ['can_delete' => true];
    }


    public function getUserByEmail($email)
    {
        return $this->findOne(['email' => $email]);
    }

    public function resetRememberToken($userId)
    {
        return $this->updateById($userId, ['remember_token' => null]);
    }
}
