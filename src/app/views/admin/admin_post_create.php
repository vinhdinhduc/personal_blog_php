<?php
$page_title = "Tạo bài viết mới";
include 'header.php';

// Mock categories
$categories = [
    ['id' => 1, 'name' => 'Công nghệ'],
    ['id' => 2, 'name' => 'Lập trình'],
    ['id' => 3, 'name' => 'Thiết kế'],
    ['id' => 4, 'name' => 'Cuộc sống'],
];

// Mock tags
$available_tags = ['PHP', 'JavaScript', 'CSS', 'HTML', 'React', 'Vue', 'Node.js', 'Python', 'UI/UX', 'Design'];
?>

<style>
    .editor-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
    }

    .editor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e5e5e5;
    }

    .editor-header h1 {
        font-size: 2rem;
        color: #1a1a1a;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-secondary {
        background-color: white;
        color: #666;
        border: 1px solid #e5e5e5;
    }

    .btn-secondary:hover {
        background-color: #f9f9f9;
    }

    .btn-primary {
        background-color: #1a1a1a;
        color: white;
    }

    .btn-primary:hover {
        background-color: #333;
    }

    .form-card {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #1a1a1a;
        font-size: 0.95rem;
    }

    .form-required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    #content {
        min-height: 400px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.8;
    }

    .form-help {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.5rem;
    }

    .file-upload-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-upload-input {
        position: absolute;
        font-size: 100px;
        opacity: 0;
        right: 0;
        top: 0;
        cursor: pointer;
    }

    .file-upload-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        border: 2px dashed #e5e5e5;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        background-color: #f9f9f9;
    }

    .file-upload-label:hover {
        border-color: #6366f1;
        background-color: #f5f7ff;
    }

    .upload-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .preview-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        margin-top: 1rem;
        display: none;
    }

    .tags-input-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 0.75rem;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        min-height: 50px;
    }

    .tag-item {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #f0f0f0;
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .tag-remove {
        cursor: pointer;
        font-weight: bold;
        color: #666;
    }

    .tag-remove:hover {
        color: #dc2626;
    }

    .tag-suggestions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .tag-suggestion {
        padding: 0.35rem 0.75rem;
        background-color: white;
        border: 1px solid #e5e5e5;
        border-radius: 15px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .tag-suggestion:hover {
        background-color: #6366f1;
        color: white;
        border-color: #6366f1;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .editor-container {
            padding: 1rem;
        }

        .editor-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .header-actions {
            flex-direction: column;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="editor-container">
    <!-- Editor Header -->
    <div class="editor-header">
        <h1>✍️ Tạo bài viết mới</h1>
        <div class="header-actions">
            <a href="admin_post_list.php" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <!-- Editor Form -->
    <form method="POST" action="admin_post_create.php" enctype="multipart/form-data" class="form-card">
        <!-- Title -->
        <div class="form-group">
            <label class="form-label">
                Tiêu đề <span class="form-required">*</span>
            </label>
            <input
                type="text"
                name="title"
                class="form-control"
                placeholder="Nhập tiêu đề bài viết..."
                required>
        </div>

        <!-- Excerpt -->
        <div class="form-group">
            <label class="form-label">Mô tả ngắn</label>
            <textarea
                name="excerpt"
                class="form-control"
                placeholder="Mô tả ngắn gọn về nội dung bài viết (tối đa 200 ký tự)..."
                maxlength="200"></textarea>
            <div class="form-help">Mô tả này sẽ hiển thị trong danh sách bài viết</div>
        </div>

        <!-- Category and Status -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">
                    Danh mục <span class="form-required">*</span>
                </label>
                <select name="category_id" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="draft">Bản nháp</option>
                    <option value="published">Xuất bản</option>
                </select>
            </div>
        </div>

        <!-- Cover Image -->
        <div class="form-group">
            <label class="form-label">Ảnh bìa</label>
            <div class="file-upload-wrapper">
                <input
                    type="file"
                    name="cover_image"
                    class="file-upload-input"
                    accept="image/*"
                    onchange="previewImage(event)">
                <div class="file-upload-label">
                    <div style="text-align: center;">
                        <div class="upload-icon">📷</div>
                        <div>Nhấn để tải ảnh lên</div>
                        <div style="font-size: 0.85rem; color: #999; margin-top: 0.5rem;">
                            PNG, JPG, GIF (tối đa 5MB)
                        </div>
                    </div>
                </div>
            </div>
            <img id="preview" class="preview-image" alt="Preview">
        </div>

        <!-- Content -->
        <div class="form-group">
            <label class="form-label">
                Nội dung <span class="form-required">*</span>
            </label>
            <textarea
                name="content"
                id="content"
                class="form-control"
                placeholder="Viết nội dung bài viết của bạn..."
                required></textarea>
            <div class="form-help">Hỗ trợ HTML cơ bản: &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;em&gt;</div>
        </div>

        <!-- Tags -->
        <div class="form-group">
            <label class="form-label">Tags</label>
            <div class="tags-input-wrapper" id="tagsContainer" onclick="document.getElementById('tagInput').focus()">
                <input
                    type="text"
                    id="tagInput"
                    placeholder="Nhập tag và nhấn Enter..."
                    style="border: none; outline: none; flex: 1; min-width: 150px;"
                    onkeypress="handleTagInput(event)">
            </div>
            <input type="hidden" name="tags" id="tagsHidden">

            <div class="tag-suggestions">
                <small style="width: 100%; color: #666; margin-bottom: 0.5rem;">Gợi ý:</small>
                <?php foreach ($available_tags as $tag): ?>
                    <span class="tag-suggestion" onclick="addTag('<?php echo $tag; ?>')"><?php echo $tag; ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="form-group" style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" name="status" value="published" class="btn btn-primary">
                📢 Xuất bản
            </button>
            <button type="submit" name="status" value="draft" class="btn btn-secondary">
                💾 Lưu nháp
            </button>
        </div>
    </form>
</div>

<script>
    // Preview image
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    // Tags management
    let tags = [];

    function addTag(tagName) {
        tagName = tagName.trim();
        if (tagName && !tags.includes(tagName)) {
            tags.push(tagName);
            renderTags();
        }
        document.getElementById('tagInput').value = '';
    }

    function removeTag(tagName) {
        tags = tags.filter(t => t !== tagName);
        renderTags();
    }

    function renderTags() {
        const container = document.getElementById('tagsContainer');
        const input = document.getElementById('tagInput');

        container.innerHTML = '';

        tags.forEach(tag => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag-item';
            tagElement.innerHTML = `
                ${tag}
                <span class="tag-remove" onclick="removeTag('${tag}')">×</span>
            `;
            container.appendChild(tagElement);
        });

        container.appendChild(input);
        document.getElementById('tagsHidden').value = tags.join(',');
    }

    function handleTagInput(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            addTag(event.target.value);
        }
    }
</script>

<?php include 'footer.php'; ?>