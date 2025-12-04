<div class="tag-view">
    <div class="tag-view__header">
        <div class="tag-view__header-left">
            <h1 class="tag-view__title">Chi Tiết Tag</h1>
            <span class="tag-view__badge"><?= htmlspecialchars($tag['name']) ?></span>
        </div>
        <div class="tag-view__header-right">
            <a href="<?= Router::url('/admin/tags/edit/' . $tag['id']) ?>"
                class="tag-view__btn tag-view__btn--primary">
                <i class="fas fa-edit"></i> Chỉnh Sửa
            </a>
            <a href="<?= Router::url('/admin/tags') ?>"
                class="tag-view__btn tag-view__btn--secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="tag-view__container">
        <!-- Main Info Card -->
        <div class="tag-view__card tag-view__card--main">
            <div class="tag-view__card-header">
                <h2 class="tag-view__card-title">
                    <i class="fas fa-tag"></i> Thông Tin Cơ Bản
                </h2>
                <?php if ($usageCount > 0): ?>
                    <span class="tag-view__status tag-view__status--active">
                        <i class="fas fa-check-circle"></i> Đang sử dụng
                    </span>
                <?php else: ?>
                    <span class="tag-view__status tag-view__status--inactive">
                        <i class="fas fa-circle"></i> Chưa sử dụng
                    </span>
                <?php endif; ?>
            </div>
            <div class="tag-view__card-body">
                <div class="tag-view__info-grid">
                    <div class="tag-view__info-item">
                        <label class="tag-view__info-label">
                            <i class="fas fa-hashtag"></i> ID
                        </label>
                        <div class="tag-view__info-value"><?= $tag['id'] ?></div>
                    </div>

                    <div class="tag-view__info-item">
                        <label class="tag-view__info-label">
                            <i class="fas fa-tag"></i> Tên Tag
                        </label>
                        <div class="tag-view__info-value">
                            <span class="tag-view__tag-name"><?= htmlspecialchars($tag['name']) ?></span>
                        </div>
                    </div>

                    <div class="tag-view__info-item">
                        <label class="tag-view__info-label">
                            <i class="fas fa-link"></i> Slug
                        </label>
                        <div class="tag-view__info-value">
                            <code class="tag-view__code"><?= htmlspecialchars($tag['slug']) ?></code>
                        </div>
                    </div>

                    <div class="tag-view__info-item">
                        <label class="tag-view__info-label">
                            <i class="fas fa-file-alt"></i> Số Bài Viết
                        </label>
                        <div class="tag-view__info-value">
                            <span class="tag-view__count"><?= $usageCount ?></span>
                        </div>
                    </div>

                    <div class="tag-view__info-item">
                        <label class="tag-view__info-label">
                            <i class="fas fa-calendar-plus"></i> Ngày Tạo
                        </label>
                        <div class="tag-view__info-value">
                            <?= date('d/m/Y H:i:s', strtotime($tag['created_at'])) ?>
                        </div>
                    </div>


                    <div class="tag-view__info-item tag-view__info-item--full">
                        <label class="tag-view__info-label">
                            <i class="fas fa-external-link-alt"></i> URL Công Khai
                        </label>
                        <div class="tag-view__info-value">
                            <a href="<?= Router::url('/tag/' . $tag['slug']) ?>"
                                target="_blank"
                                class="tag-view__link">
                                <?= Router::url('/tag/' . $tag['slug']) ?>
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="tag-view__sidebar">
            <!-- Stats Card -->
            <div class="tag-view__card">
                <div class="tag-view__card-header">
                    <h3 class="tag-view__card-title">
                        <i class="fas fa-chart-bar"></i> Thống Kê
                    </h3>
                </div>
                <div class="tag-view__card-body">
                    <div class="tag-view__stat">
                        <div class="tag-view__stat-icon tag-view__stat-icon--primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="tag-view__stat-content">
                            <div class="tag-view__stat-value"><?= $usageCount ?></div>
                            <div class="tag-view__stat-label">Bài viết</div>
                        </div>
                    </div>

                    <div class="tag-view__stat">
                        <div class="tag-view__stat-icon tag-view__stat-icon--info">
                            <i class="fas fa-font"></i>
                        </div>
                        <div class="tag-view__stat-content">
                            <div class="tag-view__stat-value"><?= strlen($tag['name']) ?></div>
                            <div class="tag-view__stat-label">Ký tự</div>
                        </div>
                    </div>

                    <div class="tag-view__stat">
                        <div class="tag-view__stat-icon tag-view__stat-icon--success">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="tag-view__stat-content">
                            <div class="tag-view__stat-value">
                                <?php
                                $created = new DateTime($tag['created_at']);
                                $now = new DateTime();
                                $diff = $now->diff($created);
                                echo $diff->days;
                                ?>
                            </div>
                            <div class="tag-view__stat-label">Ngày tồn tại</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="tag-view__card">
                <div class="tag-view__card-header">
                    <h3 class="tag-view__card-title">
                        <i class="fas fa-cog"></i> Thao Tác
                    </h3>
                </div>
                <div class="tag-view__card-body">
                    <div class="tag-view__actions">
                        <a href="<?= Router::url('/admin/tags/edit/' . $tag['id']) ?>"
                            class="tag-view__action-btn tag-view__action-btn--primary">
                            <i class="fas fa-edit"></i> Chỉnh Sửa Tag
                        </a>

                        <?php if ($usageCount > 0): ?>
                            <a href="<?= Router::url('/tag/' . $tag['slug']) ?>"
                                class="tag-view__action-btn tag-view__action-btn--secondary"
                                target="_blank">
                                <i class="fas fa-eye"></i> Xem Trang Tag
                            </a>
                        <?php endif; ?>

                        <button
                            type="button"
                            class="tag-view__action-btn tag-view__action-btn--danger"
                            onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Xóa Tag
                        </button>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="tag-view__card">
                <div class="tag-view__card-header">
                    <h3 class="tag-view__card-title">
                        <i class="fas fa-info-circle"></i> Thông Tin
                    </h3>
                </div>
                <div class="tag-view__card-body">
                    <ul class="tag-view__help-list">
                        <?php if ($usageCount > 0): ?>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                Tag đang được sử dụng trong <?= $usageCount ?> bài viết
                            </li>
                        <?php else: ?>
                            <li>
                                <i class="fas fa-exclamation-circle"></i>
                                Tag chưa được sử dụng trong bài viết nào
                            </li>
                        <?php endif; ?>
                        <li>
                            <i class="fas fa-link"></i>
                            URL slug: <?= htmlspecialchars($tag['slug']) ?>
                        </li>
                        <li>
                            <i class="fas fa-calendar"></i>
                            Tạo lúc: <?= date('d/m/Y', strtotime($tag['created_at'])) ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="tag-view__modal" id="deleteModal">
    <div class="tag-view__modal-content">
        <div class="tag-view__modal-header">
            <h3 class="tag-view__modal-title">Xác Nhận Xóa Tag</h3>
            <button type="button" class="tag-view__modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tag-view__modal-body">
            <p>Bạn có chắc chắn muốn xóa tag <strong>"<?= htmlspecialchars($tag['name']) ?>"</strong>?</p>
            <?php if ($usageCount > 0): ?>
                <p class="tag-view__modal-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tag này đang được sử dụng trong <?= $usageCount ?> bài viết và sẽ bị gỡ khỏi tất cả các bài viết!
                </p>
            <?php endif; ?>
        </div>
        <div class="tag-view__modal-footer">
            <button type="button" class="tag-view__btn tag-view__btn--secondary" onclick="closeDeleteModal()">
                Hủy
            </button>
            <form method="POST" action="<?= Router::url('/admin/tags/delete/' . $tag['id']) ?>" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="force" value="1">
                <button type="submit" class="tag-view__btn tag-view__btn--danger">
                    <i class="fas fa-trash"></i> Xóa Tag
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Close modal on outside click
    document.getElementById('deleteModal').addEventListener('click', (e) => {
        if (e.target.id === 'deleteModal') {
            closeDeleteModal();
        }
    });
</script>