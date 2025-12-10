<?php

//Quản lý users

require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../helpers/ToastHelper.php";

class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->userModel = new UserModel();
    }

    //Danh sách users
    public function index()
    {
        $page = $this->input("page", 1);
        $perPage = 5;

        //Lấy danh sách users với thống kê qua Model
        $users = $this->userModel->getAllWithStats($page, $perPage);
        $totalUsers = $this->userModel->getTotalUsers();
        $adminCount = $this->userModel->getAdminCount();


        $pagination = $this->paginate($totalUsers, $page, $perPage);

        $this->viewWithLayout("admin/manage_user/users", [
            "users" => $users,
            "pagination" => $pagination,
            "totalUsers" => $totalUsers,
            "adminCount" => $adminCount,
            "pageTitle" => "Quản lý người dùng",
            "needUsers" => true,

            "csrfToken" => Security::generateCSRFToken()
        ], "layouts/admin_layout");
    }

    // Show form tạo user
    public function showFormCreate()
    {
        $this->viewWithLayout("admin/manage_user/user_create", [
            "pageTitle" => "Thêm người dùng mới",
            "needUsers" => true,
            "csrfToken" => Security::generateCSRFToken()
        ], "layouts/admin_layout");
    }

    // Tạo user mới
    public function create()
    {
        $this->validateMethod("POST");

        if (!$this->validateCSRF()) {
            Session::flash("error", "Invalid CSRF token");
            $this->redirect("/admin/users/create");
            return;
        }

        // Handle avatar upload
        $avatar = "";
        if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFile("avatar", "uploads/avatars/");
            if ($uploadResult["success"]) {
                $avatar = $uploadResult["path"];
            }
        }

        // Prepare data for Model
        $data = [
            "first_name" => Security::sanitize($this->input("first_name")),
            "last_name" => Security::sanitize($this->input("last_name")),
            "email" => Security::sanitize($this->input("email")),
            "password" => $this->input("password"),
            "password_confirm" => $this->input("password_confirm"),
            "role" => $this->input("role", "user"),
            "avatar" => $avatar
        ];


        // Create through Model
        $result = $this->userModel->register($data);

        if ($result["success"]) {
            Toast::success("Tạo người dùng thành công");
            $this->redirect("/admin/users");
        } else {
            Toast::error($result["message"]);
            $this->redirect("/admin/users/create");
        }
    }

    /**
     * Hiển thị form edit user
     */
    public function showFormEdit($id)
    {
        // Lấy user với thống kê
        $user = $this->userModel->getUserWithStats($id);

        if (!$user) {
            Toast::error("Không tìm thấy người dùng");
            $this->redirect("/admin/users");
            return;
        }

        $this->viewWithLayout("admin/manage_user/user_edit", [
            "user" => $user,
            "pageTitle" => "Chỉnh sửa người dùng",
            "needUsers" => true,

            "csrfToken" => Security::generateCSRFToken()
        ], "layouts/admin_layout");
    }

    /**
     * Cập nhật user
     */
    public function update($id)
    {
        $this->validateMethod("POST");

        if (!$this->validateCSRF()) {
            Toast::error("Invalid CSRF token");
            $this->redirect("/admin/users/update/" . $id);
            return;
        }

        // Validate role change permission
        $role = $this->input("role", "user");
        if ($id == Session::getUserId() && $role !== Session::getUserRole()) {
            Toast::error("Không thể thay đổi role của chính bạn");
            $this->redirect("/admin/users/update/" . $id);
            return;
        }

        // Get current user data
        $currentUser = $this->userModel->getUserWithStats($id);
        if (!$currentUser) {
            Toast::error("Không tìm thấy người dùng");
            $this->redirect("/admin/users");
            return;
        }

        // Prepare update data
        $updateData = [
            "first_name" => Security::sanitize($this->input("first_name")),
            "last_name" => Security::sanitize($this->input("last_name")),
            "email" => Security::sanitize($this->input("email")),
            "role" => $role
        ];

        // Add password if provided
        $password = $this->input("password");
        if (!empty($password)) {
            $updateData["password"] = $password;
            $updateData["password_confirm"] = $this->input("password_confirm");
        }

        // Handle avatar removal
        $removeAvatar = $this->input("remove_avatar", "0");
        if ($removeAvatar === "1") {
            // Delete old avatar file if exists
            if (!empty($currentUser["avatar"]) && file_exists($currentUser["avatar"])) {
                @unlink($currentUser["avatar"]);
            }
            $updateData["avatar"] = null;
        }
        // Handle avatar upload
        else if (isset($_FILES["avatar"]) && $_FILES["avatar"]["error"] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFile("avatar", "uploads/avatars/");
            if ($uploadResult["success"]) {
                // Delete old avatar if exists
                if (!empty($currentUser["avatar"]) && file_exists($currentUser["avatar"])) {
                    @unlink($currentUser["avatar"]);
                }
                $updateData["avatar"] = $uploadResult["path"];
            } else {
                Toast::error($uploadResult["message"] ?? "Lỗi upload ảnh");
                $this->redirect("/admin/users/update/" . $id);
                return;
            }
        }

        // Update through Model
        $result = $this->userModel->updateUser($id, $updateData);

        if ($result["success"]) {
            // Update session if editing own profile
            if ($id == Session::getUserId()) {
                // Lấy thông tin user mới nhất từ database
                $updatedUser = $this->userModel->getUserById($id);

                $userData = Session::getUserData();
                $userData["first_name"] = $updatedUser["first_name"];
                $userData["last_name"] = $updatedUser["last_name"];
                $userData["email"] = $updatedUser["email"];
                $userData["avatar"] = $updatedUser["avatar"] ?? "";
                Session::set("user_data", $userData);
            }

            Toast::success("Cập nhật người dùng thành công");
        } else {
            Toast::error($result["message"]);
        }

        $this->redirect("/admin/users/update/" . $id);
    }

    /**
     * Xóa user
     */
    public function delete($id)
    {
        $this->validateMethod("POST");

        if (!$this->validateCSRF()) {
            Toast::error("Token không hợp lệ");
            $this->redirect("/admin/users");
            return;
        }

        // Delete through Model
        $result = $this->userModel->deleteUser($id, Session::getUserId());

        if ($result["success"]) {
            Toast::success("Xóa người dùng thành công");
            $this->redirect("/admin/users");
        } else {
            Toast::error($result["message"]);
            $this->redirect("/admin/users");
        }
    }
}
