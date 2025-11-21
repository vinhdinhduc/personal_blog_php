<?php

/**
 * User Controller
 * Quản lý users trong admin panel
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/ToastHelper.php';

class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->userModel = new UserModel();
    }

    /**
     * Danh sách users
     */
    public function index()
    {
        $page = $this->input('page', 1);
        $perPage = 20;

        // Get users through Model
        $users = $this->userModel->getAllWithStats($page, $perPage);
        $totalUsers = $this->userModel->getTotalUsers();

        $pagination = $this->paginate($totalUsers, $page, $perPage);

        $this->viewWithLayout('admin/users', [
            'users' => $users,
            'pagination' => $pagination,
            'totalUsers' => $totalUsers,
            'pageTitle' => 'Quản lý người dùng',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    /**
     * Hiển thị form tạo user
     */
    public function showFormCreate()
    {
        $this->viewWithLayout('admin/manage_user/user_create', [
            'pageTitle' => 'Thêm người dùng mới',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    /**
     * Tạo user mới
     */
    public function create()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/users/create');
            return;
        }

        // Handle avatar upload
        $avatar = '';
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFile('avatar', 'uploads/avatars/');
            if ($uploadResult['success']) {
                $avatar = $uploadResult['path'];
            }
        }

        // Prepare data for Model
        $data = [
            'first_name' => Security::sanitize($this->input('first_name')),
            'last_name' => Security::sanitize($this->input('last_name')),
            'email' => Security::sanitize($this->input('email')),
            'password' => $this->input('password'),
            'password_confirm' => $this->input('password_confirm'),
            'role' => $this->input('role', 'user'),
            'avatar' => $avatar
        ];

        // Create through Model
        $result = $this->userModel->register($data);

        if ($result['success']) {
            Toast::success('Tạo người dùng thành công');
            $this->redirect('/admin/users');
        } else {
            Toast::error($result['message']);
            $this->redirect('/admin/users/create');
        }
    }

    /**
     * Hiển thị form edit user
     */
    public function showFormEdit($id)
    {
        // Get user with stats through Model
        $user = $this->userModel->getUserWithStats($id);

        if (!$user) {
            Session::flash('error', 'Không tìm thấy người dùng');
            $this->redirect('/admin/users');
            return;
        }

        $this->viewWithLayout('admin/manage_user/user_edit', [
            'user' => $user,
            'pageTitle' => 'Chỉnh sửa người dùng',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    /**
     * Cập nhật user
     */
    public function update($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/users/update/' . $id);
            return;
        }

        // Validate role change permission
        $role = $this->input('role', 'user');
        if ($id == Session::getUserId() && $role !== Session::getUserRole()) {
            Session::flash('error', 'Không thể thay đổi role của chính bạn');
            $this->redirect('/admin/users/update/' . $id);
            return;
        }

        // Handle avatar upload
        $avatarPath = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFile('avatar', 'uploads/avatars/');
            if ($uploadResult['success']) {
                $avatarPath = $uploadResult['path'];
            }
        }

        // Prepare update data
        $updateData = [
            'first_name' => Security::sanitize($this->input('first_name')),
            'last_name' => Security::sanitize($this->input('last_name')),
            'email' => Security::sanitize($this->input('email')),
            'role' => $role
        ];

        // Add password if provided
        $password = $this->input('password');
        if (!empty($password)) {
            $updateData['password'] = $password;
            $updateData['password_confirm'] = $this->input('password_confirm');
        }

        if ($avatarPath) {
            $updateData['avatar'] = $avatarPath;
        }

        // Update through Model
        $result = $this->userModel->updateUser($id, $updateData);

        if ($result['success']) {
            // Update session if editing own profile
            if ($id == Session::getUserId()) {
                $userData = Session::getUserData();
                $userData['first_name'] = $updateData['first_name'];
                $userData['last_name'] = $updateData['last_name'];
                $userData['email'] = $updateData['email'];
                if ($avatarPath) {
                    $userData['avatar'] = $avatarPath;
                }
                Session::set('user_data', $userData);
            }

            Toast::success('Cập nhật người dùng thành công');
        } else {
            Toast::error($result['message']);
        }

        $this->redirect('/admin/users/update/' . $id);
    }

    /**
     * Xóa user
     */
    public function delete($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        // Delete through Model
        $result = $this->userModel->deleteUser($id, Session::getUserId());

        if ($result['success']) {
            Session::flash('success', $result['message']);
            $this->json($result);
        } else {
            Session::flash('error', $result['message']);
            $this->json($result, 500);
        }
    }
}
