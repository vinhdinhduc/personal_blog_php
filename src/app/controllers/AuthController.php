<?php



require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/ToastHelper.php';
require_once __DIR__ . '/../helpers/EmailHelper.php';
require_once __DIR__ . '/../models/ResetPwdModel.php';

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
            Toast::error('Invalid request');
            $this->redirect('/register');
            return;
        }

        // Rate limiting
        // if (!Security::rateLimit('register', 3, 3600)) {
        //     Toast::error('Bạn đã đăng ký quá nhiều lần. Vui lòng thử lại sau 1 giờ');
        //     $this->redirect('/register');
        //     return;
        // }

        $userModel = new UserModel();

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
            Toast::error('Vui lòng điền đầy đủ thông tin');
            $this->redirect('/register');
            return;
        }

        if (!Security::validateEmail($data['email'])) {
            Toast::error('Email không hợp lệ');
            $this->redirect('/register');
            return;
        }

        if ($data['password'] !== $data['password_confirm']) {
            Toast::error('Mật khẩu xác nhận không khớp');
            $this->redirect('/register');
            return;
        }

        if (strlen($data['password']) < 6) {
            Toast::error('Mật khẩu phải có ít nhất 6 ký tự');
            $this->redirect('/register');
            return;
        }

        // Register
        $result = $userModel->register($data);

        if ($result['success']) {
            $fullName = $data['fName'] . ' ' . $data['lName'];
            $emailSent = EmailHelper::sendWelcomeEmail($data['email'], $fullName);
            if (!$emailSent) {
                Toast::warning('Đăng ký thành công nhưng không thể gửi email chào mừng.');
            } else {
                Toast::info('Email chào mừng đã được gửi đến ' . $data['email']);
            }
            Toast::success('Đăng ký thành công! Vui lòng đăng nhập để tiếp tục.');

            $this->redirect('/login');
        } else {
            Toast::error($result['message']);


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
            Toast::error('Invalid request');
            $this->redirect('/login');
            return;
        }

        // Rate limiting - chống brute force
        $rateLimitKey = 'login_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        if (!Security::rateLimit($rateLimitKey, 5, 300)) {
            Toast::error('Quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau 5 phút');
            $this->redirect('/login');
            return;
        }

        $userModel = new UserModel();

        $email = Security::sanitize($this->input('email'));
        $password = $this->input('password');
        $remember = $this->input('remember') === 'on';

        // Login
        $result = $userModel->login($email, $password);

        if ($result['success']) {
            $user = $result['user'];

            // Lưu thông tin vào session
            Session::login($user['id'], $user['role'], [
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'email' => $user['email'],
                'avatar' => $user['avatar'] ?? ''
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
            Toast::success('Đăng nhập thành công!');

            $this->redirect($redirect);
        } else {
            Toast::error($result['message']);
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
        Toast::success('Đã đăng xuất');
        $this->redirect('/');
    }
    public function showForgotPassword()
    {
        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        $this->viewWithLayout("/auth/forgot_password", [
            "pageTitle" => "Quên mật khẩu",
            "csrfToken" => Security::generateCSRFToken()
        ], "layouts/layout-auth");
    }
    public function forgotPassword()
    {
        $this->validateMethod('POST');

        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        if (!$this->validateCSRF()) {
            Toast::error('Invalid request');
            $this->redirect('/forgot-password');
            return;
        }

        $rateLimitKey = "forgot_password_" . ($_SERVER["REMOTE_ADDR"] ?? "unknown");
        // if (!Security::rateLimit($rateLimitKey, 3, 3600)) {
        //     Toast::error('Bạn đã thử quên mật khẩu quá nhiều lần. Vui lòng thử lại sau 1 giờ.');
        //     $this->redirect('/forgot-password');
        //     return;
        // }

        $email = Security::sanitize($this->input('email'));


        //Validate dữ liệu

        if (empty($email)) {
            Toast::error('Vui lòng nhập email');
            $this->redirect('/forgot-password');
            return;
        }

        if (!Security::validateEmail($email)) {
            Toast::error('Email không hợp lệ');
            $this->redirect('/forgot-password');
            return;
        }

        //Check user có tồn tại không

        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email);

        //Luôn hiển thị thông báo thành công để tránh lộ thông tin user
        if (!$user) {
            Toast::info('Nếu email tồn tại trong hệ thống, một liên kết đặt lại mật khẩu đã được gửi đến email của bạn.');
            $this->redirect('/forgot-password');
            return;
        }

        //Generate token

        $resetTokenModel = new ResetPwdModel();

        $token = $resetTokenModel->createResetToken($user["id"], $email);

        if ($token) {


            $resetLink = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/personal-blog/reset-password?token=" . $token;
            //get full name
            $fullName = trim(($user["first_name"] ?? "") . " " . ($user["last_name"] ?? ""));

            //trường hợp không có tên thì dùng email làm tên
            if (empty($fullName)) {
                $fullName = explode("@", $email)[0];
            }
            error_log("Attempting to send reset email to: $email");
            error_log("Reset link: $resetLink");
            $emailSent = EmailHelper::sendPasswordResetEmail($email, $fullName, $resetLink);
            error_log("Email sent result: " . ($emailSent ? 'SUCCESS' : 'FAILED'));
            if ($emailSent) {
                Toast::success("Chúng tôi đã gửi liên kết đặt lại mật khẩu đến email của bạn.");
            } else {
                Toast::error("Không thể gửi email đặt lại mật khẩu. Vui lòng thử lại sau.");
            }
        } else {

            Toast::error("Có lỗi xảy ra. Vui lòng thử lại sau.");
        }
        $this->redirect('/forgot-password');
    }
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

    // Kiểm tra và xử lý Remember Me
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

        $userModel = new UserModel();

        if ($userModel->verifyRememberToken($userId, $hashedToken)) {
            $user = $userModel->getUserById($userId);

            if ($user) {
                Session::login($user['id'], $user['role'], [
                    'first_name' => $user['first_name'] ?? '',
                    'last_name' => $user['last_name'] ?? '',
                    'email' => $user['email'],
                    'avatar' => $user['avatar'] ?? ''
                ]);
            }
        } else {
            // Token không hợp lệ, xóa cookie
            setcookie('remember_token', '', time() - 3600, '/');
            setcookie('remember_user', '', time() - 3600, '/');
        }
    }



    public function showResetPassword($token)
    {

        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        $token = $this->input('token');
        if (empty($token)) {
            Toast::error('Token không hợp lệ');
            $this->redirect('/forgot-password');
            return;
        }

        //Verify token 

        $resetTokenModel = new ResetPwdModel();
        $resetData = $resetTokenModel->verifyToken($token);

        if (!$resetData) {
            Toast::error('Token không hợp lệ hoặc đã hết hạn');
            $this->redirect('/forgot-password');
            return;
        }

        $this->viewWithLayout('auth/reset_password', [
            'pageTitle' => 'Đặt lại mật khẩu',
            'csrfToken' => Security::generateCSRFToken(),
            'token' => $token,
            'email' => $resetData['email']
        ], 'layouts/layout-auth');
    }

    public function resetPassword()
    {
        $this->validateMethod('POST');

        if (Session::isLoggedIn()) {
            $this->redirect('/');
            return;
        }

        if (!$this->validateCSRF()) {
            Toast::error('Request không hợp lệ');
            $this->redirect('/reset-password');
            return;
        }


        $token = $this->input('token');
        $newPwd = trim($this->input('password'));
        $confirmPwd = trim($this->input('password_confirm'));


        if (empty($token) || empty($newPwd) || empty($confirmPwd)) {
            Toast::error('Vui lòng điền đầy đủ thông tin');
            if (!empty($token)) {
                $this->redirect('/reset-password?token=' . $token);
            } else {
                $this->redirect('/forgot-password');
            }
            return;
        }

        if ($newPwd !== $confirmPwd) {
            Toast::error('Mật khẩu xác nhận không khớp');
            $this->redirect('/reset-password?token=' . $token);
            return;
        }
        if (strlen($newPwd) < 6) {
            Toast::error('Mật khẩu phải có ít nhất 6 ký tự');
            $this->redirect('/reset-password?token=' . $token);
            return;
        }

        //Verify token

        $resetTokenModel = new ResetPwdModel();
        $resetData = $resetTokenModel->verifyToken($token);

        if (!$resetData) {
            Toast::error('Token không hợp lệ hoặc đã hết hạn');
            $this->redirect('/forgot-password');
            return;
        }
        //update user
        $userModel = new UserModel();
        $result = $userModel->updateUser($resetData['user_id'], [
            "password" => $newPwd,
            "password_confirm" => $confirmPwd
        ]);

        if ($result["success"]) {
            //Xoá token sau khi đổi mật khẩu thành công

            $resetTokenModel->deleteTokens($token);

            //Xoá tất cả token remember me của user
            $userModel->resetRememberToken($resetData['user_id']);
            Toast::success('Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');
            $this->redirect('/login');
        } else {
            Toast::error($result['message']);
            $this->redirect('/reset-password?token=' . $token);
        }
    }
}
