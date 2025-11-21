<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Thêm bài viết mới</h1>
    <div class="content__breadcrumb">
        <a href="/admin/dashboard" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <a href="/admin/posts" class="content__breadcrumb-item">Bài viết</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Thêm mới</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 20px;">
    <!-- Main Form -->
    <div class="table-container">
        <form action="/admin/posts/store" method="POST" enctype="multipart/form-data" data-validate>

            <!-- Title -->
            <div class="form-group">
                <label class="form-label">
                    Tiêu đề bài viết <span style="color: var(--danger-color);">*</span>
                </label>
                <input type="text"
                    name="title"
                    class="form-control"
                    placeholder="Nhập tiêu đề bài viết..."
                    required
                    value="<?= isset($post['title']) ? htmlspecialchars($post['title']) : '' ?>">
            </div>

            <!-- Slug -->
            <div class="form-group">
                <label class="form-label">URL thân thiện (Slug)</label>
                <input type="text"
                    name="slug"
                    class="form-control"
                    placeholder="url-than-thien-tu-dong-tao"
                    value="<?= isset($post['slug']) ? htmlspecialchars($post['slug']) : '' ?>">
                <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                    <i class="fas fa-info-circle"></i>
                    Để trống để tự động tạo từ tiêu đề
                </small>
            </div>

            <!-- Excerpt -->
            <div class="form-group">
                <label class="form-label">Mô tả ngắn (Excerpt)</label>
                <textarea name="excerpt"
                    class="form-control"
                    rows="3"
                    placeholder="Nhập mô tả ngắn cho bài viết..."><?= isset($post['excerpt']) ? htmlspecialchars($post['excerpt']) : '' ?></textarea>
            </div>

            <!-- Content -->
            <div class="form-group">
                <label class="form-label">
                    Nội dung bài viết <span style="color: var(--danger-color);">*</span>
                </label>
                <textarea name="content"
                    id="editor"
                    class="form-control"
                    rows="15"
                    required
                    placeholder="Nhập nội dung bài viết..."><?= isset($post['content']) ? htmlspecialchars($post['content']) : '' ?></textarea>
                <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                    <i class="fas fa-lightbulb"></i>
                    Hỗ trợ HTML và Markdown
                </small>
            </div>

            <!-- SEO Meta -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-search"></i>
                    Meta Description (SEO)
                </label>
                <textarea name="meta_description"
                    class="form-control"
                    rows="2"
                    maxlength="160"
                    placeholder="Mô tả SEO (tối đa 160 ký tự)..."><?= isset($post['meta_description']) ? htmlspecialchars($post['meta_description']) : '' ?></textarea>
            </div>

            <!-- Meta Keywords -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-tags"></i>
                    Meta Keywords (SEO)
                </label>
                <input type="text"
                    name="meta_keywords"
                    class="form-control"
                    placeholder="keyword1, keyword2, keyword3..."
                    value="<?= isset($post['meta_keywords']) ? htmlspecialchars($post['meta_keywords']) : '' ?>">
            </div>

            <!-- Action Buttons -->
            <div class="form-group" style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #eee;">
                <a href="/admin/posts" class="btn btn--secondary">
                    <i class="fas fa-times"></i>
                    Hủy bỏ
                </a>
                <button type="submit" name="status" value="draft" class="btn btn--warning">
                    <i class="fas fa-save"></i>
                    Lưu nháp
                </button>
                <button type="submit" name="status" value="published" class="btn btn--success">
                    <i class="fas fa-check"></i>
                    Xuất bản
                </button>
            </div>
        </form>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Featured Image -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px; font-size: 16px;">
                <i class="fas fa-image"></i>
                Ảnh đại diện
            </h3>

            <div class="form-group">
                <div id="thumbnailPreview" style="margin-bottom: 15px; text-align: center;">
                    <?php if (isset($post['thumbnail']) && !empty($post['thumbnail'])): ?>
                        <img src="<?= htmlspecialchars($post['thumbnail']) ?>"
                            style="max-width: 100%; border-radius: 8px; border: 2px solid #eee;">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: var(--light-color); border: 2px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: var(--secondary-color);">
                            <i class="fas fa-image" style="font-size: 48px; margin-bottom: 10px;"></i>
                            <p>Chưa có ảnh đại diện</p>
                        </div>
                    <?php endif; ?>
                </div>

                <input type="file"
                    name="thumbnail"
                    class="form-control"
                    accept="image/*">

                <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                    Kích thước khuyến nghị: 1200x630px
                </small>
            </div>
        </div>

        <!-- Category -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px; font-size: 16px;">
                <i class="fas fa-folder"></i>
                Danh mục
            </h3>

            <div class="form-group">
                <select name="category_id" class="form-control" required>
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

        <!-- Tags -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px; font-size: 16px;">
                <i class="fas fa-tags"></i>
                Thẻ (Tags)
            </h3>

            <div class="form-group">
                <input type="text"
                    name="tags"
                    class="form-control"
                    placeholder="Nhập thẻ, cách nhau bởi dấu phẩy..."
                    value="<?= isset($post['tags']) ? htmlspecialchars($post['tags']) : '' ?>">
                <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                    Ví dụ: công nghệ, tin tức, giải trí
                </small>
            </div>
        </div>

        <!-- Options -->
        <div class="table-container">
            <h3 style="margin-bottom: 15px; font-size: 16px;">
                <i class="fas fa-cog"></i>
                Tùy chọn
            </h3>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="featured" value="1"
                        <?= (isset($post['featured']) && $post['featured']) ? 'checked' : '' ?>>
                    <span>Bài viết nổi bật</span>
                </label>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="allow_comments" value="1" checked>
                    <span>Cho phép bình luận</span>
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Lịch xuất bản</label>
                <input type="datetime-local"
                    name="published_at"
                    class="form-control"
                    value="<?= date('Y-m-d\TH:i') ?>">
            </div>
        </div>
    </div>
</div>