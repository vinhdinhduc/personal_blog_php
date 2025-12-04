<?php

//Xử lý quản lý bình luận trong admin panel

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


    // Hiển thị danh sách comments

    public function comments()
    {
        // Lấy bộ lọc từ query parameters
        $status = $_GET['status'] ?? 'all';
        $postId = $_GET['post_id'] ?? null;
        $search = $_GET['search'] ?? '';
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 20;

        // Lấy danh sách bình luận dựa trên bộ lọc
        $comments = $this->commentModel->getFilteredComments($status, $postId, $search, $page, $perPage);

        // Lấy thống kê
        $stats = $this->commentModel->getStats();

        // Lấy bài viết gần đây để lọc
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

            "needComments" => true,
            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    public function showEditComment($id)
    {
        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        $this->viewWithLayout('admin/manage_comments/edit', [
            'comment' => $comment,
            'pageTitle' => 'Sửa bình luận',
            "needComments" => true,

            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }

    // Phê duyệt bình luận
    public function approveComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        if ($this->commentModel->approve($id)) {
            Toast::success('Phê duyệt bình luận thành công');
        } else {
            Toast::error('Có lỗi xảy ra khi phê duyệt');
        }

        $this->redirect('/admin/comments');
    }

    // Ân bình luận
    public function unapproveComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        if ($this->commentModel->unapprove($id)) {
            Toast::success('Đã ẩn bình luận');
        } else {
            Toast::error('Có lỗi xảy ra');
        }

        $this->redirect('/admin/comments');
    }

    /**
     * Delete comment
     */
    public function deleteComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        // Đếm số câu trả lời để thông báo
        $replyCount = $this->commentModel->countReplies($id);

        if ($this->commentModel->delete($id)) {
            $message = $replyCount > 0
                ? "Đã xóa bình luận và {$replyCount} câu trả lời"
                : 'Xóa bình luận thành công';
            Toast::success($message);
        } else {
            Toast::error('Có lỗi xảy ra khi xóa');
        }

        $this->redirect('/admin/comments');
    }

    //
    // Phê duyệt nhiều bình luận
    //
    public function bulkApprove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        $ids = $_POST['comment_ids'] ?? [];

        if (empty($ids)) {
            Toast::error('Vui lòng chọn ít nhất một bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        $count = 0;
        foreach ($ids as $id) {
            if ($this->commentModel->approve($id)) {
                $count++;
            }
        }

        Toast::success("Đã phê duyệt {$count} bình luận");
        $this->redirect('/admin/comments');
    }

    // Xóa nhiều bình luận
    //
    public function bulkDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        $ids = $_POST['comment_ids'] ?? [];

        if (empty($ids)) {
            Toast::error('Vui lòng chọn ít nhất một bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        $count = 0;
        foreach ($ids as $id) {
            if ($this->commentModel->delete($id)) {
                $count++;
            }
        }

        Toast::success("Đã xóa {$count} bình luận");
        $this->redirect('/admin/comments');
    }



    //Sửa comment
    public function editComment($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Toast::error('Phương thức không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        // Validate CSRF
        if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            Toast::error('Token không hợp lệ');
            $this->redirect('/admin/comments');
            return;
        }

        $comment = $this->commentModel->getById($id);
        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        $content = trim($_POST['content'] ?? '');
        if (empty($content)) {
            Toast::error('Nội dung không được trống');
            $this->redirect('/admin/comments');
            return;
        }

        $content = Security::sanitize($content);

        if ($this->commentModel->update($id, $content)) {
            Toast::success('Cập nhật bình luận thành công');
            $this->redirect('/admin/comments');
            return;
        } else {
            Toast::error('Có lỗi xảy ra khi cập nhật bình luận');
            $this->redirect('/admin/comments');
            return;
        }
    }

    //
    // View comment detail 
    //
    public function viewComment($id)
    {
        $comment = $this->commentModel->getById($id);

        if (!$comment) {
            Toast::error('Không tìm thấy bình luận');
            $this->redirect('/admin/comments');
            return;
        }

        // Lấy thông tin bài viết
        $post = $this->postModel->getById($comment['post_id']);

        // Lấy câu trả lời
        $replies = $this->commentModel->getRepliesWithUser($id);

        $this->viewWithLayout('admin/manage_comments/view_detail', [
            'comment' => $comment,
            'post' => $post,
            'replies' => $replies,
            'pageTitle' => 'Chi tiết bình luận',
            "needComments" => true,

            'csrfToken' => Security::generateCSRFToken()
        ], 'layouts/admin_layout');
    }
}
