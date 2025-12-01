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
     * Cập nhật avatar - FIXED VERSION
     */
    /**
     * Cập nhật avatar - SIMPLE VERSION (không dùng JSON)
     */
    public function updateAvatar()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Toast::error('Invalid request');
            $this->redirect('/profile');
            return;
        }

        $userId = Session::getUserId();

        // ✅ CHECK FILE
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
            Toast::error('Vui lòng chọn file ảnh');
            $this->redirect('/profile');
            return;
        }

        if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            Toast::error('Có lỗi khi tải file lên');
            $this->redirect('/profile');
            return;
        }

        // ✅ Get user info để xóa ảnh cũ
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            Toast::error('User không tồn tại');
            $this->redirect('/profile');
            return;
        }

        // ✅ Upload file
        $uploadResult = $this->uploadFile('avatar', 'uploads/avatars/');

        if (!$uploadResult['success']) {
            Toast::error($uploadResult['message']);
            $this->redirect('/profile');
            return;
        }

        // ✅ XÓA ảnh cũ
        if (!empty($user['avatar']) && strpos($user['avatar'], 'default-avatar') === false) {
            $oldPath = __DIR__ . '/../../public/' . ltrim($user['avatar'], '/');
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // ✅ Update database
        $result = $this->userModel->updateUser($userId, [
            'avatar' => $uploadResult['path']
        ]);

        if ($result['success']) {
            // Update session
            $userData = Session::getUserData();
            $userData['avatar'] = $uploadResult['path'];
            Session::set('user_data', $userData);

            Toast::success('Cập nhật ảnh đại diện thành công');
        } else {
            // Xóa file vừa upload nếu không lưu được vào DB
            $uploadedPath = __DIR__ . '/../../public/' . $uploadResult['path'];
            if (file_exists($uploadedPath)) {
                @unlink($uploadedPath);
            }
            Toast::error($result['message']);
        }

        $this->redirect('/profile');
    }

    /**
     * Helper: Resize image
     */
    private function resizeImage($filePath, $maxWidth, $maxHeight)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) {
            return false;
        }

        list($width, $height, $type) = $imageInfo;

        // Không cần resize nếu ảnh đã nhỏ hơn
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true;
        }

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        // Create image resource
        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filePath);
                break;
            case IMAGETYPE_WEBP:
                $source = imagecreatefromwebp($filePath);
                break;
            default:
                return false;
        }

        // Create new image
        $destination = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($destination, false);
            imagesavealpha($destination, true);
            $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($destination, $filePath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($destination, $filePath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($destination, $filePath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($destination, $filePath, 85);
                break;
        }

        // Clean up
        imagedestroy($source);
        imagedestroy($destination);

        return true;
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
