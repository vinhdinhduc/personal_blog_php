<?php

/**
 * Admin Controller
 * Xử lý admin dashboard và quản lý hệ thống
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/TagModel.php';

class AdminController extends BaseController
{
    private $userModel;
    private $postModel;
    private $commentModel;

    public function __construct()
    {
        // Tất cả actions trong AdminController yêu cầu admin
        $this->requireAdmin();

        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->commentModel = new CommentModel();
    }

    // Hiển thị dashboard admin
    public function dashboard()
    {
        // Lấy thống kê tổng quan qua Model
        $stats = [
            'total_posts' => $this->postModel->count(),
            'total_comments' => $this->commentModel->count(),
            'total_users' => $this->userModel->getTotalUsers(),
            'pending_comments' => $this->commentModel->count(['is_approved' => 0]),
            'draft_posts' => $this->postModel->count(['status' => 'draft']),
            'published_posts' => $this->postModel->count(['status' => 'published'])
        ];

        // Cập nhật session với thông tin thông báo
        $_SESSION['pending_comments_count'] = $stats['pending_comments'];

        $this->viewWithLayout('admin/dashboard', [
            'stats' => $stats,
            'pageTitle' => 'Admin Dashboard',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }
}
