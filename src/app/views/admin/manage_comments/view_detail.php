<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Chi tiết bình luận</h1>
    <div class="content__breadcrumb">
        <a href="<?php echo Router::url('/admin'); ?>" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <a href="<?php echo Router::url('/admin/comments'); ?>" class="content__breadcrumb-item">Bình luận</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Chi tiết</span>
    </div>
</div>

<!-- Comment Details -->
<div class="comment-detail">
    <div class="comment-detail__card">
        <div class="comment-detail__header">
            <h2 class="comment-detail__title">
                <i class="fas fa-comment-dots"></i>
                Thông tin chi tiết
            </h2>
            <div class="comment-detail__actions">
                <a href="<?php echo Router::url('/admin/comments/edit/' . $comment['id']); ?>"
                    class="comment-detail__btn comment-detail__btn--primary comment-detail__btn--sm">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <a href="<?php echo Router::url('/admin/comments'); ?>"
                    class="comment-detail__btn comment-detail__btn--secondary comment-detail__btn--sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="comment-detail__body">
            <!-- User Info -->
            <div class="comment-detail__section">
                <h3 class="comment-detail__section-title">
                    <i class="fas fa-user"></i> Thông tin người bình luận
                </h3>
                <div class="comment-detail__user">
                    <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($comment['user_email'] ?? ''))) ?>?s=80&d=mp"
                        alt="Avatar"
                        class="comment-detail__user-avatar">
                    <div class="comment-detail__user-info">
                        <div class="comment-detail__user-name-wrapper">
                            <strong class="comment-detail__user-name">
                                <?= htmlspecialchars($comment['user_name'] ?? 'Anonymous') ?>
                            </strong>
                            <?php if ($comment['is_approved']): ?>
                                <span class="comment-detail__badge comment-detail__badge--success">
                                    <i class="fas fa-check-circle"></i> Đã duyệt
                                </span>
                            <?php else: ?>
                                <span class="comment-detail__badge comment-detail__badge--warning">
                                    <i class="fas fa-clock"></i> Chờ duyệt
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="comment-detail__user-meta">
                            <i class="fas fa-envelope"></i>
                            <?= htmlspecialchars($comment['user_email'] ?? 'N/A') ?>
                        </div>
                        <div class="comment-detail__user-meta">
                            <i class="fas fa-calendar"></i>
                            <?= date('d/m/Y H:i:s', strtotime($comment['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comment Content -->
            <div class="comment-detail__section">
                <h3 class="comment-detail__section-title">
                    <i class="fas fa-comment"></i> Nội dung bình luận
                </h3>
                <div class="comment-detail__content">
                    <?= nl2br(htmlspecialchars($comment['content'])) ?>
                </div>
            </div>

            <!-- Post Info -->
            <div class="comment-detail__section">
                <h3 class="comment-detail__section-title">
                    <i class="fas fa-newspaper"></i> Bài viết
                </h3>
                <div class="comment-detail__post">
                    <a href="<?php echo Router::url('/post/' . $post['slug']); ?>"
                        target="_blank"
                        class="comment-detail__post-link">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                    <div class="comment-detail__post-meta">
                        <i class="fas fa-user"></i>
                        <?= htmlspecialchars($post['author_name'] ?? 'Unknown') ?>
                        <span class="comment-detail__post-divider">•</span>
                        <i class="fas fa-calendar"></i>
                        <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                    </div>
                </div>
            </div>

            <!-- Replies -->
            <?php if (isset($replies) && count($replies) > 0): ?>
                <div class="comment-detail__section">
                    <h3 class="comment-detail__section-title">
                        <i class="fas fa-reply"></i> Câu trả lời (<?= count($replies) ?>)
                    </h3>
                    <div class="comment-detail__replies">
                        <?php foreach ($replies as $reply): ?>
                            <div class="comment-detail__reply">
                                <div class="comment-detail__reply-header">
                                    <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($reply['user_email'] ?? ''))) ?>?s=50&d=mp"
                                        class="comment-detail__reply-avatar"
                                        alt="Avatar">
                                    <div class="comment-detail__reply-info">
                                        <strong class="comment-detail__reply-name">
                                            <?= htmlspecialchars($reply['user_name'] ?? 'Anonymous') ?>
                                        </strong>
                                        <div class="comment-detail__reply-date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d/m/Y H:i', strtotime($reply['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-detail__reply-content">
                                    <?= nl2br(htmlspecialchars($reply['content'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="comment-detail__footer">
                <?php if (!$comment['is_approved']): ?>
                    <form method="POST" action="<?php echo Router::url('/admin/comments/approve/' . $comment['id']); ?>" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="comment-detail__btn comment-detail__btn--success">
                            <i class="fas fa-check"></i> Phê duyệt
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="<?php echo Router::url('/admin/comments/unapprove/' . $comment['id']); ?>" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <button type="submit" class="comment-detail__btn comment-detail__btn--warning">
                            <i class="fas fa-eye-slash"></i> Ẩn
                        </button>
                    </form>
                <?php endif; ?>

                <a href="<?php echo Router::url('/admin/comments/edit/' . $comment['id']); ?>"
                    class="comment-detail__btn comment-detail__btn--primary">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>

                <form method="POST"
                    action="<?php echo Router::url('/admin/comments/delete/' . $comment['id']); ?>"

                    onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="comment-detail__btn comment-detail__btn--danger">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>