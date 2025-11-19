<?php

/**
 * Admin Controller
 * Xử lý admin dashboard và quản lý hệ thống
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Tag.php';

class AdminController extends BaseController
{
    public function __construct()
    {
        // Tất cả actions trong AdminController yêu cầu admin
        // Được gọi TRƯỚC mọi method
        $this->requireAdmin();
    }

    /**
     * Dashboard - Trang chủ admin
     */
    public function dashboard()
    {
        $postModel = new Post();
        $commentModel = new Comment();
        $userModel = new User();

        // Lấy thống kê tổng quan
        $stats = [
            'total_posts' => $this->getTotalPosts(),
            'total_comments' => $this->getTotalComments(),
            'total_users' => $this->getTotalUsers(),
            'pending_comments' => $this->getPendingCommentsCount(),
            'draft_posts' => $this->getDraftPostsCount(),
            'published_posts' => $postModel->countPublishedPosts()
        ];

        // Recent activities
        $recentPosts = $postModel->getByUser(Session::getUserId(), 1, 5);
        $recentComments = $commentModel->getRecent(10, false);
        $pendingComments = $commentModel->getPendingApproval(5);

        // Chart data - Bài viết theo tháng (6 tháng gần nhất)
        $postsByMonth = $this->getPostsByMonth(6);

        $this->viewWithLayout('admin/dashboard', [
            'stats' => $stats,
            'recentPosts' => $recentPosts,
            'recentComments' => $recentComments,
            'pendingComments' => $pendingComments,
            'postsByMonth' => $postsByMonth,
            'pageTitle' => 'Admin Dashboard',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Quản lý bài viết
     */
    public function posts()
    {
        $postModel = new Post();

        $page = $this->input('page', 1);
        $status = $this->input('status', '');
        $search = $this->input('search', '');
        $perPage = 20;

        // Lấy danh sách bài viết
        $posts = $this->getPostsForAdmin($page, $perPage, $status, $search);
        $totalPosts = $this->countPostsForAdmin($status, $search);

        // Pagination
        $pagination = $this->paginate($totalPosts, $page, $perPage);

        $this->viewWithLayout('admin/posts', [
            'posts' => $posts,
            'pagination' => $pagination,
            'currentStatus' => $status,
            'searchKeyword' => $search,
            'pageTitle' => 'Quản lý bài viết',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Quản lý comments
     */
    public function comments()
    {
        $commentModel = new Comment();

        $page = $this->input('page', 1);
        $status = $this->input('status', '');
        $perPage = 20;

        // Lấy comments
        if ($status === 'pending') {
            $comments = $commentModel->getPendingApproval(1000); // Get all pending
            $totalComments = count($comments);
            // Paginate manually
            $offset = ($page - 1) * $perPage;
            $comments = array_slice($comments, $offset, $perPage);
        } else {
            $comments = $commentModel->getRecent($perPage * $page, $status !== 'approved');
            $totalComments = $this->getTotalComments();
        }

        $pagination = $this->paginate($totalComments, $page, $perPage);

        // Stats
        $commentStats = $commentModel->getStats();

        $this->viewWithLayout('admin/comments', [
            'comments' => $comments,
            'pagination' => $pagination,
            'currentStatus' => $status,
            'stats' => $commentStats,
            'pageTitle' => 'Quản lý bình luận',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Quản lý users
     */
    public function users()
    {
        $page = $this->input('page', 1);
        $perPage = 20;

        $users = $this->getAllUsers($page, $perPage);
        $totalUsers = $this->getTotalUsers();

        $pagination = $this->paginate($totalUsers, $page, $perPage);

        $this->viewWithLayout('admin/users', [
            'users' => $users,
            'pagination' => $pagination,
            'pageTitle' => 'Quản lý người dùng',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Quản lý categories
     */
    public function categories()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $this->viewWithLayout('admin/categories', [
            'categories' => $categories,
            'pageTitle' => 'Quản lý danh mục',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Tạo category mới
     */
    public function createCategory()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $name = Security::sanitize($this->input('name'));
        $description = Security::sanitize($this->input('description'));

        if (empty($name)) {
            $this->json(['success' => false, 'message' => 'Tên danh mục không được trống'], 400);
            return;
        }

        $slug = Security::createSlug($name);

        $categoryModel = new Category();
        $result = $categoryModel->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description
        ]);

        if ($result['success']) {
            Session::flash('success', 'Tạo danh mục thành công');
            $this->json(['success' => true, 'message' => 'Tạo danh mục thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể tạo danh mục'], 500);
        }
    }

    /**
     * Xóa category
     */
    public function deleteCategory($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $categoryModel = new Category();

        if ($categoryModel->delete($id)) {
            Session::flash('success', 'Xóa danh mục thành công');
            $this->json(['success' => true, 'message' => 'Xóa danh mục thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể xóa danh mục'], 500);
        }
    }

    /**
     * Quản lý tags
     */
    public function tags()
    {
        $tagModel = new Tag();
        $tags = $tagModel->getAll();

        $this->viewWithLayout('admin/tags', [
            'tags' => $tags,
            'pageTitle' => 'Quản lý tags',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Tạo tag mới
     */
    public function createTag()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $name = Security::sanitize($this->input('name'));

        if (empty($name)) {
            $this->json(['success' => false, 'message' => 'Tên tag không được trống'], 400);
            return;
        }

        $slug = Security::createSlug($name);

        $tagModel = new Tag();
        $result = $tagModel->create([
            'name' => $name,
            'slug' => $slug
        ]);

        if ($result['success']) {
            Session::flash('success', 'Tạo tag thành công');
            $this->json(['success' => true, 'message' => 'Tạo tag thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể tạo tag'], 500);
        }
    }

    /**
     * Xóa tag
     */
    public function deleteTag($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $tagModel = new Tag();
        $force = $this->input('force') === 'true';

        $result = $tagModel->safeDelete($id, $force);

        if ($result['success']) {
            Session::flash('success', $result['message']);
            $this->json($result);
        } else {
            // Nếu tag đang được sử dụng, trả về thông tin để confirm
            if ($result['in_use'] ?? false) {
                $this->json($result, 409); // 409 Conflict
            } else {
                Session::flash('error', $result['message']);
                $this->json($result, 500);
            }
        }
    }

    /**
     * Bulk delete tags
     */
    public function bulkDeleteTags()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $tagIds = $this->input('tag_ids', []);

        if (empty($tagIds)) {
            $this->json(['success' => false, 'message' => 'Chưa chọn tag nào'], 400);
            return;
        }

        $tagModel = new Tag();
        $result = $tagModel->bulkDelete($tagIds);

        if ($result['success']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }

        $this->json($result);
    }

    /**
     * Bulk action cho posts
     */
    public function bulkActionPosts()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $action = $this->input('action');
        $postIds = $this->input('post_ids', []);

        if (empty($postIds)) {
            $this->json(['success' => false, 'message' => 'Chưa chọn bài viết nào'], 400);
            return;
        }

        $postModel = new Post();
        $count = 0;

        foreach ($postIds as $postId) {
            switch ($action) {
                case 'delete':
                    if ($postModel->delete($postId)) {
                        $count++;
                    }
                    break;
                case 'publish':
                    if ($postModel->update($postId, ['status' => 'published'])) {
                        $count++;
                    }
                    break;
                case 'draft':
                    if ($postModel->update($postId, ['status' => 'draft'])) {
                        $count++;
                    }
                    break;
            }
        }

        Session::flash('success', "Đã xử lý {$count} bài viết");
        $this->json(['success' => true, 'message' => "Đã xử lý {$count} bài viết"]);
    }

    /**
     * Bulk action cho comments
     */
    public function bulkActionComments()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $action = $this->input('action');
        $commentIds = $this->input('comment_ids', []);

        if (empty($commentIds)) {
            $this->json(['success' => false, 'message' => 'Chưa chọn comment nào'], 400);
            return;
        }

        $commentModel = new Comment();
        $count = 0;

        foreach ($commentIds as $commentId) {
            switch ($action) {
                case 'approve':
                    if ($commentModel->approve($commentId)) {
                        $count++;
                    }
                    break;
                case 'unapprove':
                    if ($commentModel->unapprove($commentId)) {
                        $count++;
                    }
                    break;
                case 'delete':
                    if ($commentModel->delete($commentId)) {
                        $count++;
                    }
                    break;
            }
        }

        Session::flash('success', "Đã xử lý {$count} bình luận");
        $this->json(['success' => true, 'message' => "Đã xử lý {$count} bình luận"]);
    }

    /**
     * Thay đổi role user
     */
    public function changeUserRole()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $userId = $this->input('user_id');
        $role = $this->input('role');

        if (!in_array($role, ['user', 'admin'])) {
            $this->json(['success' => false, 'message' => 'Role không hợp lệ'], 400);
            return;
        }

        // Không cho phép thay đổi role của chính mình
        if ($userId == Session::getUserId()) {
            $this->json(['success' => false, 'message' => 'Không thể thay đổi role của chính bạn'], 400);
            return;
        }

        $database = new Database();
        $conn = $database->connect();

        $query = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            Session::flash('success', 'Cập nhật role thành công');
            $this->json(['success' => true, 'message' => 'Cập nhật role thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể cập nhật role'], 500);
        }
    }

    /**
     * Xóa user
     */
    public function deleteUser($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        // Không cho phép xóa chính mình
        if ($id == Session::getUserId()) {
            $this->json(['success' => false, 'message' => 'Không thể xóa chính bạn'], 400);
            return;
        }

        $database = new Database();
        $conn = $database->connect();

        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            Session::flash('success', 'Xóa user thành công');
            $this->json(['success' => true, 'message' => 'Xóa user thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể xóa user'], 500);
        }
    }

    /**
     * Tạo bài viết mới - Form
     */
    public function createPost()
    {
        $categoryModel = new Category();
        $tagModel = new Tag();

        $this->viewWithLayout('admin/posts-create', [
            'pageTitle' => 'Tạo bài viết mới',
            'categories' => $categoryModel->getAll(),
            'tags' => $tagModel->getAll(),
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Lưu bài viết mới
     */
    public function storePost()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/posts/create');
            return;
        }

        $postModel = new Post();

        // Handle cover image upload
        $coverImage = '';
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFile('cover_image', 'uploads/posts/');
            if ($uploadResult['success']) {
                $coverImage = $uploadResult['path'];
            }
        }

        $data = [
            'user_id' => Session::getUserId(),
            'category_id' => $this->input('category_id'),
            'title' => Security::sanitize($this->input('title')),
            'slug' => $this->input('slug') ?: Security::createSlug($this->input('title')),
            'excerpt' => Security::sanitize($this->input('excerpt')),
            'content' => $this->input('content'), // Không sanitize HTML content
            'cover_image' => $coverImage,
            'status' => $this->input('status', 'draft'),
            'tags' => $this->input('tags', [])
        ];

        $result = $postModel->create($data);

        if ($result['success']) {
            Session::flash('success', 'Tạo bài viết thành công');
            $this->redirect('/admin/posts/edit/' . $result['post_id']);
        } else {
            Session::flash('error', $result['message']);
            $this->redirect('/admin/posts/create');
        }
    }

    /**
     * Sửa bài viết - Form
     */
    public function editPost($id)
    {
        $postModel = new Post();
        $categoryModel = new Category();
        $tagModel = new Tag();

        $post = $postModel->getById($id);

        if (!$post) {
            Session::flash('error', 'Không tìm thấy bài viết');
            $this->redirect('/admin/posts');
            return;
        }

        // Check ownership (non-admin can only edit their own posts)
        if (!Session::isAdmin() && $post['user_id'] != Session::getUserId()) {
            Session::flash('error', 'Bạn không có quyền sửa bài viết này');
            $this->redirect('/admin/posts');
            return;
        }

        $this->viewWithLayout('admin/posts-edit', [
            'pageTitle' => 'Sửa bài viết',
            'post' => $post,
            'categories' => $categoryModel->getAll(),
            'tags' => $tagModel->getAll(),
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Cập nhật bài viết
     */
    public function updatePost($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/posts/edit/' . $id);
            return;
        }

        $postModel = new Post();
        $post = $postModel->getById($id);

        if (!$post) {
            Session::flash('error', 'Không tìm thấy bài viết');
            $this->redirect('/admin/posts');
            return;
        }

        // Check ownership
        if (!Session::isAdmin() && $post['user_id'] != Session::getUserId()) {
            Session::flash('error', 'Bạn không có quyền sửa bài viết này');
            $this->redirect('/admin/posts');
            return;
        }

        // Handle cover image upload
        $coverImage = $post['cover_image'];
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadFile('cover_image', 'uploads/posts/');
            if ($uploadResult['success']) {
                $coverImage = $uploadResult['path'];
            }
        }

        $data = [
            'category_id' => $this->input('category_id'),
            'title' => Security::sanitize($this->input('title')),
            'slug' => $this->input('slug') ?: Security::createSlug($this->input('title')),
            'excerpt' => Security::sanitize($this->input('excerpt')),
            'content' => $this->input('content'),
            'cover_image' => $coverImage,
            'status' => $this->input('status', 'draft'),
            'tags' => $this->input('tags', [])
        ];

        $result = $postModel->update($id, $data);

        if ($result['success']) {
            Session::flash('success', 'Cập nhật bài viết thành công');
            $this->redirect('/admin/posts/edit/' . $id);
        } else {
            Session::flash('error', $result['message']);
            $this->redirect('/admin/posts/edit/' . $id);
        }
    }

    /**
     * Xóa bài viết
     */
    public function deletePost($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/posts');
            return;
        }

        $postModel = new Post();
        $post = $postModel->getById($id);

        if (!$post) {
            Session::flash('error', 'Không tìm thấy bài viết');
            $this->redirect('/admin/posts');
            return;
        }

        // LOGIC: Admin có thể xóa mọi bài viết
        // User thường chỉ xóa bài của mình
        if (!Session::isAdmin() && $post['user_id'] != Session::getUserId()) {
            Session::flash('error', 'Bạn không có quyền xóa bài viết này');
            $this->redirect('/admin/posts');
            return;
        }

        // Xóa bài viết
        if ($postModel->delete($id)) {
            Session::flash('success', 'Xóa bài viết thành công');
        } else {
            Session::flash('error', 'Không thể xóa bài viết');
        }

        $this->redirect('/admin/posts');
    }

    /**
     * Cập nhật category
     */
    public function updateCategory($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $categoryModel = new Category();

        $data = [
            'name' => Security::sanitize($this->input('name')),
            'slug' => $this->input('slug') ?: Security::createSlug($this->input('name')),
            'description' => Security::sanitize($this->input('description'))
        ];

        $result = $categoryModel->update($id, $data);

        if ($result['success']) {
            Session::flash('success', 'Cập nhật danh mục thành công');
            $this->json(['success' => true, 'message' => 'Cập nhật danh mục thành công']);
        } else {
            $this->json(['success' => false, 'message' => $result['message']], 500);
        }
    }

    /**
     * Cập nhật tag
     */
    public function updateTag($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $tagModel = new Tag();

        $data = [
            'name' => Security::sanitize($this->input('name')),
            'slug' => $this->input('slug') ?: Security::createSlug($this->input('name'))
        ];

        $result = $tagModel->update($id, $data);

        if ($result['success']) {
            Session::flash('success', 'Cập nhật tag thành công');
            $this->json(['success' => true, 'message' => 'Cập nhật tag thành công']);
        } else {
            $this->json(['success' => false, 'message' => $result['message']], 500);
        }
    }

    /**
     * Approve comment
     */
    public function approveComment($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $commentModel = new Comment();

        if ($commentModel->approve($id)) {
            Session::flash('success', 'Đã duyệt bình luận');
            $this->json(['success' => true, 'message' => 'Đã duyệt bình luận']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể duyệt bình luận'], 500);
        }
    }

    /**
     * Xóa comment
     */
    public function deleteComment($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            return;
        }

        $commentModel = new Comment();

        if ($commentModel->delete($id)) {
            Session::flash('success', 'Xóa bình luận thành công');
            $this->json(['success' => true, 'message' => 'Xóa bình luận thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Không thể xóa bình luận'], 500);
        }
    }

    /**
     * Tạo user mới
     */
    public function createUser()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/users');
            return;
        }

        $userModel = new User();

        $data = [
            'fName' => Security::sanitize($this->input('first_name')),
            'lName' => Security::sanitize($this->input('last_name')),
            'email' => Security::sanitize($this->input('email')),
            'password' => $this->input('password'),
            'password_confirm' => $this->input('password')
        ];

        $result = $userModel->register($data);

        if ($result['success']) {
            // Update role if specified
            $role = $this->input('role', 'user');
            if ($role === 'admin') {
                $database = new Database();
                $conn = $database->connect();
                $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
                $stmt->execute([':id' => $result['user_id']]);
            }

            Session::flash('success', 'Tạo user thành công');
        } else {
            Session::flash('error', $result['message']);
        }

        $this->redirect('/admin/users');
    }

    /**
     * Cập nhật user
     */
    public function updateUser($id)
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/users');
            return;
        }

        $database = new Database();
        $conn = $database->connect();

        $firstName = Security::sanitize($this->input('first_name'));
        $lastName = Security::sanitize($this->input('last_name'));
        $email = Security::sanitize($this->input('email'));
        $role = $this->input('role', 'user');

        // Không cho phép tự thay đổi role của mình
        if ($id == Session::getUserId() && $role !== Session::getUserRole()) {
            Session::flash('error', 'Không thể thay đổi role của chính bạn');
            $this->redirect('/admin/users');
            return;
        }

        $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, 
                  email = :email, role = :role WHERE id = :id";
        $stmt = $conn->prepare($query);

        if ($stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':role' => $role,
            ':id' => $id
        ])) {
            Session::flash('success', 'Cập nhật user thành công');
        } else {
            Session::flash('error', 'Không thể cập nhật user');
        }

        $this->redirect('/admin/users');
    }

    /**
     * Cài đặt hệ thống
     */
    public function settings()
    {
        // Load current settings from database or config file
        $settings = $this->getSettings();

        $this->viewWithLayout('admin/settings', [
            'pageTitle' => 'Cài đặt hệ thống',
            'settings' => $settings,
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin');
    }

    /**
     * Cập nhật settings
     */
    public function updateSettings()
    {
        $this->validateMethod('POST');

        if (!$this->validateCSRF()) {
            Session::flash('error', 'Invalid CSRF token');
            $this->redirect('/admin/settings');
            return;
        }

        $database = new Database();
        $conn = $database->connect();

        $settings = [
            'site_name' => Security::sanitize($this->input('site_name')),
            'site_description' => Security::sanitize($this->input('site_description')),
            'posts_per_page' => (int)$this->input('posts_per_page', 10),
            'comment_moderation' => $this->input('comment_moderation') === 'on' ? 1 : 0
        ];

        foreach ($settings as $key => $value) {
            $query = "INSERT INTO settings (`key`, `value`) VALUES (:key, :value)
                      ON DUPLICATE KEY UPDATE `value` = :value";
            $stmt = $conn->prepare($query);
            $stmt->execute([':key' => $key, ':value' => $value]);
        }

        Session::flash('success', 'Cập nhật cài đặt thành công');
        $this->redirect('/admin/settings');
    }

    /**
     * Lấy settings từ database
     */
    private function getSettings()
    {
        $database = new Database();
        $conn = $database->connect();

        $query = "SELECT `key`, `value` FROM settings";
        $stmt = $conn->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Default values
        return array_merge([
            'site_name' => 'MyBlog',
            'site_description' => 'Blog cá nhân',
            'posts_per_page' => 10,
            'comment_moderation' => 1
        ], $results);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Lấy tổng số posts
     */
    private function getTotalPosts()
    {
        $database = new Database();
        $conn = $database->connect();
        $result = $conn->query("SELECT COUNT(*) as total FROM posts")->fetch();
        return $result['total'];
    }

    /**
     * Lấy tổng số comments
     */
    private function getTotalComments()
    {
        $database = new Database();
        $conn = $database->connect();
        $result = $conn->query("SELECT COUNT(*) as total FROM comments")->fetch();
        return $result['total'];
    }

    /**
     * Lấy tổng số users
     */
    private function getTotalUsers()
    {
        $database = new Database();
        $conn = $database->connect();
        $result = $conn->query("SELECT COUNT(*) as total FROM users")->fetch();
        return $result['total'];
    }

    /**
     * Lấy số comments chờ approve
     */
    private function getPendingCommentsCount()
    {
        $database = new Database();
        $conn = $database->connect();
        $result = $conn->query("SELECT COUNT(*) as total FROM comments WHERE is_approved = FALSE")->fetch();
        return $result['total'];
    }

    /**
     * Lấy số bài viết draft
     */
    private function getDraftPostsCount()
    {
        $database = new Database();
        $conn = $database->connect();
        $result = $conn->query("SELECT COUNT(*) as total FROM posts WHERE status = 'draft'")->fetch();
        return $result['total'];
    }

    /**
     * Lấy posts cho admin (với filter)
     */
    private function getPostsForAdmin($page, $perPage, $status, $search)
    {
        $database = new Database();
        $conn = $database->connect();

        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];

        if ($status) {
            $conditions[] = "p.status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $conditions[] = "(p.title LIKE :search OR p.content LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $query = "SELECT p.*, u.name as author_name, c.name as category_name
                  FROM posts p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN categories c ON p.category_id = c.id
                  {$whereClause}
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm posts cho admin
     */
    private function countPostsForAdmin($status, $search)
    {
        $database = new Database();
        $conn = $database->connect();

        $conditions = [];
        $params = [];

        if ($status) {
            $conditions[] = "status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $conditions[] = "(title LIKE :search OR content LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $query = "SELECT COUNT(*) as total FROM posts {$whereClause}";
        $stmt = $conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Lấy tất cả users
     */
    private function getAllUsers($page, $perPage)
    {
        $database = new Database();
        $conn = $database->connect();

        $offset = ($page - 1) * $perPage;

        $query = "SELECT u.*, 
                         COUNT(DISTINCT p.id) as post_count,
                         COUNT(DISTINCT c.id) as comment_count
                  FROM users u
                  LEFT JOIN posts p ON u.id = p.user_id
                  LEFT JOIN comments c ON u.id = c.user_id
                  GROUP BY u.id
                  ORDER BY u.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy thống kê posts theo tháng
     */
    private function getPostsByMonth($months = 6)
    {
        $database = new Database();
        $conn = $database->connect();

        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
                  FROM posts
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL :months MONTH)
                  GROUP BY month
                  ORDER BY month ASC";

        $stmt = $conn->prepare($query);
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
