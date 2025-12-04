<?php


?>
<div class="tag-form">
    <div class="tag-form__header">
        <h1 class="tag-form__title">Chỉnh Sửa Tag</h1>
        <a href="<?= Router::url('/admin/tags') ?>" class="tag-form__btn tag-form__btn--secondary">
            <i class="fas fa-arrow-left"></i> Quay Lại
        </a>
    </div>

    <div class="tag-form__container">
        <form method="POST" action="<?= Router::url('/admin/tags/update/' . $tag['id']) ?>" class="tag-form__form">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="tag-form__card">
                <div class="tag-form__card-header">
                    <h2 class="tag-form__card-title">Thông Tin Tag</h2>
                    <?php if ($usageCount > 0): ?>
                        <span class="tag-form__badge tag-form__badge--info">
                            <i class="fas fa-file-alt"></i> <?= $usageCount ?> bài viết
                        </span>
                    <?php else: ?>
                        <span class="tag-form__badge tag-form__badge--warning">
                            <i class="fas fa-exclamation-circle"></i> Chưa sử dụng
                        </span>
                    <?php endif; ?>
                </div>
                <div class="tag-form__card-body">
                    <!-- Name -->
                    <div class="tag-form__group">
                        <label for="name" class="tag-form__label">
                            Tên Tag <span class="tag-form__required">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="tag-form__input"
                            value="<?= htmlspecialchars($tag['name']) ?>"
                            required
                            autofocus>
                        <p class="tag-form__help">Tên hiển thị của tag</p>
                    </div>

                    <!-- Slug -->
                    <div class="tag-form__group">
                        <label for="slug" class="tag-form__label">
                            Slug <span class="tag-form__required">*</span>
                        </label>
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            class="tag-form__input"
                            value="<?= htmlspecialchars($tag['slug']) ?>"
                            required>
                        <p class="tag-form__help">
                            URL thân thiện. Ví dụ: laravel, php, javascript
                        </p>
                    </div>

                    <!-- Preview -->
                    <div class="tag-form__preview">
                        <h3 class="tag-form__preview-title">Xem Trước</h3>
                        <div class="tag-form__preview-content">
                            <span class="tag-form__preview-badge" id="previewBadge"><?= htmlspecialchars($tag['name']) ?></span>
                            <div class="tag-form__preview-url">
                                <strong>URL:</strong>
                                <code id="previewUrl"><?= Router::url('/tag/' . $tag['slug']) ?></code>
                            </div>
                        </div>
                    </div>

                    <!-- //Thông tin -->
                    <div class="tag-form__info">
                        <div class="tag-form__info-item">
                            <strong>ID:</strong> <?= $tag['id'] ?>
                        </div>
                        <div class="tag-form__info-item">
                            <strong>Ngày tạo:</strong>
                            <?= date('d/m/Y H:i', strtotime($tag['created_at'])) ?>
                        </div>

                    </div>

                    <!-- Warning if in use -->
                    <?php if ($usageCount > 0): ?>
                        <div class="tag-form__warning">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Lưu ý:</strong> Tag này đang được sử dụng trong <?= $usageCount ?> bài viết.
                                Thay đổi slug sẽ ảnh hưởng đến URL của các trang tag.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="tag-form__actions">
                <button
                    type="button"
                    class="tag-form__btn tag-form__btn--danger"
                    onclick="confirmDelete(<?= $tag['id'] ?>, '<?= htmlspecialchars($tag['name']) ?>', <?= $usageCount ?>)">
                    <i class="fas fa-trash"></i> Xóa Tag
                </button>
                <div class="tag-form__actions-right">
                    <a href="<?= Router::url('/admin/tags') ?>" class="tag-form__btn tag-form__btn--secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                    <button type="submit" class="tag-form__btn tag-form__btn--primary">
                        <i class="fas fa-save"></i> Cập Nhật
                    </button>
                </div>
            </div>
        </form>

        <!-- Help Card -->
        <div class="tag-form__help-card">
            <div class="tag-form__help-header">
                <i class="fas fa-chart-line"></i>
                <h3>Thống Kê</h3>
            </div>
            <div class="tag-form__stats">
                <div class="tag-form__stat">
                    <div class="tag-form__stat-value"><?= $usageCount ?></div>
                    <div class="tag-form__stat-label">Bài viết</div>
                </div>
                <div class="tag-form__stat">
                    <div class="tag-form__stat-value"><?= strlen($tag['name']) ?></div>
                    <div class="tag-form__stat-label">Ký tự</div>
                </div>
            </div>

            <?php if ($usageCount > 0): ?>
                <div class="tag-form__help-actions">
                    <a href="<?= Router::url('/tag/' . $tag['slug']) ?>"
                        class="tag-form__btn tag-form__btn--secondary tag-form__btn--block"
                        target="_blank">
                        <i class="fas fa-external-link-alt"></i> Xem Trang Tag
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="tag-form__modal" id="deleteModal">
    <div class="tag-form__modal-content">
        <div class="tag-form__modal-header">
            <h3 class="tag-form__modal-title">Xác Nhận Xóa Tag</h3>
            <button type="button" class="tag-form__modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tag-form__modal-body">
            <p id="deleteMessage"></p>
            <p class="tag-form__modal-warning" id="deleteWarning" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                Tag này đang được sử dụng và sẽ bị gỡ khỏi tất cả bài viết!
            </p>
        </div>
        <div class="tag-form__modal-footer">
            <button type="button" class="tag-form__btn tag-form__btn--secondary" onclick="closeDeleteModal()">
                Hủy
            </button>
            <form id="deleteForm" method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="force" value="1">
                <button type="submit" class="tag-form__btn tag-form__btn--danger">
                    <i class="fas fa-trash"></i> Xóa Tag
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const baseUrl = '<?= Router::url() ?>';

    // Auto update preview
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const previewBadge = document.getElementById('previewBadge');
    const previewUrl = document.getElementById('previewUrl');

    nameInput.addEventListener('input', () => {
        previewBadge.textContent = nameInput.value || 'Tag Name';
    });

    slugInput.addEventListener('input', () => {
        const slug = slugInput.value.trim();
        previewUrl.textContent = baseUrl + '/tag/' + (slug || 'slug');
    });

    // Delete functions
    function confirmDelete(id, name, usageCount) {
        const modal = document.getElementById('deleteModal');
        const message = document.getElementById('deleteMessage');
        const warning = document.getElementById('deleteWarning');
        const form = document.getElementById('deleteForm');

        message.innerHTML = `Bạn có chắc chắn muốn xóa tag <strong>"${name}"</strong>?`;

        if (usageCount > 0) {
            warning.style.display = 'block';
        } else {
            warning.style.display = 'none';
        }

        form.action = baseUrl + '/admin/tags/delete/' + id;
        modal.style.display = 'flex';
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