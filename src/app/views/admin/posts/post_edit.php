<?php ob_start(); ?>

<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Chỉnh sửa bài viết</h1>
    <div class="content__breadcrumb">
        <a href="/admin/dashboard" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <a href="/admin/posts" class="content__breadcrumb-item">Bài viết</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Chỉnh sửa #<?= $post['id'] ?? '' ?></span>
    </div>
</div>

<?php if (isset($message)): ?>
    <div class="alert alert--<?= $message['type'] ?>" style="padding: 15px; border-radius: 8px; margin-bottom: 20px; background: <?= $message['type'] == 'success' ? 'rgba(28,200,138,0.1)' : 'rgba(231,74,59,0.1)' ?>; border-left: 4px solid <?= $message['type'] == 'success' ? 'var(--success-color)' : 'var(--danger-color)' ?>;">
        <i class="fas fa-<?= $message['type'] == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= $message['text'] ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 20px;">
    <!-- Main Form -->
    <div class="table-container">
        <form action="/admin/posts/update/<?= $post['id'] ?>" method="POST" enctype="multipart/form-data" data-validate>
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">

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
                    value="<?= htmlspecialchars($post['title'] ?? '') ?>">
            </div>

            <!-- Slug -->
            <div class="form-group">
                <label class="form-label">URL thân thiện (Slug)</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text"
                        name="slug"
                        class="form-control"
                        placeholder="url-than-thien"
                        value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
                        style="flex: 1;">
                    <button type="button" class="btn btn--info" onclick="generateSlug()">
                        <i class="fas fa-sync"></i>
                        Tạo lại
                    </button>
                </div>
                <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                    <i class="fas fa-link"></i>
                    <!--URL hiện tại: <strong>/posts/</strong>-->
                </small>
            </div>

            <!-- Excerpt -->
            <div class="form-group">
                <label class="form-label">Mô tả ngắn (Excerpt)</label>
                <textarea name="excerpt"
                    class="form-control"
                    rows="3"
                    placeholder="Nhập mô tả ngắn cho bài viết..."><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
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
                    placeholder="Nhập nội dung bài viết..."><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
                <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                    <i class="fas fa-lightbulb"></i>
                    Hỗ trợ HTML và Markdown
                </small>
            </div>

            <!-- SEO Section -->
            <div style="background: var(--light-color); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="margin-bottom: 15px; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-search"></i>
                    Tối ưu hóa SEO
                </h3>

                <div class="form-group">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description"
                        class="form-control"
                        rows="2"
                        maxlength="160"
                        placeholder="Mô tả SEO (tối đa 160 ký tự)..."><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
                    <small style="color: var(--secondary-color); display: block; margin-top: 5px;">
                        <span id="metaDescLength">0</span>/160 ký tự
                    </small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text"
                        name="meta_keywords"
                        class="form-control"
                        placeholder="keyword1, keyword2, keyword3..."
                        value="<?= htmlspecialchars($post['meta_keywords'] ?? '') ?>">
                </div>
            </div>

            <!-- Post Statistics -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px;">
                <div style="background: var(--light-color); padding: 15px; border-radius: 8px; text-align: center;">
                    <i class="fas fa-eye" style="font-size: 24px; color: var(--info-color); margin-bottom: 8px;"></i>
                    <div style="font-size: 24px; font-weight: bold; color: var(--dark-color);">
                        <?= number_format($post['views'] ?? 0) ?>
                    </div>
                    <div style="font-size: 12px; color: var(--secondary-color);">Lượt xem</div>
                </div>

                <div style="background: var(--light-color); padding: 15px; border-radius: 8px; text-align: center;">
                    <i class="fas fa-comments" style="font-size: 24px; color: var(--success-color); margin-bottom: 8px;"></i>
                    <div style="font-size: 24px; font-weight: bold; color: var(--dark-color);">
                        <?= $post['comment_count'] ?? 0 ?>
                    </div>
                    <div style="font-size: 12px; color: var(--secondary-color);">Bình luận</div>
                </div>

                <div style="background: var(--light-color); padding: 15px; border-radius: 8px; text-align: center;">
                    <i class="fas fa-heart" style="font-size: 24px; color: var(--danger-color); margin-bottom: 8px;"></i>
                    <div style="font-size: 24px; font-weight: bold; color: var(--dark-color);">
                        <?= $post['likes'] ?? 0 ?>
                    </div>
                    <div style="font-size: 12px; color: var(--secondary-color);">Lượt thích</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-group" style="display: flex; gap: 10px; justify-content: space-between; padding-top: 20px; border-top: 2px solid #eee;">
                <div style="display: flex; gap: 10px;">
                    <a href="/admin/posts" class="btn btn--secondary">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                    <a href="/posts/<?= $post['slug'] ?>" target="_blank" class="btn btn--info">
                        <i class="fas fa-external-link-alt"></i>
                        Xem trước
                    </a>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="status" value="draft" class="btn btn--warning">
                        <i class="fas fa-save"></i>
                        Lưu nháp
                    </button>
                    <button type="submit" name="status" value="published" class="btn btn--success">
                        <i class="fas fa-check"></i>
                        Cập nhật & Xuất bản
                    </button>
                </div>
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
                    <?php if (!empty($post['thumbnail'])): ?>
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
                                <?= ($post['category_id'] == $category['id']) ? 'selected' : '' ?>>
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
                    value="<?= htmlspecialchars($post['tags'] ?? '') ?>">
            </div>
        </div>

        <!-- Status & Options -->
        <div class="table-container">
            <h3 style="margin-bottom: 15px; font-size: 16px;">
                <i class="fas fa-cog"></i>
                Trạng thái & Tùy chọn
            </h3>

            <div class="form-group">
                <label class="form-label">Trạng thái hiện tại</label>
                <div style="padding: 10px; background: var(--light-color); border-radius: 5px; text-align: center;">
                    <?php if ($post['status'] == 'published'): ?>
                        <span class="badge badge--success" style="font-size: 14px; padding: 8px 16px;">
                            <i class="fas fa-check-circle"></i>
                            Đã xuất bản
                        </span>
                    <?php elseif ($post['status'] == 'draft'): ?>
                        <span class="badge badge--warning" style="font-size: 14px; padding: 8px 16px;">
                            <i class="fas fa-file-alt"></i>
                            Bản nháp
                        </span>
                    <?php else: ?>
                        <span class="badge badge--info" style="font-size: 14px; padding: 8px 16px;">
                            <i class="fas fa-clock"></i>
                            Chờ duyệt
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="featured" value="1"
                        <?= (!empty($post['featured'])) ? 'checked' : '' ?>>
                    <span>Bài viết nổi bật</span>
                </label>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="allow_comments" value="1"
                        <?= (!empty($post['allow_comments'])) ? 'checked' : '' ?>>
                    <span>Cho phép bình luận</span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">Ngày xuất bản</label>
                <input type="datetime-local"
                    name="published_at"
                    class="form-control"
                    value="<?= isset($post['published_at']) ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : date('Y-m-d\TH:i') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <small style="color: var(--secondary-color);">
                    <i class="fas fa-clock"></i>
                    Tạo lúc: <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?><br>
                    <i class="fas fa-edit"></i>
                    Cập nhật: <?= date('d/m/Y H:i', strtotime($post['updated_at'])) ?>
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    // Generate slug from title
    function generateSlug() {
        const title = document.querySelector('input[name="title"]').value;
        const slug = title.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        document.querySelector('input[name="slug"]').value = slug;
    }

    // Count meta description characters
    const metaDesc = document.querySelector('textarea[name="meta_description"]');
    if (metaDesc) {
        const counter = document.getElementById('metaDescLength');
        metaDesc.addEventListener('input', function() {
            counter.textContent = this.value.length;
            if (this.value.length > 160) {
                counter.style.color = 'var(--danger-color)';
            } else {
                counter.style.color = 'var(--success-color)';
            }
        });
        counter.textContent = metaDesc.value.length;
    }
</script>

<?php
$content = ob_get_clean();
$pageTitle = 'Chỉnh sửa bài viết';
require_once __DIR__ . '/../layouts/admin_layout.php';

?>