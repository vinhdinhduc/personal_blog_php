<?php



require_once __DIR__ . '/../../../helpers/ImageHelper.php';

// Set giá trị mặc định
$post = $post ?? [];
$isEdit = $isEdit ?? false;
$formAction = $formAction ?? '/admin/posts/store';
?>

<!-- Post Editor Container -->
<div class="post-editor">
    <div class="post-editor__main">
        <div class="post-card">
            <form action="<?= htmlspecialchars($formAction) ?>"
                method="POST"
                enctype="multipart/form-data"
                class="post-form"
                id="postForm">

                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <?php if ($isEdit): ?>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <?php endif; ?>

                <!-- Title Field -->
                <div class="form-group">
                    <label class="form-label form-label--required">
                        <i class="form-label__icon fas fa-heading"></i>
                        Tiêu đề bài viết
                    </label>
                    <input type="text"
                        name="title"
                        class="form-control form-control--large"
                        placeholder="Nhập tiêu đề bài viết..."
                        required
                        value="<?= htmlspecialchars($post['title'] ?? '') ?>"
                        id="postTitle">
                </div>

                <!-- Slug Field -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="form-label__icon fas fa-link"></i>
                        URL thân thiện (Slug)
                    </label>
                    <div class="form-group--inline">
                        <input type="text"
                            name="slug"
                            class="form-control"
                            placeholder="url-than-thien"
                            value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
                            id="postSlug"
                            style="flex: 1;">
                        <button type="button"
                            class="btn btn--info"
                            onclick="generateSlug()">
                            <i class="btn__icon fas fa-sync"></i>
                            Tạo lại
                        </button>
                    </div>
                    <small class="form-hint">
                        <i class="form-hint__icon fas fa-info-circle"></i>
                        Để trống để tự động tạo từ tiêu đề
                    </small>
                </div>

                <!-- Excerpt Field -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="form-label__icon fas fa-align-left"></i>
                        Mô tả ngắn (Excerpt)
                    </label>
                    <textarea name="excerpt"
                        class="form-control"
                        rows="3"
                        placeholder="Nhập mô tả ngắn cho bài viết..."
                        id="excerptField"
                        maxlength="250"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
                    <small class="form-hint">
                        <span id="excerptCounter">0</span>/250 ký tự
                    </small>
                </div>

                <!-- Rich Text Editor -->
                <div class="form-group">
                    <label class="form-label form-label--required">
                        <i class="form-label__icon fas fa-file-alt"></i>
                        Nội dung bài viết
                    </label>

                    <div class="editor-wrapper">
                        <!-- Quill Editor Container -->
                        <div id="editor" class="editor-content"></div>

                        <!-- Hidden textarea for form submission -->
                        <textarea name="content"
                            id="editorContent"
                            style="display: none;"
                            required><?= htmlspecialchars($post['content'] ?? '') ?></textarea>

                        <!-- Character Counter -->
                        <div class="editor-counter">
                            <span>
                                <i class="fas fa-keyboard"></i>
                                <span id="wordCount">0</span> từ
                            </span>
                            <span>
                                <i class="fas fa-text-height"></i>
                                <span id="charCount">0</span> ký tự
                            </span>
                        </div>
                    </div>

                    <small class="form-hint">
                        <i class="form-hint__icon fas fa-lightbulb"></i>
                        Hỗ trợ định dạng văn bản phong phú, hình ảnh và video
                    </small>
                </div>

            </form>
        </div>

        <!-- Statistics (Only for Edit Mode) -->
        <?php if ($isEdit && isset($post['views'])): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card__icon stat-card__icon--primary">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-card__value">
                        <?= number_format($post['views'] ?? 0) ?>
                    </div>
                    <div class="stat-card__label">Lượt xem</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__icon stat-card__icon--success">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-card__value">
                        <?= $post['comment_count'] ?? 0 ?>
                    </div>
                    <div class="stat-card__label">Bình luận</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card__icon stat-card__icon--danger">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-card__value">
                        <?= $post['likes'] ?? 0 ?>
                    </div>
                    <div class="stat-card__label">Lượt thích</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="post-card">
            <div class="btn-group btn-group--space-between">
                <div class="btn-group">
                    <a href="/admin/posts" class="btn btn--secondary">
                        <i class="btn__icon fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                </div>

                <div class="btn-group">
                    <button type="button"
                        id="btnSaveDraft"
                        class="btn btn--warning">
                        <i class="btn__icon fas fa-save"></i>
                        Lưu nháp
                    </button>
                    <button type="button"
                        id="btnPublish"
                        class="btn btn--success">
                        <i class="btn__icon fas fa-check"></i>
                        <?= $isEdit ? 'Cập nhật' : 'Tạo mới' ?> & Xuất bản
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="post-editor__sidebar">

        <!-- Featured Image Card -->
        <div class="post-card post-card--compact">
            <div class="post-card__header">
                <i class="post-card__icon fas fa-image"></i>
                <h3 class="post-card__title">Ảnh đại diện</h3>
            </div>

            <div class="post-card__body">
                <div class="image-upload">
                    <div class="image-upload__preview" id="thumbnailPreview">
                        <?php if ($isEdit && !empty($post['cover_image'])): ?>
                            <img src="<?= ImageHelper::postCover($post['cover_image']) ?>"
                                alt="<?= htmlspecialchars($post['title'] ?? 'Cover image') ?>"
                                class="image-upload__preview-img">
                        <?php else: ?>
                            <div class="image-upload__placeholder"
                                onclick="document.getElementById('thumbnailInput').click()">
                                <i class="image-upload__placeholder-icon fas fa-image"></i>
                                <span class="image-upload__placeholder-text">Chưa có ảnh đại diện</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <input type="file"
                        name="cover_image"
                        class="image-upload__input form-control"
                        accept="image/*"
                        id="thumbnailInput"
                        onchange="previewImage(this)"
                        form="postForm">

                    <small class="form-hint">
                        <i class="form-hint__icon fas fa-info-circle"></i>
                        Kích thước khuyến nghị: 1200x630px
                    </small>
                </div>
            </div>
        </div>

        <!-- Category Card -->
        <div class="post-card post-card--compact">
            <div class="post-card__header">
                <i class="post-card__icon fas fa-folder"></i>
                <h3 class="post-card__title">Danh mục</h3>
            </div>

            <div class="post-card__body">
                <div class="form-group">
                    <select name="category_id"
                        class="form-control"
                        required
                        form="postForm">
                        <option value="">-- Chọn danh mục --</option>
                        <?php if (isset($categories) && count($categories) > 0): ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"
                                    <?= (isset($post['category_id']) && $post['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tags Card -->
        <div class="post-card post-card--compact">
            <div class="post-card__header">
                <i class="post-card__icon fas fa-tags"></i>
                <h3 class="post-card__title">Thẻ (Tags)</h3>
            </div>

            <div class="post-card__body">
                <div class="form-group">
                    <?php
                    //  XỬ LÝ TAGS: Chuyển array thành string
                    $tagsValue = '';
                    if (isset($post['tags'])) {
                        if (is_array($post['tags'])) {
                            // Lấy tên các tag từ array
                            $tagNames = [];
                            foreach ($post['tags'] as $tag) {
                                if (is_array($tag) && isset($tag['name'])) {
                                    $tagNames[] = $tag['name'];
                                } elseif (is_string($tag)) {
                                    $tagNames[] = $tag;
                                }
                            }
                            $tagsValue = implode(', ', $tagNames);
                        } else {
                            // Nếu đã là string
                            $tagsValue = $post['tags'];
                        }
                    }
                    ?>
                    <input type="text"
                        name="tags"
                        class="form-control"
                        placeholder="Nhập thẻ, cách nhau bởi dấu phẩy..."
                        value="<?= htmlspecialchars($tagsValue) ?>"
                        form="postForm">
                    <small class="form-hint">
                        Ví dụ: công nghệ, tin tức, giải trí
                    </small>
                </div>
            </div>
        </div>

        <!-- Status & Options Card -->
        <div class="post-card post-card--compact">
            <div class="post-card__header">
                <i class="post-card__icon fas fa-cog"></i>
                <h3 class="post-card__title">Trạng thái & Tùy chọn</h3>
            </div>

            <div class="post-card__body">
                <!-- Current Status (Only for Edit) -->
                <?php if ($isEdit && isset($post['status'])): ?>
                    <div class="form-group">
                        <label class="form-label">Trạng thái hiện tại</label>
                        <div class="status-box">
                            <?php if ($post['status'] == 'published'): ?>
                                <span class="badge badge--success">
                                    <i class="badge__icon fas fa-check-circle"></i>
                                    Đã xuất bản
                                </span>
                            <?php elseif ($post['status'] == 'draft'): ?>
                                <span class="badge badge--warning">
                                    <i class="badge__icon fas fa-file-alt"></i>
                                    Bản nháp
                                </span>
                            <?php else: ?>
                                <span class="badge badge--info">
                                    <i class="badge__icon fas fa-clock"></i>
                                    Chờ duyệt
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Hidden status field -->
                <input type="hidden" name="status" id="postStatus" value="draft" form="postForm">

                <!-- Featured Post -->
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox"
                            name="featured"
                            value="1"
                            form="postForm"
                            <?= !empty($post['featured']) ? 'checked' : '' ?>>
                        <span class="form-checkbox__label">Bài viết nổi bật</span>
                    </label>
                </div>

                <!-- Allow Comments -->
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox"
                            name="allow_comments"
                            value="1"
                            form="postForm"
                            <?= (!empty($post['allow_comments']) || !$isEdit) ? 'checked' : '' ?>>
                        <span class="form-checkbox__label">Cho phép bình luận</span>
                    </label>
                </div>

                <!-- Publish Date -->
                <div class="form-group">
                    <label class="form-label">Ngày xuất bản</label>
                    <input type="datetime-local"
                        name="published_at"
                        class="form-control"
                        form="postForm"
                        value="<?= isset($post['published_at']) ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : date('Y-m-d\TH:i') ?>">
                </div>

                <!-- Metadata (Only for Edit) -->
                <?php if ($isEdit && isset($post['created_at'])): ?>
                    <div class="metadata">
                        <div class="metadata__item">
                            <i class="metadata__icon fas fa-clock"></i>
                            <span class="metadata__text">
                                Tạo lúc: <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                            </span>
                        </div>
                        <div class="metadata__item">
                            <i class="metadata__icon fas fa-edit"></i>
                            <span class="metadata__text">
                                Cập nhật: <?= date('d/m/Y H:i', strtotime($post['updated_at'] ?? $post['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div> <!-- .post-editor__sidebar -->
</div> <!-- .post-editor -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== FORM SCRIPT LOADED ===');

        //  KHAI BÁO TẤT CẢ BIẾN VÀ KIỂM TRA
        const form = document.getElementById('postForm');
        const btnSaveDraft = document.getElementById('btnSaveDraft');
        const btnPublish = document.getElementById('btnPublish');
        const statusField = document.getElementById('postStatus');
        const titleField = document.getElementById('postTitle');
        const editorContent = document.getElementById('editorContent');
        const excerptField = document.getElementById('excerptField');
        const metaDescField = document.getElementById('metaDescription');

        //  KIỂM TRA CÁC ELEMENT BẮT BUỘC
        if (!form) {
            console.error('❌ Form not found!');
            return;
        }

        if (!editorContent) {
            console.error('❌ editorContent field not found!');
            return;
        }

        if (!statusField) {
            console.error('❌ statusField not found!');
            return;
        }

        if (!titleField) {
            console.error('❌ titleField not found!');
            return;
        }

        console.log(' All required elements found:', {
            form: !!form,
            btnSaveDraft: !!btnSaveDraft,
            btnPublish: !!btnPublish,
            statusField: !!statusField,
            titleField: !!titleField,
            editorContent: !!editorContent,
            excerptField: !!excerptField,
            metaDescField: !!metaDescField
        });

        //  WAIT FOR QUILL EDITOR
        function waitForQuill(callback) {
            if (window.quillEditor) {
                console.log(' Quill Editor ready');
                callback();
            } else {
                console.warn('⏳ Waiting for Quill Editor...');
                setTimeout(function() {
                    if (window.quillEditor) {
                        console.log(' Quill Editor ready after delay');
                        callback();
                    } else {
                        console.error('❌ Quill Editor not initialized!');
                        alert('Lỗi: Editor chưa được khởi tạo. Vui lòng reload trang!');
                    }
                }, 1500);
            }
        }

        // Character counters
        function updateCounters() {
            if (excerptField) {
                const excerptCounter = document.getElementById('excerptCounter');
                if (excerptCounter) {
                    excerptCounter.textContent = excerptField.value.length;
                }
            }

            if (metaDescField) {
                const metaDescCounter = document.getElementById('metaDescCounter');
                if (metaDescCounter) {
                    metaDescCounter.textContent = metaDescField.value.length;
                }
            }

            if (window.quillEditor) {
                const text = window.quillEditor.getText().trim();
                const wordCount = document.getElementById('wordCount');
                const charCount = document.getElementById('charCount');

                if (wordCount) {
                    wordCount.textContent = text ? text.split(/\s+/).length : 0;
                }
                if (charCount) {
                    charCount.textContent = text.length;
                }
            }
        }

        //  Sync Quill content to hidden field
        function syncEditorContent() {
            if (!window.quillEditor) {
                console.error('❌ Quill Editor not available!');
                return '';
            }

            if (!editorContent) {
                console.error('❌ editorContent field not found!');
                return '';
            }

            const content = window.quillEditor.root.innerHTML;
            editorContent.value = content;

            console.log(' Content synced:', content.length, 'chars');

            updateCounters();
            return content;
        }

        //  Validate form
        function validateForm() {
            console.log('=== VALIDATION START ===');

            // Check required fields exist
            if (!titleField) {
                console.error('❌ titleField not found');
                alert('Lỗi hệ thống: Không tìm thấy trường tiêu đề');
                return false;
            }

            if (!editorContent) {
                console.error('❌ editorContent not found');
                alert('Lỗi hệ thống: Không tìm thấy trường nội dung');
                return false;
            }

            // Sync content first
            const content = syncEditorContent();
            const title = titleField.value.trim();

            console.log('Title:', title);
            console.log('Content length:', content.length);

            if (!title) {
                alert('Tiêu đề không được để trống!');
                titleField.focus();
                return false;
            }

            if (!window.quillEditor) {
                alert('Editor chưa sẵn sàng!');
                return false;
            }

            const textContent = window.quillEditor.getText().trim();

            if (!textContent || textContent.length === 0) {
                alert('Nội dung không được để trống!');
                console.log('❌ Content validation failed: Empty text');
                return false;
            }

            // Check category
            const categoryField = form.querySelector('select[name="category_id"]');
            if (categoryField && !categoryField.value) {
                alert('Vui lòng chọn danh mục!');
                categoryField.focus();
                return false;
            }

            console.log(' Validation passed!');
            return true;
        }

        //  Submit form with status
        function submitForm(status) {
            console.log('=== SUBMIT START ===');
            console.log('Status:', status);

            //  CHECK REQUIRED FIELDS AGAIN
            if (!statusField) {
                console.error('❌ statusField is null');
                alert('Lỗi hệ thống: Không tìm thấy trường trạng thái');
                return false;
            }

            if (!editorContent) {
                console.error('❌ editorContent is null');
                alert('Lỗi hệ thống: Không tìm thấy trường nội dung');
                return false;
            }

            // Sync editor content
            syncEditorContent();

            // Validate
            if (!validateForm()) {
                console.log('❌ Validation failed, aborting submit');
                return false;
            }

            // Set status
            statusField.value = status;
            console.log('Status field set to:', statusField.value);

            // Show loading state
            const btn = status === 'draft' ? btnSaveDraft : btnPublish;
            if (!btn) {
                console.error('❌ Button not found');
                return false;
            }

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="btn__icon fas fa-spinner fa-spin"></i> Đang xử lý...';

            // Debug log
            console.log('=== FORM SUBMIT DEBUG ===');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            console.log('Status:', statusField.value);
            console.log('Title:', titleField.value);
            console.log('Content length:', editorContent.value.length);
            console.log('Content preview:', editorContent.value.substring(0, 200));

            const categoryField = form.querySelector('select[name="category_id"]');
            if (categoryField) {
                console.log('Category:', categoryField.value);
            }

            //  Submit form
            console.log('📤 Submitting form...');

            try {
                form.submit();
            } catch (e) {
                console.error('❌ Form submit error:', e);
                btn.disabled = false;
                btn.innerHTML = originalText;
                alert('Lỗi khi submit form: ' + e.message);
            }
        }

        //  Setup buttons after Quill is ready
        waitForQuill(function() {
            // Listen for input changes
            if (excerptField) {
                excerptField.addEventListener('input', updateCounters);
            }
            if (metaDescField) {
                metaDescField.addEventListener('input', updateCounters);
            }

            // Initial count
            updateCounters();

            // Button click handlers
            if (btnSaveDraft) {
                btnSaveDraft.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('🔘 Draft button clicked');
                    submitForm('draft');
                });
            }

            if (btnPublish) {
                btnPublish.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('🔘 Publish button clicked');
                    submitForm('published');
                });
            }

            // Sync content when Quill changes
            if (window.quillEditor) {
                window.quillEditor.on('text-change', function() {
                    syncEditorContent();
                });
            }

            console.log(' Form setup complete!');
        });

        // Warn before leaving with unsaved changes
        let formChanged = false;

        form.addEventListener('change', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Bạn có thay đổi chưa lưu. Bạn có chắc muốn rời khỏi trang?';
                return e.returnValue;
            }
        });

        form.addEventListener('submit', function() {
            formChanged = false;
        });
    });

    // Generate slug from title
    function generateSlug() {
        const titleField = document.getElementById('postTitle');
        const slugField = document.getElementById('postSlug');

        if (!titleField || !slugField) return;

        let slug = titleField.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Remove diacritics
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/-+/g, '-') // Replace multiple - with single -
            .replace(/^-+|-+$/g, ''); // Trim - from start/end

        slugField.value = slug;
    }

    // Preview image
    function previewImage(input) {
        const preview = document.getElementById('thumbnailPreview');
        if (!preview) return;

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `
                <img src="${e.target.result}" 
                     alt="Preview" 
                     class="image-upload__preview-img">
            `;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto-generate slug when title changes (only for new posts)
    document.addEventListener('DOMContentLoaded', function() {
        const titleField = document.getElementById('postTitle');
        const slugField = document.getElementById('postSlug');
        const isEdit = <?= $isEdit ? 'true' : 'false' ?>;

        if (titleField && slugField && !isEdit) {
            titleField.addEventListener('blur', function() {
                if (!slugField.value) {
                    generateSlug();
                }
            });
        }
    });
</script>