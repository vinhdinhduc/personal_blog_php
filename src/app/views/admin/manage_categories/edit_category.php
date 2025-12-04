<!-- Edit Category Modal -->
<div class="category-modal" id="editCategoryModal">
    <div class="category-modal__overlay" onclick="closeEditModal()"></div>
    <div class="category-modal__dialog">
        <div class="category-modal__header category-modal__header--edit">
            <h3 class="category-modal__title">
                <i class="fas fa-edit"></i>
                <span>Chỉnh sửa danh mục</span>
            </h3>
            <button class="category-modal__close" onclick="closeEditModal()" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="category-modal__body">
            <!-- Category Info Banner -->
            <div class="edit-banner" id="edit_banner">
                <div class="edit-banner__icon">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="edit-banner__info">
                    <div class="edit-banner__label">Đang chỉnh sửa:</div>
                    <div class="edit-banner__name" id="edit_banner_name">-</div>
                    <div class="edit-banner__meta">
                        <span id="edit_banner_posts">0 bài viết</span>
                        <span>•</span>
                        <span id="edit_banner_id">ID: -</span>
                    </div>
                </div>
            </div>

            <form id="editCategoryForm"
                method="POST"
                action="<?php echo Router::url('/admin/categories/update'); ?>"
                class="category-form">

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" id="edit_category_id">

                <!-- Tên danh mục -->
                <div class="form-group">
                    <label class="form-label form-label--required">
                        <i class="fas fa-tag"></i>
                        Tên danh mục
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="edit_category_name"
                        class="form-control"
                        placeholder="Tên danh mục"
                        required
                        autocomplete="off"
                        onkeyup="generateEditSlug()">
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i>
                        Thay đổi tên sẽ ảnh hưởng đến tất cả bài viết trong danh mục
                    </small>
                </div>

                <!-- Slug -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-link"></i>
                        URL thân thiện (Slug)
                    </label>
                    <div class="form-input-group">
                        <input
                            type="text"
                            name="slug"
                            id="edit_category_slug"
                            class="form-control form-control--slug"
                            placeholder="url-than-thien"
                            autocomplete="off"
                            readonly>
                        <button
                            type="button"
                            class="btn btn--warning btn--icon"
                            onclick="unlockEditSlug()"
                            id="edit_slug_lock"
                            title="Mở khóa để chỉnh sửa">
                            <i class="fas fa-lock"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn--info btn--icon"
                            onclick="generateEditSlug()"
                            title="Tạo lại slug">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="form-alert form-alert--warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Cảnh báo: Thay đổi slug có thể ảnh hưởng đến SEO và các liên kết cũ</span>
                    </div>
                    <div class="form-preview" id="edit_slug_preview">
                        <i class="fas fa-eye"></i>
                        <span>URL: </span>
                        <code id="edit_slug_preview_text"></code>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left"></i>
                        Mô tả
                    </label>
                    <textarea
                        name="description"
                        id="edit_category_description"
                        class="form-control"
                        rows="4"
                        placeholder="Mô tả về danh mục..."></textarea>
                    <div class="form-counter">
                        <span id="edit_desc_counter">0</span>/500 ký tự
                    </div>
                </div>

                <!-- Change History -->
                <div class="form-history" id="edit_history">
                    <div class="form-history__title">
                        <i class="fas fa-history"></i>
                        Lịch sử thay đổi
                    </div>
                    <div class="form-history__items">
                        <div class="form-history__item">
                            <i class="fas fa-circle"></i>
                            <span>Tên cũ: <strong id="edit_old_name">-</strong></span>
                        </div>
                        <div class="form-history__item">
                            <i class="fas fa-circle"></i>
                            <span>Slug cũ: <code id="edit_old_slug">-</code></span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn--secondary" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                        <span>Hủy</span>
                    </button>
                    <button type="button" class="btn btn--secondary" onclick="resetEditForm()">
                        <i class="fas fa-undo"></i>
                        <span>Hoàn tác</span>
                    </button>
                    <button type="submit" class="btn btn--success" id="edit_submit_btn">
                        <i class="fas fa-check"></i>
                        <span>Cập nhật</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ============================================
   EDIT MODAL SPECIFIC STYLES
   ============================================ */
    .category-modal__header--edit {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }

    /* Edit Banner */
    .edit-banner {
        display: flex;
        gap: 15px;
        padding: 15px;
        background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
        border-radius: 8px;
        margin-bottom: 20px;
        border: 2px solid #e3e6f0;
    }

    .edit-banner__icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .edit-banner__info {
        flex: 1;
    }

    .edit-banner__label {
        font-size: 11px;
        color: #858796;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .edit-banner__name {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .edit-banner__meta {
        font-size: 12px;
        color: #858796;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    /* Form Alert */
    .form-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 6px;
        font-size: 13px;
        margin-top: 8px;
    }

    .form-alert--warning {
        background: rgba(246, 194, 62, 0.1);
        border: 1px solid rgba(246, 194, 62, 0.3);
        color: #c87800;
    }

    .form-alert i {
        font-size: 14px;
    }

    /* Form History */
    .form-history {
        padding: 15px;
        background: #f8f9fc;
        border-radius: 8px;
        border: 1px solid #e3e6f0;
    }

    .form-history__title {
        font-size: 13px;
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-history__title i {
        color: #858796;
    }

    .form-history__items {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-history__item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #5a5c69;
    }

    .form-history__item i {
        font-size: 6px;
        color: #858796;
    }

    .form-history__item strong {
        color: #2c3e50;
    }

    .form-history__item code {
        background: white;
        padding: 2px 8px;
        border-radius: 4px;
        color: #1cc88a;
        font-family: 'Monaco', 'Courier New', monospace;
    }

    /* Button Variants */
    .btn--success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
    }

    .btn--success:hover:not(:disabled) {
        background: linear-gradient(135deg, #17a673 0%, #0f6848 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(28, 200, 138, 0.4);
    }

    .btn--warning {
        background: #f6c23e;
        color: #2c3e50;
    }

    .btn--warning:hover {
        background: #f4b619;
    }

    /* Locked Input */
    .form-control:read-only {
        background: #f8f9fc;
        cursor: not-allowed;
        color: #858796;
    }

    /* ============================================
   RESPONSIVE
   ============================================ */
    @media (max-width: 480px) {
        .edit-banner {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .edit-banner__meta {
            justify-content: center;
        }
    }
</style>

<script>
    // ============================================
    // EDIT FORM FUNCTIONS
    // ============================================

    let originalCategoryData = {};

    function openEditModal(category) {
        // Store original data
        originalCategoryData = {
            ...category
        };

        // Fill banner
        document.getElementById('edit_banner_name').textContent = category.name;
        document.getElementById('edit_banner_posts').textContent = (category.post_count || 0) + ' bài viết';
        document.getElementById('edit_banner_id').textContent = 'ID: ' + category.id;

        // Fill form
        document.getElementById('edit_category_id').value = category.id;
        document.getElementById('edit_category_name').value = category.name;
        document.getElementById('edit_category_slug').value = category.slug;
        document.getElementById('edit_category_description').value = category.description || '';

        // Update form action
        const form = document.getElementById('editCategoryForm');
        const baseUrl = form.action.split('/admin/categories/')[0] + '/admin/categories';
        form.action = `${baseUrl}/update/${category.id}`;

        // Show history
        document.getElementById('edit_old_name').textContent = category.name;
        document.getElementById('edit_old_slug').textContent = category.slug;

        // Update preview
        updateEditSlugPreview();
        updateEditDescCounter();

        // Lock slug by default
        lockEditSlug();

        // Open modal
        const modal = document.getElementById('editCategoryModal');
        modal.style.display = 'flex';
        modal.classList.add('category-modal--open');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            document.getElementById('edit_category_name').focus();
            document.getElementById('edit_category_name').select();
        }, 300);
    }

    function closeEditModal() {
        const modal = document.getElementById('editCategoryModal');
        modal.style.display = 'none';
        modal.classList.remove('category-modal--open');
        document.body.style.overflow = 'auto';
        originalCategoryData = {};
    }

    function resetEditForm() {
        if (Object.keys(originalCategoryData).length > 0) {
            document.getElementById('edit_category_name').value = originalCategoryData.name;
            document.getElementById('edit_category_slug').value = originalCategoryData.slug;
            document.getElementById('edit_category_description').value = originalCategoryData.description || '';
            updateEditSlugPreview();
            updateEditDescCounter();
            lockEditSlug();
        }
    }

    function generateEditSlug() {
        const name = document.getElementById('edit_category_name').value;
        const slug = vietnameseToSlug(name);
        document.getElementById('edit_category_slug').value = slug;
        updateEditSlugPreview();
    }

    function updateEditSlugPreview() {
        const slug = document.getElementById('edit_category_slug').value;
        const previewText = document.getElementById('edit_slug_preview_text');
        previewText.textContent = window.location.origin + '/category/' + slug;
    }

    function lockEditSlug() {
        const slugInput = document.getElementById('edit_category_slug');
        const lockBtn = document.getElementById('edit_slug_lock');

        slugInput.readOnly = true;
        lockBtn.innerHTML = '<i class="fas fa-lock"></i>';
        lockBtn.title = 'Mở khóa để chỉnh sửa';
        lockBtn.classList.remove('btn--success');
        lockBtn.classList.add('btn--warning');
    }

    function unlockEditSlug() {
        const slugInput = document.getElementById('edit_category_slug');
        const lockBtn = document.getElementById('edit_slug_lock');

        if (slugInput.readOnly) {
            if (confirm('Cảnh báo: Thay đổi slug có thể ảnh hưởng đến SEO!\n\nBạn có chắc muốn chỉnh sửa slug?')) {
                slugInput.readOnly = false;
                lockBtn.innerHTML = '<i class="fas fa-lock-open"></i>';
                lockBtn.title = 'Khóa lại';
                lockBtn.classList.remove('btn--warning');
                lockBtn.classList.add('btn--success');
                slugInput.focus();
            }
        } else {
            lockEditSlug();
        }
    }

    function updateEditDescCounter() {
        const textarea = document.getElementById('edit_category_description');
        const counter = document.getElementById('edit_desc_counter');
        const length = textarea.value.length;
        counter.textContent = length;

        if (length > 500) {
            counter.style.color = '#e74a3b';
            textarea.value = textarea.value.substring(0, 500);
        } else {
            counter.style.color = length > 400 ? '#f6c23e' : '#858796';
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Slug input change
        const editSlug = document.getElementById('edit_category_slug');
        if (editSlug) {
            editSlug.addEventListener('input', updateEditSlugPreview);
        }

        // Description counter
        const editDesc = document.getElementById('edit_category_description');
        if (editDesc) {
            editDesc.addEventListener('input', updateEditDescCounter);
        }

        // Form submit
        const editForm = document.getElementById('editCategoryForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const name = document.getElementById('edit_category_name').value.trim();

                if (!name) {
                    e.preventDefault();
                    alert('Vui lòng nhập tên danh mục!');
                    document.getElementById('edit_category_name').focus();
                    return false;
                }

                const slug = document.getElementById('edit_category_slug').value.trim();
                if (!slug) {
                    e.preventDefault();
                    alert('Slug không được để trống!');
                    document.getElementById('edit_category_slug').focus();
                    return false;
                }

                // Check if changed
                const changed =
                    name !== originalCategoryData.name ||
                    slug !== originalCategoryData.slug ||
                    editDesc.value.trim() !== (originalCategoryData.description || '');

                if (!changed) {
                    e.preventDefault();
                    alert('Bạn chưa thay đổi gì!');
                    return false;
                }

                // Confirm if slug changed
                if (slug !== originalCategoryData.slug) {
                    if (!confirm('Bạn đã thay đổi slug. Điều này có thể ảnh hưởng đến SEO.\n\nBạn có chắc muốn tiếp tục?')) {
                        e.preventDefault();
                        return false;
                    }
                }

                // Show loading
                const submitBtn = document.getElementById('edit_submit_btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Đang cập nhật...</span>';
            });
        }

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('editCategoryModal');
                if (modal && modal.classList.contains('category-modal--open')) {
                    closeEditModal();
                }
            }
        });
    });
</script>