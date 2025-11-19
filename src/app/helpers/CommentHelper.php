<?php

/**
 * Comment Helper
 * Helper functions để hiển thị comments theo dạng threaded
 */

class CommentHelper
{

    /**
     * Render comment tree (đệ quy)
     * @param array $comments
     * @param int $depth
     * @param int $maxDepth
     */
    public static function renderCommentTree($comments, $depth = 0, $maxDepth = 3)
    {
        if (empty($comments)) {
            return;
        }

        foreach ($comments as $comment) {
            self::renderComment($comment, $depth);

            // Render replies nếu có và chưa đạt max depth
            if (!empty($comment['replies']) && $depth < $maxDepth) {
                echo '<div class="comment-replies ml-4 ml-md-5">';
                self::renderCommentTree($comment['replies'], $depth + 1, $maxDepth);
                echo '</div>';
            }
        }
    }

    /**
     * Render single comment
     * @param array $comment
     * @param int $depth
     */
    private static function renderComment($comment, $depth)
    {
        $commentId = $comment['id'];
        $userName = Security::escape($comment['user_name']);
        $content = nl2br(Security::escape($comment['content']));
        $createdAt = self::timeAgo($comment['created_at']);
        $isApproved = $comment['is_approved'];

        // User info
        $currentUserId = Session::getUserId();
        $isOwner = $currentUserId == $comment['user_id'];
        $isAdmin = Session::isAdmin();
        $canModerate = $isAdmin || Session::getUserId() == $comment['user_id'];

        // CSS classes
        $commentClass = 'comment';
        if (!$isApproved) {
            $commentClass .= ' comment-pending';
        }
        if ($depth > 0) {
            $commentClass .= ' comment-reply';
        }

?>
        <div class="<?php echo $commentClass; ?>" id="comment-<?php echo $commentId; ?>" data-comment-id="<?php echo $commentId; ?>">
            <div class="comment-header d-flex align-items-center mb-2">
                <div class="comment-avatar me-3">
                    <?php echo self::getAvatar($comment['user_email'], 48); ?>
                </div>
                <div class="comment-meta flex-grow-1">
                    <strong class="comment-author"><?php echo $userName; ?></strong>
                    <?php if ($isOwner): ?>
                        <span class="badge bg-primary ms-2">Bạn</span>
                    <?php endif; ?>
                    <?php if (!$isApproved): ?>
                        <span class="badge bg-warning text-dark ms-2">Chờ phê duyệt</span>
                    <?php endif; ?>
                    <div class="comment-date text-muted small">
                        <?php echo $createdAt; ?>
                    </div>
                </div>
            </div>

            <div class="comment-content mb-3">
                <?php echo $content; ?>
            </div>

            <div class="comment-actions">
                <?php if (Session::isLoggedIn() && $depth < 2): ?>
                    <button class="btn btn-sm btn-link reply-btn" data-comment-id="<?php echo $commentId; ?>">
                        <i class="fas fa-reply"></i> Trả lời
                    </button>
                <?php endif; ?>

                <?php if ($canModerate): ?>
                    <button class="btn btn-sm btn-link text-primary edit-comment-btn" data-comment-id="<?php echo $commentId; ?>">
                        <i class="fas fa-edit"></i> Sửa
                    </button>
                    <button class="btn btn-sm btn-link text-danger delete-comment-btn" data-comment-id="<?php echo $commentId; ?>">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                <?php endif; ?>

                <?php if (($isAdmin || self::isPostAuthor($comment)) && !$isApproved): ?>
                    <button class="btn btn-sm btn-link text-success approve-comment-btn" data-comment-id="<?php echo $commentId; ?>">
                        <i class="fas fa-check"></i> Phê duyệt
                    </button>
                <?php endif; ?>
            </div>

            <!-- Reply form (hidden by default) -->
            <div class="reply-form mt-3" id="reply-form-<?php echo $commentId; ?>" style="display: none;">
                <form class="comment-form" data-parent-id="<?php echo $commentId; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="parent_id" value="<?php echo $commentId; ?>">
                    <textarea name="content" class="form-control" rows="3" placeholder="Viết câu trả lời..." required></textarea>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary btn-sm">Gửi trả lời</button>
                        <button type="button" class="btn btn-secondary btn-sm cancel-reply-btn">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    <?php
    }

    /**
     * Get Gravatar avatar
     * @param string $email
     * @param int $size
     * @return string
     */
    private static function getAvatar($email, $size = 48)
    {
        $hash = md5(strtolower(trim($email)));
        $default = urlencode('identicon');
        $url = "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}";
        return "<img src='{$url}' alt='Avatar' class='rounded-circle' width='{$size}' height='{$size}'>";
    }

    /**
     * Format time ago
     * @param string $datetime
     * @return string
     */
    private static function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'vừa xong';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' phút trước';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' giờ trước';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' ngày trước';
        } else {
            return date('d/m/Y H:i', $timestamp);
        }
    }

    /**
     * Check if current user is post author
     * @param array $comment
     * @return bool
     */
    private static function isPostAuthor($comment)
    {
        // This should check via database, but for now we'll use a simple check
        // In production, pass this info from controller
        return false;
    }

    /**
     * Render comment form
     * @param int $postId
     * @param string $buttonText
     */
    public static function renderCommentForm($postId, $buttonText = 'Gửi bình luận')
    {
        if (!Session::isLoggedIn()) {
            echo '<div class="alert alert-info">';
            echo 'Bạn cần <a href="' . Router::url('/login') . '">đăng nhập</a> để bình luận.';
            echo '</div>';
            return;
        }

        $userName = Session::get('user_data')['name'] ?? 'User';
    ?>
        <div class="comment-form-container card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Để lại bình luận</h5>
                <p class="text-muted small">Đăng nhập với tên: <strong><?php echo Security::escape($userName); ?></strong></p>

                <form id="main-comment-form" class="comment-form" method="POST" action="<?php echo Router::url('/comment/create'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">

                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="4"
                            placeholder="Nhập bình luận của bạn..." required
                            minlength="3" maxlength="5000"></textarea>
                        <small class="text-muted">Tối thiểu 3 ký tự, tối đa 5000 ký tự</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-comment"></i> <?php echo $buttonText; ?>
                    </button>
                </form>
            </div>
        </div>
<?php
    }

    /**
     * Get comment count display text
     * @param int $count
     * @return string
     */
    public static function getCommentCountText($count)
    {
        if ($count == 0) {
            return 'Chưa có bình luận';
        } elseif ($count == 1) {
            return '1 bình luận';
        } else {
            return $count . ' bình luận';
        }
    }

    /**
     * Calculate total comments including replies
     * @param array $comments
     * @return int
     */
    public static function countTotalComments($comments)
    {
        $count = count($comments);

        foreach ($comments as $comment) {
            if (!empty($comment['replies'])) {
                $count += self::countTotalComments($comment['replies']);
            }
        }

        return $count;
    }
}
