<?php

/**
 * Admin Comment Controller
 * Xử lý quản lý bình luận trong admin panel
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/PostModel.php';

class AdminCommentController extends BaseController
{
    private $commentModel;
    private $postModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->commentModel = new CommentModel();
        $this->postModel = new PostModel();
    }

    /**
     * Hiển thị danh sách comments
     */
    public function comments()
    {
        // Get filter params
        $status = $_GET['status'] ?? 'all';
        $postId = $_GET['post_id'] ?? null;
        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 20;

        // Get comments based on filters
        $comments = $this->commentModel->getFilteredComments($status, $postId, $search, $page, $perPage);

        // Get statistics
        $stats = $this->commentModel->getStats();

        // Get recent posts for filter
        $recentPosts = $this->postModel->getRecentPosts(20);

        $this->viewWithLayout('admin/manage_comments/comment', [
            'comments' => $comments['items'] ?? [],
            'pagination' => $comments,
            'stats' => $stats,
            'recentPosts' => $recentPosts,
            'currentStatus' => $status,
            'currentPostId' => $postId,
            'searchQuery' => $search,
            'pageTitle' => 'Quản lý bình luận',
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    /**
     * Approve comment
     */
    public function approveComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            Router::redirect('/admin/comments');
            return;
        }

        if ($this->commentModel->approve($id)) {
            Toast::success('Phê duyệt bình luận thành công');
        } else {
            Toast::error('Có lỗi xảy ra khi phê duyệt');
        }

        Router::redirect('/admin/comments');
    }

    /**
     * Unapprove comment
     */
    public function unapproveComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::generateCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            Router::redirect('/admin/comments');
            return;
        }

        if ($this->commentModel->unapprove($id)) {
            Toast::success('Đã ẩn bình luận');
        } else {
            Toast::error('Có lỗi xảy ra');
        }

        Router::redirect('/admin/comments');
    }

    /**
     * Delete comment
     */
    public function deleteComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::generateCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            Router::redirect('/admin/comments');
            return;
        }

        // Check if has replies
        $replyCount = $this->commentModel->countReplies($id);

        if ($this->commentModel->delete($id)) {
            $message = $replyCount > 0
                ? "Đã xóa bình luận và {$replyCount} câu trả lời"
                : 'Xóa bình luận thành công';
            Toast::success($message);
        } else {
            Toast::error('Có lỗi xảy ra khi xóa');
        }

        Router::redirect('/admin/comments');
    }

    /**
     * Bulk approve
     */
    public function bulkApprove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::generateCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        $ids = $_POST['comment_ids'] ?? [];

        if (empty($ids)) {
            Toast::error('Vui lòng chọn ít nhất một bình luận');
            Router::redirect('/admin/comments');
            return;
        }

        $count = 0;
        foreach ($ids as $id) {
            if ($this->commentModel->approve($id)) {
                $count++;
            }
        }

        Toast::success("Đã phê duyệt {$count} bình luận");
        Router::redirect('/admin/comments');
    }

    /**
     * Bulk delete
     */
    public function bulkDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::generateCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            Router::redirect('/admin/comments');
            return;
        }

        $ids = $_POST['comment_ids'] ?? [];

        if (empty($ids)) {
            Toast::error('Vui lòng chọn ít nhất một bình luận');
            Router::redirect('/admin/comments');
            return;
        }

        $count = 0;
        foreach ($ids as $id) {
            if ($this->commentModel->delete($id)) {
                $count++;
            }
        }

        Toast::success("Đã xóa {$count} bình luận");
        Router::redirect('/admin/comments');
    }



    /**
     * Edit comment content
     */
    public function editComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Phương thức không hợp lệ'], 400);
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Token không hợp lệ'], 403);
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy bình luận'], 404);
            return;
        }

        $content = trim($_POST['content'] ?? '');
        if (empty($content)) {
            $this->json(['success' => false, 'message' => 'Nội dung không được trống'], 400);
            return;
        }

        $content = Security::sanitize($content);

        if ($this->commentModel->update($id, $content)) {
            $this->json([
                'success' => true,
                'message' => 'Cập nhật bình luận thành công',
                'content' => $content
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * View comment detail (AJAX)
     */
    public function viewComment($id)
    {
        $comment = $this->commentModel->getById($id);

        if (!$comment) {
            $this->json(['success' => false, 'message' => 'Không tìm thấy bình luận'], 404);
            return;
        }

        // Get post info
        $post = $this->postModel->getById($comment['post_id']);

        // Get replies
        $replies = $this->commentModel->getRepliesWithUser($id);

        $this->json([
            'success' => true,
            'comment' => $comment,
            'post' => $post,
            'replies' => $replies
        ]);
    }
}
