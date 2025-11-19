<?php
class Security
{

    //Tạo token CSRF
    public static function generateCSRFToken()
    {
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf_token"];
    }
    //Xác thực token CSRF
    public static function verifyCSRFToken($token)
    {

        if (!isset($_SESSION["csrf_token"])) {
            return false;
        }
        return hash_equals($_SESSION["csrf_token"], $token);
    }

    //Escape dữ liệu để chống XSS
    public static function escape($data)
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    //sanitize input
    public static function sanitize($data)
    {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    public static function createSlug($string)
    {
        // Chuyển đổi sang chữ thường
        $slug = strtolower($string);
        // Thay thế các ký tự đặc biệt bằng dấu gạch ngang
        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
        // Loại bỏ các dấu gạch ngang thừa
        $slug = preg_replace('/-+/', '-', $slug);
        // Loại bỏ dấu gạch ngang ở đầu và cuối
        $slug = trim($slug, '-');
        return $slug;
    }

    //Hash mật khẩu

    public static function hashPassword($pwd)
    {
        return password_hash($pwd, PASSWORD_BCRYPT, ["cost" => 10]);
    }

    // Verify pw
    public static function verifyPassword($pwd, $hash)
    {
        return password_verify($pwd, $hash);
    }

    //Validate email
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    //Validate file upload
    public static function validateFileUpload($file, $allowedTypes = ["image/jpeg", "image/png", "image/gif"], $maxSize = 5242880)
    {
        //Check có lỗi k
        if ($file["error"] !== UPLOAD_ERR_OK) {
            return ["success" => false, "message" => "Lỗi khi tải tệp lên."];
        }
        // Check type file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);
        if (!in_array($mimeType, $allowedTypes)) {
            return ["success" => false, "message" => "File không đúng định dạng"];
        }
        // Check size file
        if ($file["size"] > $maxSize) {
            return ["success" => false, "message" => "File vượt quá kích thước cho phép."];
        }
        return ["success" => true, "message" => "OK"];
    }

    // Rate limit
    public static function rateLimit($key, $limit = 5, $timeWindow = 60)
    {
        if (!isset($_SESSION["rate_limit"][$key])) {
            $_SESSION["rate_limit"][$key] = [
                "count" => 1,
                "start_time" => time()
            ];
            return true;
        }
        $data = $_SESSION["rate_limit"][$key];
        $elapsed = time() - $data["start_time"];

        if ($elapsed > $timeWindow) {
            //reset
            $_SESSION["rate_limit"][$key] = [
                "count" => 1,
                "start_time" => time()
            ];
            return true;
        }
        if ($data['count'] >= $limit) {
            return false;
        }

        $_SESSION['rate_limit'][$key]['count']++;
        return true;
    }
}
