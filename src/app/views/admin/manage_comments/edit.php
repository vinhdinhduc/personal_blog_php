<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Chỉnh sửa bình luận</h1>
    <div class="content__breadcrumb">
        <a href="<?php echo Router::url('/admin'); ?>" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <a href="<?php echo Router::url('/admin/comments'); ?>" class="content__breadcrumb-item">Bình luận</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Chỉnh sửa</span>
    </div>
</div>

<!-- Edit Form -->
<div class="comment-edit">
    <div class="comment-edit__card">
        <div class="comment-edit__header">
            <h2 class="comment-edit__title">
                <i class="fas fa-edit"></i>
                Chỉnh sửa bình luận
            </h2>
        </div>

        <div class="comment-edit__body">
            <!-- Comment Info -->
            <div class="comment-edit__info">
                <div class="comment-edit__info-grid">
                    <div class="comment-edit__info-item">
                        <span class="comment-edit__info-label">Người bình luận:</span>
                        <span class="comment-edit__info-value">
                            <?= htmlspecialchars($comment['user_name'] ?? 'Anonymous') ?>
                        </span>
                    </div>
                    <div class="comment-edit__info-item">
                        <span class="comment-edit__info-label">Email:</span>
                        <span class="comment-edit__info-value">
                            <?= htmlspecialchars($comment['user_email'] ?? 'N/A') ?>
                        </span>
                    </div>
                    <div class="comment-edit__info-item">
                        <span class="comment-edit__info-label">Ngày tạo:</span>
                        <span class="comment-edit__info-value">
                            <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                        </span>
                    </div>
                    <div class="comment-edit__info-item">
                        <span class="comment-edit__info-label">Trạng thái:</span>
                        <span class="comment-edit__info-value">
                            <?php if ($comment['is_approved']): ?>
                                <span class="comment-edit__badge comment-edit__badge--success">Đã duyệt</span>
                            <?php else: ?>
                                <span class="comment-edit__badge comment-edit__badge--warning">Chờ duyệt</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($comment['post_title'])): ?>
                    <div class="comment-edit__info-post">
                        <span class="comment-edit__info-post-label">Bài viết:</span>
                        <a href="<?php echo Router::url('/post/' . $comment['post_slug']); ?>"
                            target="_blank"
                            class="comment-edit__info-post-link">
                            <?= htmlspecialchars($comment['post_title']) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Edit Form -->
            <form method="POST"
                action="<?php echo Router::url('/admin/comments/edit/' . $comment['id']); ?>"
                class="comment-edit__form">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="comment-edit__form-group">
                    <label class="comment-edit__form-label">
                        <i class="fas fa-comment"></i>
                        Nội dung bình luận
                        <span class="comment-edit__form-required">*</span>
                    </label>
                    <textarea name="content"
                        class="comment-edit__form-textarea"
                        rows="8"
                        required
                        maxlength="5000"
                        placeholder="Nhập nội dung bình luận..."><?= htmlspecialchars($comment['content']) ?></textarea>
                    <small class="comment-edit__form-hint">
                        Chỉnh sửa nội dung bình luận của người dùng
                    </small>
                </div>

                <div class="comment-edit__form-footer">
                    <a href="<?php echo Router::url('/admin/comments'); ?>"
                        class="comment-edit__btn comment-edit__btn--secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                    <button type="submit" class="comment-edit__btn comment-edit__btn--primary">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>