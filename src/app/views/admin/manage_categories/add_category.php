<!-- Add Category Modal -->
<div class="category-modal" id="addCategoryModal">
    <div class="category-modal__overlay" onclick="closeAddModal()"></div>
    <div class="category-modal__dialog">
        <div class="category-modal__header">
            <h3 class="category-modal__title">
                <i class="fas fa-plus-circle"></i>
                <span>Thêm danh mục mới</span>
            </h3>
            <button class="category-modal__close" onclick="closeAddModal()" type="button">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="category-modal__body">
            <form id="addCategoryForm"
                method="POST"
                action="<?php echo Router::url('/admin/categories/create'); ?>"
                class="category-form">

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <!-- Tên danh mục -->
                <div class="form-group">
                    <label class="form-label form-label--required">
                        <i class="fas fa-tag"></i>
                        Tên danh mục
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="add_category_name"
                        class="form-control"
                        placeholder="Ví dụ: Công nghệ, Lập trình, Thiết kế..."
                        required
                        autocomplete="off"
                        autofocus
                        onkeyup="generateAddSlug()">
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i>
                        Tên danh mục sẽ hiển thị trên website
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
                            id="add_category_slug"
                            class="form-control form-control--slug"
                            placeholder="tu-dong-tao-tu-ten"
                            autocomplete="off">
                        <button
                            type="button"
                            class="btn btn--info btn--icon"
                            onclick="generateAddSlug()"
                            title="Tạo lại slug">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <small class="form-help">
                        <i class="fas fa-lightbulb"></i>
                        Để trống để tự động tạo từ tên danh mục
                    </small>
                    <div class="form-preview" id="add_slug_preview" style="display: none;">
                        <i class="fas fa-eye"></i>
                        <span>URL: </span>
                        <code id="add_slug_preview_text"></code>
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
                        id="add_category_description"
                        class="form-control"
                        rows="4"
                        placeholder="Nhập mô tả ngắn về danh mục này..."></textarea>
                    <div class="form-counter">
                        <span id="add_desc_counter">0</span>/500 ký tự
                    </div>
                    <small class="form-help">
                        <i class="fas fa-info-circle"></i>
                        Mô tả giúp người đọc và SEO hiểu rõ hơn về danh mục
                    </small>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn--secondary" onclick="closeAddModal()">
                        <i class="fas fa-times"></i>
                        <span>Hủy</span>
                    </button>
                    <button type="reset" class="btn btn--secondary" onclick="resetAddForm()">
                        <i class="fas fa-undo"></i>
                        <span>Làm mới</span>
                    </button>
                    <button type="submit" class="btn btn--primary" id="add_submit_btn">
                        <i class="fas fa-save"></i>
                        <span>Tạo danh mục</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ============================================
   ADD MODAL STYLES
   ============================================ */
    .category-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .category-modal--open {
        display: flex;
        animation: fadeIn 0.3s ease;
    }

    .category-modal__overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(3px);
    }

    .category-modal__dialog {
        position: relative;
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        animation: slideUp 0.3s ease;
    }

    .category-modal__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 25px;
        border-bottom: 2px solid #e3e6f0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px 12px 0 0;
    }

    .category-modal__title {
        margin: 0;
        font-size: 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .category-modal__close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        font-size: 20px;
        color: white;
        cursor: pointer;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .category-modal__close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .category-modal__body {
        padding: 25px;
        overflow-y: auto;
        flex: 1;
    }

    /* ============================================
   FORM STYLES
   ============================================ */
    .category-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label i {
        color: #667eea;
        font-size: 13px;
    }

    .form-label--required::after {
        content: '*';
        color: #e74a3b;
        margin-left: 3px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #d1d3e2;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.3s;
        background: #fff;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control::placeholder {
        color: #a5a8b5;
    }

    .form-control--slug {
        font-family: 'Monaco', 'Courier New', monospace;
        color: #667eea;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
        line-height: 1.6;
    }

    .form-input-group {
        display: flex;
        gap: 10px;
    }

    .form-input-group .form-control {
        flex: 1;
    }

    .form-help {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #858796;
    }

    .form-help i {
        font-size: 11px;
    }

    .form-preview {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        background: #f8f9fc;
        border-radius: 6px;
        font-size: 13px;
        margin-top: 8px;
        border: 1px solid #e3e6f0;
    }

    .form-preview i {
        color: #667eea;
    }

    .form-preview code {
        background: white;
        padding: 4px 8px;
        border-radius: 4px;
        color: #667eea;
        font-weight: 600;
    }

    .form-counter {
        text-align: right;
        font-size: 12px;
        color: #858796;
        margin-top: 5px;
    }

    /* ============================================
   FORM ACTIONS
   ============================================ */
    .form-actions {
        display: flex;
        gap: 10px;
        padding-top: 20px;
        border-top: 2px solid #e3e6f0;
        margin-top: 10px;
    }

    .form-actions .btn {
        flex: 1;
    }

    .form-actions .btn--primary {
        flex: 2;
    }

    /* ============================================
   BUTTON STYLES
   ============================================ */
    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn--primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn--primary:hover:not(:disabled) {
        background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn--secondary {
        background: #f8f9fc;
        color: #5a5c69;
        border: 2px solid #e3e6f0;
    }

    .btn--secondary:hover {
        background: #e3e6f0;
        border-color: #d1d3e2;
    }

    .btn--info {
        background: #36b9cc;
        color: white;
    }

    .btn--info:hover {
        background: #258391;
    }

    .btn--icon {
        padding: 12px;
        width: 48px;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn i {
        font-size: 14px;
    }

    /* ============================================
   ANIMATIONS
   ============================================ */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* ============================================
   RESPONSIVE
   ============================================ */
    @media (max-width: 768px) {
        .category-modal {
            padding: 10px;
        }

        .category-modal__dialog {
            max-width: 100%;
            max-height: 95vh;
        }

        .category-modal__header,
        .category-modal__body {
            padding: 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {

        .category-modal__header,
        .category-modal__body {
            padding: 15px;
        }

        .category-modal__title {
            font-size: 18px;
        }

        .form-input-group {
            flex-direction: column;
        }

        .btn--icon {
            width: 100%;
        }
    }
</style>

<script>
    // ============================================
    // ADD FORM FUNCTIONS
    // ============================================

    function openAddModal() {
        const modal = document.getElementById('addCategoryModal');
        modal.style.display = 'flex';
        modal.classList.add('category-modal--open');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            document.getElementById('add_category_name').focus();
        }, 300);
    }

    function closeAddModal() {
        const modal = document.getElementById('addCategoryModal');
        modal.style.display = 'none';
        modal.classList.remove('category-modal--open');
        document.body.style.overflow = 'auto';
        resetAddForm();
    }

    function resetAddForm() {
        document.getElementById('addCategoryForm').reset();
        document.getElementById('add_slug_preview').style.display = 'none';
        updateAddDescCounter();
    }

    function generateAddSlug() {
        const name = document.getElementById('add_category_name').value;
        const slug = vietnameseToSlug(name);
        document.getElementById('add_category_slug').value = slug;

        // Show preview
        if (slug) {
            const preview = document.getElementById('add_slug_preview');
            const previewText = document.getElementById('add_slug_preview_text');
            previewText.textContent = window.location.origin + '/category/' + slug;
            preview.style.display = 'flex';
        }
    }

    function updateAddDescCounter() {
        const textarea = document.getElementById('add_category_description');
        const counter = document.getElementById('add_desc_counter');
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
        // Description counter
        const addDesc = document.getElementById('add_category_description');
        if (addDesc) {
            addDesc.addEventListener('input', updateAddDescCounter);
        }

        // Form submit
        const addForm = document.getElementById('addCategoryForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                const name = document.getElementById('add_category_name').value.trim();

                if (!name) {
                    e.preventDefault();
                    alert('Vui lòng nhập tên danh mục!');
                    document.getElementById('add_category_name').focus();
                    return false;
                }

                // Auto-generate slug if empty
                const slug = document.getElementById('add_category_slug').value.trim();
                if (!slug) {
                    generateAddSlug();
                }

                // Show loading
                const submitBtn = document.getElementById('add_submit_btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Đang tạo...</span>';
            });
        }

        // Close on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('addCategoryModal');
                if (modal && modal.classList.contains('category-modal--open')) {
                    closeAddModal();
                }
            }
        });
    });
</script>