<?php
class Session
{
    //init session

    public static function start()
    {

        if (session_status() == PHP_SESSION_NONE) {

            //Config session an toàn
            ini_set("session.cookie_httponly", 1);
            ini_set("session.use_only_cookies", 1);
            ini_set("session.cookie_secure", isset($_SERVER["HTTPS"]));
            session_start();
        }
    }

    //create session when login
    public static function generateSession()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    //Set session data
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    //Get session data
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    //Kiểm tra nếu tồn tại session
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    //Remove session key 

    public static function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    //
    //Destroy session
    public static function destroy()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (isset($_COOKIE[session_name()])) {

                setcookie(session_name(), "", time() - 3600, "/");
            }
            session_destroy();
        }
    }

    //flash message
    public static function flash($key, $value)
    {
        $_SESSION["flash"][$key] = $value;
    }

    //Get and remove flash message
    public static function getFlash($key)
    {
        if (isset($_SESSION["flash"][$key])) {
            $value = $_SESSION["flash"][$key];
            unset($_SESSION["flash"][$key]);
            return $value;
        }
        return null;
    }

    //Check user  is logged in
    public static function isLoggedIn()
    {
        return isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"]);
    }

    //Get current user ID
    public static function getUserId()
    {
        return $_SESSION["user_id"] ?? null;
    }

    //Get current user role
    public static function getUserRole()
    {
        return $_SESSION["user_role"] ?? null;
    }

    public static function getUserData()
    {
        return $_SESSION["user_data"] ?? [];
    }
    //Check if user is admin
    public static function isAdmin()
    {
        return self::getUserRole() === 'admin';
    }

    //Login user
    public static function login($userId, $role, $userData = [])
    {
        self::generateSession();
        $_SESSION["user_id"] = $userId;
        $_SESSION["user_role"] = $role;

        // Tạo full_name nếu có first_name và last_name
        if (isset($userData['first_name']) && isset($userData['last_name'])) {
            $userData['name'] = trim($userData['first_name'] . ' ' . $userData['last_name']);
        }

        $_SESSION["user_data"] = $userData;
        $_SESSION["login_time"] = time();
    }

    //Logout
    public static function logout()
    {
        self::destroy();
    }
}
