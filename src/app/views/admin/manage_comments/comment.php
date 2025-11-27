<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Quản lý bình luận</h1>
    <div class="content__breadcrumb">
        <a href="<?php echo Router::url('/admin'); ?>" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Bình luận</span>
    </div>
</div>

<!-- Statistics -->
<div class="comment-stats">
    <div class="comment-stat-card comment-stat-card--total">
        <div class="comment-stat-card__header">
            <div>
                <h3 class="comment-stat-card__title">Tổng bình luận</h3>
                <div class="comment-stat-card__value"><?= $stats['total'] ?? 0 ?></div>
            </div>
            <div class="comment-stat-card__icon comment-stat-card__icon--total">
                <i class="fas fa-comments"></i>
            </div>
        </div>
        <div class="comment-stat-card__footer">
            <a href="<?php echo Router::url('/admin/comments?status=all'); ?>" class="comment-stat-card__link">
                Xem tất cả <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="comment-stat-card comment-stat-card--approved">
        <div class="comment-stat-card__header">
            <div>
                <h3 class="comment-stat-card__title">Đã phê duyệt</h3>
                <div class="comment-stat-card__value"><?= $stats['approved'] ?? 0 ?></div>
            </div>
            <div class="comment-stat-card__icon comment-stat-card__icon--approved">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="comment-stat-card__footer">
            <a href="<?php echo Router::url('/admin/comments?status=approved'); ?>" class="comment-stat-card__link">
                Xem chi tiết <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="comment-stat-card comment-stat-card--pending">
        <div class="comment-stat-card__header">
            <div>
                <h3 class="comment-stat-card__title">Chờ duyệt</h3>
                <div class="comment-stat-card__value"><?= $stats['pending'] ?? 0 ?></div>
            </div>
            <div class="comment-stat-card__icon comment-stat-card__icon--pending">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="comment-stat-card__footer">
            <a href="<?php echo Router::url('/admin/comments?status=pending'); ?>" class="comment-stat-card__link">
                Phê duyệt ngay <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="comment-filters">
    <div class="comment-filters__header">
        <h2 class="comment-filters__title">
            <i class="fas fa-filter"></i>
            Bộ lọc
        </h2>
        <div class="comment-filters__actions">
            <button class="btn btn--success btn--sm" onclick="bulkApprove()">
                <i class="fas fa-check"></i> Duyệt đã chọn
            </button>
            <button class="btn btn--danger btn--sm" onclick="bulkDelete()">
                <i class="fas fa-trash"></i> Xóa đã chọn
            </button>
        </div>
    </div>

    <form method="GET" action="<?php echo Router::url('/admin/comments'); ?>" class="comment-filters__body">
        <div class="comment-filters__group">
            <label class="comment-filters__label">
                <i class="fas fa-filter"></i>
                Trạng thái
            </label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="all" <?= ($currentStatus ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="pending" <?= ($currentStatus ?? '') === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                <option value="approved" <?= ($currentStatus ?? '') === 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
            </select>
        </div>

        <div class="comment-filters__group">
            <label class="comment-filters__label">
                <i class="fas fa-newspaper"></i>
                Bài viết
            </label>
            <select name="post_id" class="form-control" onchange="this.form.submit()">
                <option value="">Tất cả bài viết</option>
                <?php if (isset($recentPosts) && count($recentPosts) > 0): ?>
                    <?php foreach ($recentPosts as $post): ?>
                        <option value="<?= $post['id'] ?>" <?= ($currentPostId ?? '') == $post['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($post['title']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="comment-filters__group">
            <label class="comment-filters__label">
                <i class="fas fa-search"></i>
                Tìm kiếm
            </label>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo nội dung, tên người dùng..." value="<?= htmlspecialchars($searchQuery ?? '') ?>" style="flex: 1;">
                <button type="submit" class="btn btn--primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Comments List -->
<div class="comment-list">
    <div class="comment-list__header">
        <h2 class="comment-list__title">
            Danh sách bình luận
            <?php if (isset($pagination['total'])): ?>
                <span style="color: #858796; font-weight: normal; font-size: 14px;">
                    (<?= $pagination['total'] ?> bình luận)
                </span>
            <?php endif; ?>
        </h2>
        <div class="comment-list__bulk-actions">
            <input type="checkbox" id="selectAll" style="width: 18px; height: 18px; cursor: pointer;">
            <label for="selectAll" style="cursor: pointer; user-select: none;">Chọn tất cả</label>
        </div>
    </div>

    <div class="comment-list__body">
        <?php if (isset($comments) && count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-item <?= !$comment['is_approved'] ? 'comment-item--pending' : '' ?>" data-comment-id="<?= $comment['id'] ?>">
                    <div style="display: flex; gap: 15px;">
                        <div class="comment-item__checkbox">
                            <input type="checkbox" class="comment-checkbox" value="<?= $comment['id'] ?>">
                        </div>

                        <div style="flex: 1;">
                            <div class="comment-item__header">
                                <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($comment['user_email'] ?? ''))) ?>?s=100&d=mp"
                                    alt="<?= htmlspecialchars($comment['user_name'] ?? 'Unknown') ?>"
                                    class="comment-item__avatar">

                                <div class="comment-item__meta">
                                    <div class="comment-item__author">
                                        <?= htmlspecialchars($comment['user_name'] ?? 'Anonymous') ?>
                                        <?php if (!$comment['is_approved']): ?>
                                            <span class="comment-badge comment-badge--pending">
                                                <i class="fas fa-clock"></i> Chờ duyệt
                                            </span>
                                        <?php else: ?>
                                            <span class="comment-badge comment-badge--approved">
                                                <i class="fas fa-check-circle"></i> Đã duyệt
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($comment['parent_id'])): ?>
                                            <span class="comment-badge comment-badge--reply">
                                                <i class="fas fa-reply"></i> Trả lời
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment-item__email"><?= htmlspecialchars($comment['user_email'] ?? '') ?></div>
                                    <div class="comment-item__info">
                                        <span class="comment-item__info-item">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                        </span>
                                        <span class="comment-item__info-item">
                                            <i class="fas fa-reply"></i>
                                            <?= $comment['reply_count'] ?? 0 ?> trả lời
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="comment-item__content">
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>

                            <?php if (!empty($comment['post_title'])): ?>
                                <a href="<?php echo Router::url('/post/' . $comment['post_slug']); ?>"
                                    target="_blank"
                                    class="comment-item__post">
                                    <i class="fas fa-newspaper"></i>
                                    <?= htmlspecialchars($comment['post_title']) ?>
                                </a>
                            <?php endif; ?>

                            <div class="comment-item__actions">
                                <?php if (!$comment['is_approved']): ?>
                                    <form method="POST" action="<?php echo Router::url('/admin/comments/approve/' . $comment['id']); ?>" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <button type="submit" class="comment-item__action-btn comment-item__action-btn--approve">
                                            <i class="fas fa-check"></i> Phê duyệt
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="<?php echo Router::url('/admin/comments/unapprove/' . $comment['id']); ?>" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <button type="submit" class="comment-item__action-btn comment-item__action-btn--unapprove">
                                            <i class="fas fa-eye-slash"></i> Ẩn
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <button class="comment-item__action-btn comment-item__action-btn--edit" onclick="editComment(<?= $comment['id'] ?>, <?= htmlspecialchars(json_encode($comment['content'])) ?>)">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>

                                <button class="comment-item__action-btn comment-item__action-btn--view" onclick="viewComment(<?= $comment['id'] ?>)">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </button>

                                <form method="POST" action="<?php echo Router::url('/admin/comments/delete/' . $comment['id']); ?>" style="display: inline;" onsubmit="return confirmDelete(<?= $comment['reply_count'] ?? 0 ?>)">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <button type="submit" class="comment-item__action-btn comment-item__action-btn--delete">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="comment-empty">
                <i class="fas fa-comments comment-empty__icon"></i>
                <p class="comment-empty__title">Chưa có bình luận nào</p>
                <p class="comment-empty__text">
                    <?php if (isset($currentStatus) && $currentStatus === 'pending'): ?>
                        Không có bình luận chờ duyệt
                    <?php elseif (!empty($searchQuery)): ?>
                        Không tìm thấy bình luận phù hợp với từ khóa "<?= htmlspecialchars($searchQuery) ?>"
                    <?php else: ?>
                        Bài viết của bạn chưa có bình luận nào
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="comment-pagination">
            <div class="comment-pagination__info">
                Hiển thị <?= min($pagination['per_page'], $pagination['total']) ?> / <?= $pagination['total'] ?> bình luận
            </div>
            <div class="comment-pagination__pages">
                <?php if ($pagination['has_prev']): ?>
                    <a href="?page=<?= $pagination['current_page'] - 1 ?>&status=<?= $currentStatus ?? 'all' ?>&search=<?= urlencode($searchQuery ?? '') ?>"
                        class="comment-pagination__page">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                        <a href="?page=<?= $i ?>&status=<?= $currentStatus ?? 'all' ?>&search=<?= urlencode($searchQuery ?? '') ?>"
                            class="comment-pagination__page <?= $i == $pagination['current_page'] ? 'comment-pagination__page--active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                        <span class="comment-pagination__page comment-pagination__page--disabled">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagination['has_next']): ?>
                    <a href="?page=<?= $pagination['current_page'] + 1 ?>&status=<?= $currentStatus ?? 'all' ?>&search=<?= urlencode($searchQuery ?? '') ?>"
                        class="comment-pagination__page">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Edit Comment Modal -->
<div class="comment-modal" id="editCommentModal">
    <div class="comment-modal__content">
        <div class="comment-modal__header">
            <h3 class="comment-modal__title">Chỉnh sửa bình luận</h3>
            <button class="comment-modal__close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="comment-modal__body">
            <form id="editCommentForm">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="comment_id" id="edit_comment_id">
                <div class="form-group">
                    <label class="form-label">Nội dung bình luận</label>
                    <textarea name="content" id="edit_comment_content" class="form-control" rows="5" required></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn--secondary" onclick="closeEditModal()">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn--primary">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Comment Modal -->
<div class="comment-modal" id="viewCommentModal">
    <div class="comment-modal__content">
        <div class="comment-modal__header">
            <h3 class="comment-modal__title">Chi tiết bình luận</h3>
            <button class="comment-modal__close" onclick="closeViewModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="comment-modal__body" id="viewCommentContent">
            <!-- Content will be loaded via JavaScript -->
        </div>
    </div>
</div>

<script src="<?php echo Router::url('js/admin-comments.js'); ?>"></script>