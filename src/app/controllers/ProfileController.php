<?php

/**
 * Profile Controller
 * Quản lý profile của user
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/ToastHelper.php';

class ProfileController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        Session::isLoggedIn();
        $this->userModel = new UserModel();
    }

    /**
     * Hiển thị trang profile
     */
    public function index()
    {
        $userId = Session::getUserId();
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            Toast::error('Không tìm thấy thông tin người dùng');
            $this->redirect('/');
            return;
        }

        $this->viewWithLayout('users/profile', [
            'user' => $user,
            'pageTitle' => 'Thông tin cá nhân',
            'csrfToken' => Security::generateCSRFToken()
        ], "layouts/main");
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateInfo()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Toast::error('Invalid request');
            $this->redirect('/profile');
            return;
        }

        $userId = Session::getUserId();

        $updateData = [
            'first_name' => Security::sanitize($this->input('first_name')),
            'last_name' => Security::sanitize($this->input('last_name')),
            'email' => Security::sanitize($this->input('email'))
        ];

        // Validate
        if (empty($updateData['first_name']) || empty($updateData['last_name']) || empty($updateData['email'])) {
            Toast::error('Vui lòng điền đầy đủ thông tin');
            $this->redirect('/profile');
            return;
        }

        if (!Security::validateEmail($updateData['email'])) {
            Toast::error('Email không hợp lệ');
            $this->redirect('/profile');
            return;
        }

        $result = $this->userModel->updateUser($userId, $updateData);

        if ($result['success']) {
            // Cập nhật session
            $userData = Session::getUserData();
            $userData['first_name'] = $updateData['first_name'];
            $userData['last_name'] = $updateData['last_name'];
            $userData['email'] = $updateData['email'];
            Session::set('user_data', $userData);

            Toast::success('Cập nhật thông tin thành công');
        } else {
            Toast::error($result['message']);
        }

        $this->redirect('/profile');
    }

    /**
     * Cập nhật avatar
     */
    public function updateAvatar()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        $userId = Session::getUserId();

        // ✅ Rate limiting - tránh spam upload
        $rateLimitKey = 'avatar_upload_' . $userId;
        if (!Security::rateLimit($rateLimitKey, 5, 300)) { // 5 lần / 5 phút
            $this->json([
                'success' => false,
                'message' => 'Bạn đang upload quá nhanh. Vui lòng chờ 5 phút'
            ], 429);
            return;
        }

        // Kiểm tra file upload
        // if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        //     $error = $_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE;
        //     $message = $this->getUploadErrorMessage($error);
        //     $this->json(['success' => false, 'message' => $message], 400);
        //     return;
        // }

        // ✅ Lấy thông tin user để xóa ảnh cũ
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $this->json(['success' => false, 'message' => 'User không tồn tại'], 404);
            return;
        }

        // ✅ Validate file trước khi upload
        // $validation = $this->validateAvatarFile($_FILES['avatar']);
        // if (!$validation['success']) {
        //     $this->json(['success' => false, 'message' => $validation['message']], 400);
        //     return;
        // }

        // Upload file
        $uploadResult = $this->uploadFile('avatar', 'uploads/avatars/', [
            'resize' => true,
            'max_width' => 500,
            'max_height' => 500,
            'quality' => 85
        ]);

        if (!$uploadResult['success']) {
            $this->json(['success' => false, 'message' => $uploadResult['message']], 400);
            return;
        }

        // ✅ XÓA ẢNH CŨ trước khi cập nhật
        if (!empty($user['avatar']) && $user['avatar'] !== 'public/images/default-avatar.png') {
            $oldAvatarPath = __DIR__ . '/../../' . $user['avatar'];
            if (file_exists($oldAvatarPath)) {
                @unlink($oldAvatarPath);
                error_log("✅ Deleted old avatar: " . $oldAvatarPath);
            }
        }

        // Cập nhật vào database
        $result = $this->userModel->updateUser($userId, [
            'avatar' => $uploadResult['path']
        ]);

        if ($result['success']) {
            // Cập nhật session
            $userData = Session::getUserData();
            $userData['avatar'] = $uploadResult['path'];
            Session::set('user_data', $userData);

            $this->json([
                'success' => true,
                'message' => 'Cập nhật ảnh đại diện thành công',
                'avatar_url' => Router::url('/' . $uploadResult['path'])
            ]);
        } else {
            // ✅ Xóa file vừa upload nếu không lưu được vào DB
            if (file_exists(__DIR__ . '/../../' . $uploadResult['path'])) {
                @unlink(__DIR__ . '/../../' . $uploadResult['path']);
            }
            $this->json(['success' => false, 'message' => $result['message']], 500);
        }
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Toast::error('Invalid request');
            $this->redirect('/profile');
            return;
        }

        $userId = Session::getUserId();

        $currentPassword = $this->input('current_password');
        $newPassword = $this->input('new_password');
        $confirmPassword = $this->input('confirm_password');

        // Validate
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Toast::error('Vui lòng điền đầy đủ thông tin');
            $this->redirect('/profile');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            Toast::error('Mật khẩu xác nhận không khớp');
            $this->redirect('/profile');
            return;
        }

        if (strlen($newPassword) < 6) {
            Toast::error('Mật khẩu mới phải có ít nhất 6 ký tự');
            $this->redirect('/profile');
            return;
        }

        // Kiểm tra mật khẩu hiện tại
        $user = $this->userModel->getFullUserById($userId);
        // var_dump($user);
        // die();
        if (!password_verify($currentPassword, $user['password_hash'])) {
            Toast::error('Mật khẩu hiện tại không đúng');
            $this->redirect('/profile');
            return;
        }

        // Cập nhật mật khẩu mới
        $result = $this->userModel->updateUser($userId, [
            'password' => $newPassword,
            'password_confirm' => $confirmPassword
        ]);

        if ($result['success']) {
            Toast::success('Đổi mật khẩu thành công');
        } else {
            Toast::error($result['message']);
        }

        $this->redirect('/profile');
    }
}
