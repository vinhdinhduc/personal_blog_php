<?php

/**
 * Auth Controller
 * Xử lý đăng ký, đăng nhập, đăng xuất
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController
{

    /**
     * Hiển thị form đăng ký
     */
    public function showRegister()
    {
        // Nếu đã đăng nhập thì redirect về home
        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        $this->viewWithLayout('auth/register', [
            'pageTitle' => 'Đăng ký tài khoản',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/layout-auth');
    }

    /**
     * Xử lý đăng ký
     */
    public function register()
    {
        $this->validateMethod('POST');

        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid request');
            $this->redirect('/register');
            return;
        }

        // Rate limiting
        if (!Security::rateLimit('register', 3, 3600)) {
            Session::flash('error', 'Bạn đã đăng ký quá nhiều lần. Vui lòng thử lại sau 1 giờ');
            $this->redirect('/register');
            return;
        }

        $userModel = new User();

        // Get input (lấy name từ trường "name")
        $data = [
            'fName' => Security::sanitize($this->input('fName')),
            'lName' => Security::sanitize($this->input('lName')),
            'email' => Security::sanitize($this->input('email')),
            'password' => $this->input('password'),
            'password_confirm' => $this->input('password_confirm')
        ];

        // Validate
        if (empty($data['fName']) || empty($data['lName']) || empty($data['email']) || empty($data['password'])) {
            Session::flash('error', 'Vui lòng điền đầy đủ thông tin');
            $this->redirect('/register');
            return;
        }

        if (!Security::validateEmail($data['email'])) {
            Session::flash('error', 'Email không hợp lệ');
            $this->redirect('/register');
            return;
        }

        if ($data['password'] !== $data['password_confirm']) {
            Session::flash('error', 'Mật khẩu xác nhận không khớp');
            $this->redirect('/register');
            return;
        }

        if (strlen($data['password']) < 6) {
            Session::flash('error', 'Mật khẩu phải có ít nhất 6 ký tự');
            $this->redirect('/register');
            return;
        }

        // Register
        $result = $userModel->register($data);

        if ($result['success']) {
            Session::flash('success', 'Đăng ký thành công! Vui lòng đăng nhập');
            $this->redirect('/login');
        } else {
            Session::flash('error', $result['message']);
            $this->redirect('/register');
        }
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function showLogin()
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        $this->viewWithLayout('auth/login', [
            'pageTitle' => 'Đăng nhập',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/layout-auth');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login()
    {
        $this->validateMethod('POST');

        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        // Validate CSRF
        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid request');
            $this->redirect('/login');
            return;
        }

        // Rate limiting - chống brute force
        $rateLimitKey = 'login_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        if (!Security::rateLimit($rateLimitKey, 5, 300)) {
            Session::flash('error', 'Quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau 5 phút');
            $this->redirect('/login');
            return;
        }

        $userModel = new User();

        $email = Security::sanitize($this->input('email'));
        $password = $this->input('password');
        $remember = $this->input('remember') === 'on';

        // Login
        $result = $userModel->login($email, $password);

        if ($result['success']) {
            $user = $result['user'];

            // Set session với role
            Session::login($user['id'], $user['role'], [
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'email' => $user['email']
            ]);

            // Remember me
            if ($remember) {
                $this->setRememberMeCookie($user['id'], $userModel);
            }

            // LOGIC REDIRECT THEO ROLE
            if ($user['role'] === 'admin') {
                // Admin redirect về dashboard
                $redirect = Session::get('intended_url', '/admin');
            } else {
                // User thường redirect về home
                $redirect = Session::get('intended_url', '/');
            }

            Session::remove('intended_url');
            Session::flash('success', 'Đăng nhập thành công!');
            $this->redirect($redirect);
        } else {
            Session::flash('error', $result['message']);
            $this->redirect('/login');
        }
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        // Xóa remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
            setcookie('remember_user', '', time() - 3600, '/');
        }

        Session::logout();
        Session::flash('success', 'Đã đăng xuất');
        $this->redirect('/');
    }

    /**
     * Set remember me cookie
     * @param int $userId
     * @param User $userModel
     */
    private function setRememberMeCookie($userId, $userModel)
    {
        // Tạo token
        $token = Security::generateCSRFToken(32);

        // Hash token trước khi lưu vào DB
        $hashedToken = hash('sha256', $token);

        // Lưu vào database
        $userModel->saveRememberToken($userId, $hashedToken);

        // Set cookie (30 ngày)
        $expire = time() + (30 * 24 * 60 * 60);
        setcookie('remember_token', $token, $expire, '/', '', true, true);
        setcookie('remember_user', $userId, $expire, '/', '', true, true);
    }

    /**
     * Check remember me cookie và auto login
     */
    public function checkRememberMe()
    {
        if (Session::isLoggedIn()) {
            return;
        }

        if (!isset($_COOKIE['remember_token']) || !isset($_COOKIE['remember_user'])) {
            return;
        }

        $userId = (int)$_COOKIE['remember_user'];
        $token = $_COOKIE['remember_token'];
        $hashedToken = hash('sha256', $token);

        $userModel = new User();

        if ($userModel->verifyRememberToken($userId, $hashedToken)) {
            $user = $userModel->getUserById($userId);

            if ($user) {
                Session::login($user['id'], $user['role'], [
                    'first_name' => $user['first_name'] ?? '',
                    'last_name' => $user['last_name'] ?? '',
                    'email' => $user['email']
                ]);
            }
        } else {
            // Token không hợp lệ, xóa cookie
            setcookie('remember_token', '', time() - 3600, '/');
            setcookie('remember_user', '', time() - 3600, '/');
        }
    }
}
