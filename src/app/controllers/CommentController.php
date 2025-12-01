<?php

/**
 * Comment Controller
 * Xử lý CRUD comments và threaded replies
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/PostModel.php';

class CommentController extends BaseController
{

    /**
     * Tạo comment mới
     */
    public function create()
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        // Validate CSRF
        if (!$this->validateCSRF()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            } else {
                Session::flash('error', 'Invalid request');
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            return;
        }

        // Rate limiting - chống spam comment
        $rateLimitKey = 'comment_' . Session::getUserId();
        if (!Security::rateLimit($rateLimitKey, 10, 300)) {
            $message = 'Bạn đang bình luận quá nhanh. Vui lòng chờ 5 phút';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $message], 429);
            } else {
                Session::flash('error', $message);
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            return;
        }

        $commentModel = new CommentModel();
        $postModel = new PostModel();

        // Get input
        $postId = (int)$this->input('post_id');
        $parentId = $this->input('parent_id') ? (int)$this->input('parent_id') : null;
        $content = trim($this->input('content'));

        // Validate
        if (empty($content)) {
            $message = 'Nội dung bình luận không được trống';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $message], 400);
            } else {
                Session::flash('error', $message);
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            return;
        }

        if (strlen($content) < 3) {
            $message = 'Bình luận phải có ít nhất 3 ký tự';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $message], 400);
            } else {
                Session::flash('error', $message);
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            return;
        }

        if (strlen($content) > 5000) {
            $message = 'Bình luận quá dài (tối đa 5000 ký tự)';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $message], 400);
            } else {
                Session::flash('error', $message);
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
            return;
        }

        // Kiểm tra post tồn tại
        $post = $postModel->getById($postId);
        if (!$post) {
            $message = 'Bài viết không tồn tại';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $message], 404);
            } else {
                Session::flash('error', $message);
                $this->redirect('/');
            }
            return;
        }

        // Kiểm tra parent comment nếu có (reply)
        if ($parentId) {
            $parentComment = $commentModel->getById($parentId);
            if (!$parentComment) {
                $message = 'Comment cha không tồn tại';
                if ($this->isAjax()) {
                    $this->json(['success' => false, 'message' => $message], 404);
                } else {
                    Session::flash('error', $message);
                    $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
                }
                return;
            }

            // Giới hạn độ sâu của comment tree (tối đa 3 cấp)
            $depth = $commentModel->getDepth($parentId);
            if ($depth >= 2) {
                $message = 'Không thể trả lời comment quá sâu (tối đa 3 cấp)';
                if ($this->isAjax()) {
                    $this->json(['success' => false, 'message' => $message], 400);
                } else {
                    Session::flash('error', $message);
                    $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
                }
                return;
            }
        }

        // Sanitize content
        $content = Security::sanitize($content);

        // Auto-approve cho admin và author của bài viết
        $isAdmin = Session::isAdmin();
        $isAuthor = Session::getUserId() == $post['user_id'];
        $autoApprove = $isAdmin || $isAuthor;

        // Create comment
        $data = [
            'post_id' => $postId,
            'user_id' => Session::getUserId(),
            'parent_id' => $parentId,
            'content' => $content,
            'is_approved' => $autoApprove
        ];

        $result = $commentModel->create($data);

        if ($result['success']) {
            $message = $autoApprove
                ? 'Bình luận thành công!'
                : 'Bình luận của bạn đang chờ phê duyệt';

            if ($this->isAjax()) {
                // Return comment data for AJAX
                $comment = $commentModel->getById($result['comment_id']);
                $this->json([
                    'success' => true,
                    'message' => $message,
                    'comment' => $comment,
                    'needs_approval' => $result['needs_approval']
                ]);
            } else {
                Session::flash('success', $message);
                $this->redirect('/post/' . $post['slug'] . '#comment-' . $result['comment_id']);
            }
        } else {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $result['message']], 500);
            } else {
                Session::flash('error', $result['message']);
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            }
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $this->requireAuth();

        $commentModel = new CommentModel();
        $comment = $commentModel->getById($id);

        if (!$comment) {
            Session::flash('error', 'Comment không tồn tại');
            $this->redirect('/');
            return;
        }

        if (!$this->canModify($comment)) {
            Session::flash('error', 'Bạn không có quyền sửa comment này');
            $this->redirect('/');
            return;
        }

        $postSlug = $this->getPostSlugFromComment($comment);

        // Redirect về post với edit mode
        $this->redirect('/post/' . $postSlug . '?edit_comment=' . $id . '#comment-' . $id);
    }
    private function getPostSlugFromComment($comment)
    {
        $postModel = new PostModel();
        $post = $postModel->getById($comment['post_id']);
        return $post ? $post['slug'] : '';
    }

    /**
     * Cập nhật comment (chỉ owner)
     * @param int $id
     */
    public function update($id)
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        $commentModel = new CommentModel();
        $comment = $commentModel->getById($id);
        $postSlug = $this->getPostSlugFromComment($comment);

        if (!$comment) {
            Toast::error('Comment không tồn tại');
            $this->redirect('/post/' . $postSlug);
            return;
        }

        // Chỉ owner hoặc admin mới được sửa
        if (!$this->canModify($comment)) {
            Toast::error('Bạn không có quyền sửa comment này');
            $this->redirect('/post/' . $postSlug);
            return;
        }

        // Validate CSRF
        if (!$this->validateCSRF()) {
            Toast::error('Yêu cầu không hợp lệ');
            $this->redirect('/post/' . $postSlug);
            return;
        }

        $content = trim($this->input('content'));

        if (empty($content)) {
            Toast::error('Nội dung không được trống');
            $this->redirect('/post/' . $postSlug);
            return;
        }
        if (strlen($content) < 3) {
            Toast::error('Nội dung phải có ít nhất 3 ký tự');
            $this->redirect('/post/' . $postSlug);
            return;
        }

        $content = Security::sanitize($content);

        if ($commentModel->update($id, $content)) {
            Toast::success('success', 'Cập nhật comment thành công');
            $this->redirect('/post/' . $postSlug . '#comment-' . $id);
        } else {
            Toast::error('Không thể cập nhật comment',);
            $this->redirect('/post/' . $postSlug);
        }
    }

    /**
     * Xóa comment
     * @param int $id
     */
    public function delete($id)
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        $commentModel = new CommentModel();
        $comment = $commentModel->getById($id);
        $postSlug = $this->getPostSlugFromComment($comment);
        if (!$comment) {
            Toast::error('Comment không tồn tại');
            $this->redirect('/post/' . $postSlug);
            return;
        }

        // Kiểm tra quyền xóa
        if (!$this->canDelete($comment)) {
            Toast::error('Bạn không có quyền xóa comment này');
            $this->redirect('/post/' . $postSlug);
            return;
        }

        // Validate CSRF
        if (!$this->validateCSRF()) {
            Toast::error('Yêu cầu không hợp lệ');
            $this->redirect('/post/' . $postSlug);
            return;
        }

        // Kiểm tra có replies không
        $replyCount = $commentModel->countReplies($id);
        if ($replyCount > 0) {
            Toast::error("Comment này có {$replyCount} câu trả lời. Xóa sẽ xóa tất cả replies.");
            $this->redirect('/post/' . $postSlug);
            return;
        }

        if ($commentModel->delete($id)) {
            Toast::success('Xóa comment thành công');
            $this->redirect('/post/' . $postSlug);
        } else {
            Toast::error('Không thể xóa comment');
            $this->redirect('/post/' . $postSlug);
        }
    }

    /**
     * Approve comment (admin hoặc post author)
     * @param int $id
     */
    public function approve($id)
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        $commentModel = new CommentModel();
        $comment = $commentModel->getById($id);

        if (!$comment) {
            Toast::error('Comment không tồn tại');
            Router::redirect('/post/' . $comment['post_slug']);
            return;
        }

        // Chỉ admin hoặc post author mới approve được
        if (!$this->canApprove($comment)) {
            Toast::error('Bạn không có quyền phê duyệt comment này');
            Router::redirect('/post/' . $comment['post_slug']);
            return;
        }

        // Validate CSRF
        if (!$this->validateCSRF()) {
            Toast::error('Yêu cầu không hợp lệ');
            Router::redirect('/post/' . $comment['post_slug']);
            return;
        }

        if ($commentModel->approve($id)) {
            Session::flash('success', 'Đã phê duyệt comment');
            Router::redirect('/post/' . $comment['post_slug']);
        } else {
            Toast::error('Không thể phê duyệt comment');
            Router::redirect('/post/' . $comment['post_slug']);
        }
    }

    /**
     * Unapprove comment (ẩn comment)
     * @param int $id
     */
    public function unapprove($id)
    {
        $this->requireAuth();
        $this->validateMethod('POST');

        $commentModel = new CommentModel();
        $comment = $commentModel->getById($id);

        if (!$comment) {
            Toast::error('Comment không tồn tại');
            Router::redirect('/post/' . $comment['post_slug']);
            return;
        }

        // Chỉ admin hoặc post author
        if (!$this->canApprove($comment)) {
            Toast::error('Bạn không có quyền phê duyệt comment này');
            Router::redirect('/post/' . $comment['post_slug']);
            return;
        }

        // Validate CSRF
        if (!$this->validateCSRF()) {
            Toast::error('Yêu cầu không hợp lệ');
            Router::redirect('/post/' . $comment['post_slug']);
            return;
        }

        if ($commentModel->unapprove($id)) {
            Session::flash('success', 'Đã ẩn comment');
            Router::redirect('/post/' . $comment['post_slug']);
        } else {
            Toast::error('Không thể ẩn comment');
            Router::redirect('/post/' . $comment['post_slug']);
        }
    }

    /**
     * Lấy comments của một post (AJAX)
     * @param int $postId
     */
    public function getByPost($postId)
    {
        $commentModel = new CommentModel();

        // Kiểm tra có show unapproved comments không (cho admin/author)
        $includeUnapproved = false;
        if (Session::isLoggedIn()) {
            $postModel = new PostModel();
            $post = $postModel->getById($postId);
            if ($post && (Session::isAdmin() || Session::getUserId() == $post['user_id'])) {
                $includeUnapproved = true;
            }
        }

        $comments = $commentModel->getByPost($postId, $includeUnapproved);
        $this->json(['success' => true, 'comments' => $comments]);
    }

    /**
     * Kiểm tra quyền modify comment
     * @param array $comment
     * @return bool
     */
    private function canModify($comment)
    {
        // Admin có thể modify tất cả
        if (Session::isAdmin()) {
            return true;
        }

        // Owner có thể modify comment của mình
        return Session::getUserId() == $comment['user_id'];
    }

    /**
     * Kiểm tra quyền delete comment
     * @param array $comment
     * @return bool
     */
    private function canDelete($comment)
    {
        $commentModel = new CommentModel();

        // Admin có thể delete tất cả
        if (Session::isAdmin()) {
            return true;
        }

        // Owner có thể delete comment của mình
        if (Session::getUserId() == $comment['user_id']) {
            return true;
        }

        // Post author có thể delete comments trong bài của mình
        if ($commentModel->isPostAuthor($comment['id'], Session::getUserId())) {
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra quyền approve comment
     * @param array $comment
     * @return bool
     */
    private function canApprove($comment)
    {
        $commentModel = new CommentModel();

        // Admin có thể approve tất cả
        if (Session::isAdmin()) {
            return true;
        }

        // Post author có thể approve comments trong bài của mình
        return $commentModel->isPostAuthor($comment['id'], Session::getUserId());
    }
}
