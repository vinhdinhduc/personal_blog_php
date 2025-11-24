<?php
// $comment variable is available from parent view
?>
<div class="comment-item" id="comment-<?= $comment['id'] ?>">
    <div class="comment-avatar">
        <i class="fas fa-user-circle"></i>
    </div>
    <div class="comment-content">
        <div class="comment-header">
            <strong class="comment-author"><?= htmlspecialchars($comment['user_name']) ?></strong>
            <span class="comment-date"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></span>
        </div>
        <div class="comment-body">
            <?= nl2br(htmlspecialchars($comment['content'])) ?>
        </div>
        <div class="comment-actions">
            <?php if (Session::isLoggedIn()): ?>
                <button class="comment-reply-btn" data-comment-id="<?= $comment['id'] ?>">
                    <i class="fas fa-reply"></i> Trả lời
                </button>
            <?php endif; ?>
        </div>

        <!-- Replies -->
        <?php if (!empty($comment['replies'])): ?>
            <div class="comment-replies">
                <?php foreach ($comment['replies'] as $reply): ?>
                    <?php
                    $comment = $reply; // Đệ quy
                    require __DIR__ . '/comment-item.php';
                    ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .comment-item {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .comment-avatar i {
        font-size: 40px;
        color: #667eea;
    }

    .comment-content {
        flex: 1;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
    }

    .comment-header {
        margin-bottom: 10px;
    }

    .comment-author {
        color: #333;
        font-size: 16px;
    }

    .comment-date {
        color: #999;
        font-size: 13px;
        margin-left: 10px;
    }

    .comment-body {
        color: #555;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .comment-reply-btn {
        background: none;
        border: none;
        color: #667eea;
        font-size: 14px;
        cursor: pointer;
        padding: 5px 0;
    }

    .comment-replies {
        margin-top: 20px;
        padding-left: 40px;
    }
</style>