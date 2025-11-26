<?php

require_once __DIR__ . '/BaseModel.php';

class ResetPwdModel extends BaseModel
{
    protected $table = 'password_resets';

    protected $fillable = ['email', 'token', 'created_at'];

    // Thêm các phương thức liên quan đến việc quản lý token đặt lại mật khẩu nếu cần


    public function createResetToken($userId, $email)
    {
        try {
            // Xoá các token cũ nếu có
            $this->deleteUserTokens($userId);

            // Random token
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);

            // Thời hạn token
            $expiredAt = date('Y-m-d H:i:s', time() + 3600); // 1 giờ

            // Lưu token vào DB
            $stmt = $this->conn->prepare("
                INSERT INTO {$this->table} (user_id, email, token, expires_at, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");

            $result = $stmt->execute([$userId, $email, $hashedToken, $expiredAt]);

            if ($result) {

                return $token; // Trả về token chưa mã hoá để gửi qua email
            }

            return false;
        } catch (PDOException $th) {
            error_log("Create reset token error: " . $th->getMessage());
            return false;
        }
    }

    public function verifyToken($token)
    {
        try {
            $hashedToken = hash("sha256", $token);

            // Sử dụng raw query vì cần operator >
            $query = "SELECT * FROM {$this->table} 
                      WHERE token = :token AND expires_at > :now 
                      LIMIT 1";

            $result = $this->queryOne($query, [
                'token' => $hashedToken,
                'now' => date('Y-m-d H:i:s')
            ]);

            return $result ?: false;
        } catch (PDOException $th) {
            error_log("Verify token error: " . $th->getMessage());
            return false;
        }
    }

    //Delete token sau khi sử dụng

    public function deleteTokens($token)
    {
        try {
            $hashedToken = hash("sha256", $token);
            $result = $this->delete(["token" => $hashedToken]);
            return $result;
        } catch (PDOException $th) {
            error_log("Delete token error: " . $th->getMessage());
            return false;
            //throw $th;
        }
    }

    //Xoá tất cả token của user

    public function deleteUserTokens($userId)
    {
        try {
            $result = $this->delete(["user_id" => $userId]);
            return $result;
        } catch (PDOException $th) {
            error_log("Delete user tokens error: " . $th->getMessage());
            return false;
            //throw $th;
        }
    }

    //Xoá các token đã hết hạn


    public function deleteExpiredTokens()
    {
        try {
            // Sử dụng raw query vì cần operator <
            $query = "DELETE FROM {$this->table} WHERE expired_at < :now";

            $result = $this->execute($query, [
                'now' => date('Y-m-d H:i:s')
            ]);

            return $result;
        } catch (PDOException $th) {
            error_log("Delete expired tokens error: " . $th->getMessage());
            return false;
            //throw $th;
        }
    }
    //Check token đã tồn tại chưa


    public function tokenExists($token)
    {
        try {
            $hashedToken = hash("sha256", $token);
            $result = $this->findOne(["token" => $hashedToken]);
            return $result ? true : false;
        } catch (PDOException $th) {
            error_log("Check token exists error: " . $th->getMessage());
            return false;
        }
    }
}
