<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Quản lý bình luận</h1>
    <div class="content__breadcrumb">
        <a href="/admin/dashboard" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Bình luận</span>
    </div>
</div>

<!-- Statistics -->
<div class="card-grid" style="margin-bottom: 30px;">
    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Tổng bình luận</h3>
                <div class="card__value"><?= $totalComments ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--primary">
                <i class="fas fa-comments"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Đã duyệt</h3>
                <div class="card__value"><?= $approvedComments ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Chờ duyệt</h3>
                <div class="card__value"><?= $pendingComments ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Spam</h3>
                <div class="card__value"><?= $spamComments ?? 0 ?></div>
            </div>
            <div class="card__icon card__icon--danger">
                <i class="fas fa-ban"></i>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div style="margin-bottom: 20px; display: flex; gap: 10px;">
    <button class="btn btn--success" onclick="bulkApprove()">
        <i class="fas fa-check"></i>
        Duyệt đã chọn
    </button>
    <button class="btn btn--warning" onclick="bulkSpam()">
        <i class="fas fa-flag"></i>
        Đánh dấu spam
    </button>
    <button class="btn btn--danger" onclick="bulkDelete()">
        <i class="fas fa-trash"></i>
        Xóa đã chọn
    </button>
</div>

<!-- Comments Table -->
<div class="table-container">
    <div class="table-container__header">
        <h2 class="table-container__title">Danh sách bình luận</h2>

        <!-- Filter & Search -->
        <div style="display: flex; gap: 10px;">
            <select class="form-control" style="width: 150px;">
                <option value="">Tất cả trạng thái</option>
                <option value="approved">Đã duyệt</option>
                <option value="pending">Chờ duyệt</option>
                <option value="spam">Spam</option>
            </select>

            <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 250px;">

            <button class="btn btn--info btn--sm">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 50px;">
                    <input type="checkbox" id="selectAll">
                </th>
                <th style="width: 60px;">ID</th>
                <th style="width: 150px;">Người bình luận</th>
                <th>Nội dung</th>
                <th style="width: 200px;">Bài viết</th>
                <th style="width: 120px;">Ngày tạo</th>
                <th style="width: 100px;">Trạng thái</th>
                <th style="width: 180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($comments) && count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                    <tr style="<?= $comment['status'] == 'pending' ? 'background: rgba(246,194,62,0.05);' : '' ?>">
                        <td>
                            <input type="checkbox" class="comment-checkbox" value="<?= $comment['id'] ?>">
                        </td>
                        <td><?= $comment['id'] ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if (!empty($comment['user_avatar'])): ?>
                                    <img src="<?= htmlspecialchars($comment['user_avatar']) ?>"
                                        alt="Avatar"
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold;">
                                        <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                                    <br>
                                    <small style="color: var(--secondary-color);">
                                        <i class="fas fa-envelope"></i>
                                        <?= htmlspecialchars($comment['user_email']) ?>
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="max-width: 400px;">
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>

                            <?php if (!empty($comment['parent_id'])): ?>
                                <div style="margin-top: 10px; padding: 10px; background: var(--light-color); border-left: 3px solid var(--info-color); border-radius: 5px;">
                                    <small style="color: var(--secondary-color);">
                                        <i class="fas fa-reply"></i>
                                        Trả lời bình luận #<?= $comment['parent_id'] ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/posts/<?= $comment['post_slug'] ?>"
                                target="_blank"
                                style="color: var(--primary-color); text-decoration: none;">
                                <i class="fas fa-external-link-alt"></i>
                                <?= htmlspecialchars(mb_substr($comment['post_title'], 0, 40)) ?>...
                            </a>
                            <br>
                            <small style="color: var(--secondary-color);">
                                <i class="fas fa-comments"></i>
                                <?= $comment['post_comment_count'] ?? 0 ?> bình luận
                            </small>
                        </td>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                            <br>
                            <small style="color: var(--secondary-color);">
                                <i class="fas fa-map-marker-alt"></i>
                                IP: <?= htmlspecialchars($comment['ip_address'] ?? 'N/A') ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($comment['status'] == 'approved'): ?>
                                <span class="badge badge--success">
                                    <i class="fas fa-check-circle"></i>
                                    Đã duyệt
                                </span>
                            <?php elseif ($comment['status'] == 'pending'): ?>
                                <span class="badge badge--warning">
                                    <i class="fas fa-clock"></i>
                                    Chờ duyệt
                                </span>
                            <?php else: ?>
                                <span class="badge badge--danger">
                                    <i class="fas fa-ban"></i>
                                    Spam
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($comment['status'] == 'pending'): ?>
                                <a href="/admin/comments/approve/<?= $comment['id'] ?>"
                                    class="btn btn--success btn--sm"
                                    data-tooltip="Duyệt">
                                    <i class="fas fa-check"></i>
                                </a>
                            <?php endif; ?>

                            <a href="/admin/comments/reply/<?= $comment['id'] ?>"
                                class="btn btn--info btn--sm"
                                data-tooltip="Trả lời">
                                <i class="fas fa-reply"></i>
                            </a>

                            <a href="/admin/comments/edit/<?= $comment['id'] ?>"
                                class="btn btn--warning btn--sm"
                                data-tooltip="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>

                            <?php if ($comment['status'] != 'spam'): ?>
                                <a href="/admin/comments/spam/<?= $comment['id'] ?>"
                                    class="btn btn--warning btn--sm"
                                    data-tooltip="Đánh dấu spam"
                                    onclick="return confirm('Đánh dấu bình luận này là spam?')">
                                    <i class="fas fa-flag"></i>
                                </a>
                            <?php endif; ?>

                            <a href="/admin/comments/delete/<?= $comment['id'] ?>"
                                class="btn btn--danger btn--sm btn-delete"
                                data-tooltip="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 60px;">
                        <i class="fas fa-comments" style="font-size: 64px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                        <p style="font-size: 18px; color: var(--secondary-color);">Chưa có bình luận nào</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-top: 1px solid #eee;">
            <div style="color: var(--secondary-color);">
                Hiển thị <?= ($currentPage - 1) * $perPage + 1 ?> -
                <?= min($currentPage * $perPage, $totalComments) ?>
                trong tổng số <?= $totalComments ?> bình luận
            </div>
            <div style="display: flex; gap: 5px;">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="btn btn--sm">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <button class="btn btn--primary btn--sm"><?= $i ?></button>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>" class="btn btn--sm"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="btn btn--sm">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Select all checkboxes
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.comment-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk actions
    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.comment-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function bulkApprove() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một bình luận!');
            return;
        }
        if (confirm(`Bạn có chắc muốn duyệt ${ids.length} bình luận đã chọn?`)) {
            // Implement bulk approve logic
            console.log('Approve comments:', ids);
            window.location.href = `/admin/comments/bulk-approve?ids=${ids.join(',')}`;
        }
    }

    function bulkSpam() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một bình luận!');
            return;
        }
        if (confirm(`Bạn có chắc muốn đánh dấu spam ${ids.length} bình luận đã chọn?`)) {
            window.location.href = `/admin/comments/bulk-spam?ids=${ids.join(',')}`;
        }
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
            alert('Vui lòng chọn ít nhất một bình luận!');
            return;
        }
        if (confirm(`CẢNH BÁO: Bạn có chắc muốn xóa vĩnh viễn ${ids.length} bình luận đã chọn?`)) {
            window.location.href = `/admin/comments/bulk-delete?ids=${ids.join(',')}`;
        }
    }
</script>