<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Bài viết</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo Router::url('admin/posts/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo bài viết mới
        </a>
    </div>
</div>

<!-- Filter & Search -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo Router::url('admin/posts'); ?>" class="row g-3">
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="published" <?php echo ($currentStatus ?? '') === 'published' ? 'selected' : ''; ?>>
                        Đã xuất bản
                    </option>
                    <option value="draft" <?php echo ($currentStatus ?? '') === 'draft' ? 'selected' : ''; ?>>
                        Bản nháp
                    </option>
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm bài viết..."
                        value="<?php echo htmlspecialchars($searchKeyword ?? ''); ?>">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> Tìm
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <?php if (!empty($searchKeyword) || !empty($currentStatus)): ?>
                    <a href="<?php echo Router::url('admin/posts'); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions -->
<form method="POST" action="<?php echo Router::url('admin/posts/bulk-action'); ?>" id="bulkActionForm">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <select name="action" class="form-select" style="max-width: 200px;">
                            <option value="">Chọn thao tác</option>
                            <option value="publish">Xuất bản</option>
                            <option value="draft">Chuyển thành nháp</option>
                            <option value="delete">Xóa</option>
                        </select>
                        <button type="submit" class="btn btn-secondary" onclick="return confirmBulkAction()">
                            Áp dụng
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        Tìm thấy <?php echo number_format($pagination['total'] ?? 0); ?> bài viết
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" onclick="toggleSelectAll(this)">
                        </th>
                        <th width="80">Ảnh</th>
                        <th>Tiêu đề</th>
                        <th width="150">Danh mục</th>
                        <th width="120">Tác giả</th>
                        <th width="100">Trạng thái</th>
                        <th width="100">Lượt xem</th>
                        <th width="120">Ngày tạo</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Không có bài viết nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="post_ids[]" value="<?php echo $post['id']; ?>"
                                        class="form-check-input item-checkbox">
                                </td>
                                <td>
                                    <?php if ($post['cover_image']): ?>
                                        <img src="<?php echo htmlspecialchars($post['cover_image']); ?>"
                                            alt="Cover" class="img-thumbnail" style="max-height: 50px;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                                            style="width: 60px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo Router::url('admin/posts/edit/' . $post['id']); ?>"
                                        class="fw-semibold text-decoration-none">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <a href="<?php echo Router::url('post/' . $post['slug']); ?>"
                                            target="_blank" class="text-muted">
                                            <i class="fas fa-external-link-alt"></i> Xem
                                        </a>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($post['category_name']): ?>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($post['category_name']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo htmlspecialchars($post['author_name'] ?? 'N/A'); ?></small>
                                </td>
                                <td>
                                    <?php if ($post['status'] === 'published'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Đã xuất bản
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock"></i> Nháp
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-eye"></i> <?php echo number_format($post['views']); ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo Router::url('admin/posts/edit/' . $post['id']); ?>"
                                            class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="deletePost(<?php echo $post['id']; ?>)" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<!-- Pagination -->
<?php if (($pagination['total_pages'] ?? 0) > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($pagination['has_prev']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo !empty($currentStatus) ? '&status=' . $currentStatus : ''; ?><?php echo !empty($searchKeyword) ? '&search=' . urlencode($searchKeyword) : ''; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i == $pagination['current_page']): ?>
                    <li class="page-item active">
                        <span class="page-link"><?php echo $i; ?></span>
                    </li>
                <?php elseif ($i == 1 || $i == $pagination['total_pages'] || abs($i - $pagination['current_page']) <= 2): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($currentStatus) ? '&status=' . $currentStatus : ''; ?><?php echo !empty($searchKeyword) ? '&search=' . urlencode($searchKeyword) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php elseif (abs($i - $pagination['current_page']) == 3): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagination['has_next']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo !empty($currentStatus) ? '&status=' . $currentStatus : ''; ?><?php echo !empty($searchKeyword) ? '&search=' . urlencode($searchKeyword) : ''; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
    }

    function confirmBulkAction() {
        const selected = document.querySelectorAll('.item-checkbox:checked').length;
        if (selected === 0) {
            alert('Vui lòng chọn ít nhất một bài viết');
            return false;
        }
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Vui lòng chọn thao tác');
            return false;
        }
        if (action === 'delete') {
            return confirm(`Bạn có chắc muốn xóa ${selected} bài viết đã chọn?`);
        }
        return true;
    }

    function deletePost(postId) {
        if (confirm('Bạn có chắc muốn xóa bài viết này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo Router::url('admin/posts/delete/'); ?>' + postId;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = '<?php echo $csrfToken; ?>';
            form.appendChild(csrf);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>