<?php

/**
 * Comment Helper - NO JAVASCRIPT REQUIRED
 * Hoàn toàn dùng HTML Form và PHP
 */

class CommentHelper
{

    /**
     * Render comment tree
     */
    public static function renderCommentTree($comments, $depth = 0, $maxDepth = 3)
    {
        if (empty($comments)) {
            return;
        }

        foreach ($comments as $comment) {
            self::renderComment($comment, $depth);

            if (!empty($comment['replies']) && $depth < $maxDepth) {
                echo '<div class="comment-replies ml-4 ml-md-5">';
                self::renderCommentTree($comment['replies'], $depth + 1, $maxDepth);
                echo '</div>';
            }
        }
    }

    /**
     * Render single comment - PURE HTML FORM
     */
    private static function renderComment($comment, $depth)
    {
        $commentId = $comment['id'];
        $postId = $comment['post_id'];
        $userName = Security::escape($comment['user_name']);
        $content = nl2br(Security::escape($comment['content']));
        $createdAt = self::timeAgo($comment['created_at']);
        $isApproved = $comment['is_approved'];

        $currentUserId = Session::getUserId();
        $isOwner = $currentUserId == $comment['user_id'];
        $isAdmin = Session::isAdmin();
        $canModerate = $isAdmin || $isOwner;

        // Check if this comment is in edit mode
        $isEditMode = isset($_GET['edit_comment']) && $_GET['edit_comment'] == $commentId;

        $commentClass = 'comment';
        if (!$isApproved) {
            $commentClass .= ' comment-pending';
        }
        if ($depth > 0) {
            $commentClass .= ' comment-reply';
        }
?>
        <div class="<?php echo $commentClass; ?>" id="comment-<?php echo $commentId; ?>">
            <div class="comment-header">
                <div class="comment-avatar">
                    <?php echo self::getAvatar($comment['user_email'], 48); ?>
                </div>
                <div class="comment-meta">
                    <div class="comment-author-line">
                        <strong class="comment-author"><?php echo $userName; ?></strong>
                        <?php if ($isOwner): ?>
                            <span class="comment-badge comment-badge--owner">Bạn</span>
                        <?php endif; ?>
                        <?php if (!$isApproved): ?>
                            <span class="comment-badge comment-badge--pending">Chờ phê duyệt</span>
                        <?php endif; ?>
                    </div>
                    <div class="comment-date">
                        <i class="far fa-clock"></i> <?php echo $createdAt; ?>
                    </div>
                </div>
            </div>

            <!-- Edit Mode -->
            <?php if ($isEditMode): ?>
                <div class="comment-edit-form">
                    <form method="POST" action="<?php echo Router::url('/comment/' . $commentId . '/update'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">

                        <textarea name="content" class="form-control" rows="4" required minlength="3" maxlength="5000"><?php echo htmlspecialchars($comment['content']); ?></textarea>

                        <div style="margin-top: 10px;">
                            <button type="submit" class="btn btn--primary btn--sm">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            <a href="<?php echo Router::url($_SERVER['REQUEST_URI']); ?>"
                                class="btn btn--secondary btn--sm"
                                onclick="history.back(); return false;">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Normal View -->
                <div class="comment-content">
                    <?php echo $content; ?>
                </div>
            <?php endif; ?>

            <div class="comment-actions">
                <!-- Reply Button - Toggle form -->
                <?php if (Session::isLoggedIn() && $depth < 2 && !$isEditMode): ?>
                    <a href="#reply-form-<?php echo $commentId; ?>"
                        class="comment-action-btn"
                        onclick="document.getElementById('reply-form-<?php echo $commentId; ?>').style.display='block'; this.style.display='none'; return false;">
                        <i class="fas fa-reply"></i> Trả lời
                    </a>
                <?php endif; ?>

                <!-- Edit Button -->
                <?php if ($canModerate && !$isEditMode): ?>
                    <a href="<?php echo Router::url('/comment/' . $commentId . '/edit'); ?>"
                        class="comment-action-btn">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                <?php endif; ?>

                <!-- Delete Button -->
                <?php if ($canModerate && !$isEditMode): ?>
                    <form method="POST"
                        action="<?php echo Router::url('/comment/' . $commentId . '/delete'); ?>"
                        style="display: inline;"
                        onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?');">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <button type="submit" class="comment-action-btn comment-action-btn--danger">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Approve Button -->
                <?php if (($isAdmin || self::isPostAuthor($comment)) && !$isApproved && !$isEditMode): ?>
                    <form method="POST"
                        action="<?php echo Router::url('/comment/' . $commentId . '/approve'); ?>"
                        style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <button type="submit" class="comment-action-btn comment-action-btn--success">
                            <i class="fas fa-check"></i> Phê duyệt
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Reply Form (Hidden by default) -->
            <?php if (Session::isLoggedIn() && $depth < 2): ?>
                <div class="reply-form" id="reply-form-<?php echo $commentId; ?>" style="display: none;">
                    <form method="POST" action="<?php echo Router::url('/comment/create'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                        <input type="hidden" name="parent_id" value="<?php echo $commentId; ?>">

                        <textarea name="content"
                            class="reply-form__textarea"
                            rows="3"
                            placeholder="Viết câu trả lời..."
                            required
                            minlength="3"
                            maxlength="5000"></textarea>

                        <div class="reply-form__actions">
                            <button type="submit" class="btn btn--primary btn--sm">
                                <i class="fas fa-paper-plane"></i> Gửi trả lời
                            </button>
                            <button type="button"
                                class="btn btn--secondary btn--sm"
                                onclick="document.getElementById('reply-form-<?php echo $commentId; ?>').style.display='none'; this.parentElement.parentElement.previousElementSibling.querySelector('a').style.display='inline-flex';">
                                Hủy
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php
    }

    /**
     * Get Gravatar avatar
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
     */
    private static function isPostAuthor($comment)
    {
        return false; // Implement if needed
    }

    /**
     * Render comment form
     */
    public static function renderCommentForm($postId, $buttonText = 'Gửi bình luận')
    {
        if (!Session::isLoggedIn()) {
            echo '<div class="alert alert-info">';
            echo 'Bạn cần <a href="' . Router::url('/login') . '">đăng nhập</a> để bình luận.';
            echo '</div>';
            return;
        }

        $userData = Session::get('user_data');
        $userName = ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? '');
    ?>
        <div class="comment-form-container card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Để lại bình luận</h5>
                <p class="text-muted small">Đăng nhập với tên: <strong><?php echo Security::escape(trim($userName)); ?></strong></p>

                <form method="POST" action="<?php echo Router::url('/comment/create'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">

                    <div class="mb-3">
                        <textarea name="content"
                            class="form-control"
                            rows="4"
                            placeholder="Nhập bình luận của bạn..."
                            required
                            minlength="3"
                            maxlength="5000"></textarea>
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
     * Get comment count text
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
     * Count total comments
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
