<div class="tag-form">
    <div class="tag-form__header">
        <h1 class="tag-form__title">Thêm Tag Mới</h1>
        <a href="<?= Router::url('/admin/tags') ?>" class="tag-form__btn tag-form__btn--secondary">
            <i class="fas fa-arrow-left"></i> Quay Lại
        </a>
    </div>

    <div class="tag-form__container">
        <form method="POST" action="<?= Router::url('/admin/tags/create') ?>" class="tag-form__form">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="tag-form__card">
                <div class="tag-form__card-header">
                    <h2 class="tag-form__card-title">Thông Tin Tag</h2>
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
                            placeholder="Ví dụ: Laravel, PHP, JavaScript..."
                            required
                            autofocus>
                        <p class="tag-form__help">Tên hiển thị của tag</p>
                    </div>

                    <!-- Slug -->
                    <div class="tag-form__group">
                        <label for="slug" class="tag-form__label">
                            Slug
                        </label>
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            class="tag-form__input"
                            placeholder="Để trống để tự động tạo từ tên">
                        <p class="tag-form__help">
                            URL thân thiện (tự động tạo nếu để trống). Ví dụ: laravel, php, javascript
                        </p>
                    </div>

                    <!-- Preview -->
                    <div class="tag-form__preview" id="tagPreview" style="display: none;">
                        <h3 class="tag-form__preview-title">Xem Trước</h3>
                        <div class="tag-form__preview-content">
                            <span class="tag-form__preview-badge" id="previewBadge">Tag Name</span>
                            <div class="tag-form__preview-url">
                                <strong>URL:</strong>
                                <code id="previewUrl"><?= Router::url('/tag/') ?></code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="tag-form__actions">
                <a href="<?= Router::url('/admin/tags') ?>" class="tag-form__btn tag-form__btn--secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
                <button type="submit" class="tag-form__btn tag-form__btn--primary">
                    <i class="fas fa-save"></i> Lưu Tag
                </button>
            </div>
        </form>

        <!-- Help Card -->
        <div class="tag-form__help-card">
            <div class="tag-form__help-header">
                <i class="fas fa-info-circle"></i>
                <h3>Hướng Dẫn</h3>
            </div>
            <ul class="tag-form__help-list">
                <li>
                    <i class="fas fa-check"></i>
                    Tên tag nên ngắn gọn và dễ hiểu
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    Slug sẽ tự động tạo từ tên nếu bạn để trống
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    Tags giúp phân loại và tìm kiếm bài viết dễ dàng hơn
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    Nên sử dụng tags phổ biến để tăng khả năng tìm kiếm
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Auto generate slug
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const previewBadge = document.getElementById('previewBadge');
    const previewUrl = document.getElementById('previewUrl');
    const tagPreview = document.getElementById('tagPreview');

    let slugEdited = false;

    slugInput.addEventListener('input', () => {
        slugEdited = slugInput.value.length > 0;
    });

    nameInput.addEventListener('input', () => {
        const name = nameInput.value.trim();

        if (name) {
            // Show preview
            tagPreview.style.display = 'block';
            previewBadge.textContent = name;

            // Auto generate slug if not manually edited
            if (!slugEdited) {
                const slug = generateSlug(name);
                slugInput.value = slug;
                previewUrl.textContent = '<?= Router::url('/tag/') ?>' + slug;
            }
        } else {
            tagPreview.style.display = 'none';
        }
    });

    slugInput.addEventListener('input', () => {
        const slug = slugInput.value.trim();
        if (slug) {
            previewUrl.textContent = '<?= Router::url('/tag/') ?>' + slug;
        }
    });

    function generateSlug(text) {
        // Convert Vietnamese to ASCII
        const from = 'àáãảạăằắẳẵặâầấẩẫậèéẻẽẹêềếểễệđùúủũụưừứửữựòóỏõọôồốổỗộơờớởỡợìíỉĩịäëïîöüûñçýỳỹỵỷ';
        const to = 'aaaaaaaaaaaaaaaaaeeeeeeeeeeeduuuuuuuuuuuoooooooooooooooooiiiiiaeiiouuncyyyyy';

        for (let i = 0; i < from.length; i++) {
            text = text.replace(new RegExp(from[i], 'gi'), to[i]);
        }

        return text
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
</script>