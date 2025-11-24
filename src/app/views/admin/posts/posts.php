<?php
echo "<prev>";
print_r($posts);
echo "</prev>";

?>

<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Quản lý bài viết</h1>
    <div class="content__breadcrumb">
        <a href="/admin/dashboard" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Bài viết</span>
    </div>
</div>

<!-- Action Buttons -->
<div style="margin-bottom: 20px;">
    <a href=<?php echo Router::url('/admin/posts/add') ?> class="btn btn--primary">
        <i class="fas fa-plus"></i>
        Thêm bài viết mới
    </a>
    <button class="btn btn--success">
        <i class="fas fa-file-export"></i>
        Xuất Excel
    </button>
</div>

<!-- Posts Table -->
<div class="table-container">
    <div class="table-container__header">
        <h2 class="table-container__title">Danh sách bài viết</h2>

        <!-- Filter & Search -->
        <div style="display: flex; gap: 10px;">
            <select class="form-control" style="width: 150px;">
                <option value="">Tất cả trạng thái</option>
                <option value="published">Đã xuất bản</option>
                <option value="draft">Nháp</option>
                <option value="pending">Chờ duyệt</option>
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
                <th style="width: 100px;">Hình ảnh</th>
                <th>Tiêu đề</th>
                <th style="width: 150px;">Tác giả</th>
                <th style="width: 120px;">Danh mục</th>
                <th style="width: 100px;">Lượt xem</th>
                <th style="width: 120px;">Ngày tạo</th>
                <th style="width: 120px;">Trạng thái</th>
                <th style="width: 150px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($posts) && count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="post-checkbox" value="<?= $post['id'] ?>">
                        </td>
                        <td><?= $post['id'] ?></td>
                        <td>
                            <?php if (!empty($post['cover_image'])): ?>
                                <img src="<?= Router::url() . "public/" . htmlspecialchars($post['cover_image']) ?>"
                                    alt="cover image"
                                    style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                <div style="width: 60px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: #ccc;"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($post['title']) ?></strong>
                            <br>
                            <small style="color: var(--secondary-color);">
                                <?= htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 80)) ?>...
                            </small>
                        </td>
                        <td><?= htmlspecialchars($post['author_name']) ?></td>
                        <td><?= htmlspecialchars($post['category_name']) ?></td>
                        <td>
                            <i class="fas fa-eye" style="color: var(--info-color);"></i>
                            <?= number_format($post['views'] ?? 0) ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></td>
                        <td>
                            <?php if ($post['status'] == 'published'): ?>
                                <span class="badge badge--success">
                                    <i class="fas fa-check-circle"></i>
                                    Đã xuất bản
                                </span>
                            <?php elseif ($post['status'] == 'draft'): ?>
                                <span class="badge badge--warning">
                                    <i class="fas fa-file-alt"></i>
                                    Nháp
                                </span>
                            <?php else: ?>
                                <span class="badge badge--info">
                                    <i class="fas fa-clock"></i>
                                    Chờ duyệt
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= Router::url("/posts/{$post['slug']}") ?>" target="_blank" class="btn btn--info btn--sm" data-tooltip="Xem bài viết">
                                <i class="fas fa-eye "></i>
                            </a>
                            <a href="<?= Router::url("/admin/posts/edit/{$post['id']}") ?>" class="btn btn--warning btn--sm" data-tooltip="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- ✅ SỬA: Dùng button với onclick -->
                            <button type="button"
                                class="btn btn--danger btn--sm"
                                data-tooltip="Xóa"
                                onclick="deletePost(<?= $post['id'] ?>, '<?= htmlspecialchars($post['title']) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 60px;">
                        <i class="fas fa-inbox" style="font-size: 64px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                        <p style="font-size: 18px; color: var(--secondary-color); margin-bottom: 20px;">Chưa có bài viết nào</p>
                        <a href="/admin/posts/add" class="btn btn--primary">
                            <i class="fas fa-plus"></i>
                            Tạo bài viết đầu tiên
                        </a>
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
                <?= min($currentPage * $perPage, $totalPosts) ?>
                trong tổng số <?= $totalPosts ?> bài viết
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

<!-- ✅ THÊM: Hidden form để delete -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
</form>

<script>
    // ✅ THÊM: Delete function
    function deletePost(postId, postTitle) {
        if (confirm(`Bạn có chắc chắn muốn xóa bài viết "${postTitle}"?\n\nHành động này không thể hoàn tác!`)) {
            const form = document.getElementById('deleteForm');
            form.action = '<?= Router::url("/admin/posts/delete/") ?>' + postId;
            form.submit();
        }
    }

    // Select all checkboxes
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.post-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>