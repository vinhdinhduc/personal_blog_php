<!-- Page Header -->
<div class="content__header">
    <h1 class="content__title">Dashboard</h1>
    <div class="content__breadcrumb">
        <a href="<?php echo Router::url('/admin'); ?>" class="content__breadcrumb-item">Admin</a>
        <span>/</span>
        <span class="content__breadcrumb-item">Dashboard</span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="card-grid">
    <!-- Total Posts -->
    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Tổng bài viết</h3>
                <div class="card__value"><?php echo $stats['total_posts'] ?? 0; ?></div>
            </div>
            <div class="card__icon card__icon--primary">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>

    </div>

    <!-- Total Users -->
    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Người dùng</h3>
                <div class="card__value"><?php echo $stats['total_users'] ?? 0; ?></div>
            </div>
            <div class="card__icon card__icon--success">
                <i class="fas fa-users"></i>
            </div>
        </div>

    </div>

    <!-- Total Comments -->
    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Bình luận</h3>
                <div class="card__value"><?php echo $stats['total_comments'] ?? 0; ?></div>
            </div>
            <div class="card__icon card__icon--info">
                <i class="fas fa-comments"></i>
            </div>
        </div>

    </div>

    <!-- Pending Approval -->
    <div class="card">
        <div class="card__header">
            <div>
                <h3 class="card__title">Chờ duyệt</h3>
                <div class="card__value"><?php echo $stats['pending_comments'] ?? 0; ?></div>
            </div>
            <div class="card__icon card__icon--warning">
                <i class="fas fa-clock"></i>
            </div>
        </div>

    </div>
</div>

<!-- Recent Posts -->
<div class="table-container">
    <div class="table-container__header">
        <h2 class="table-container__title">Bài viết gần đây</h2>
        <a href="<?php echo Router::url('/admin/posts'); ?>" class="btn btn--primary btn--sm">
            <i class="fas fa-eye"></i>
            Xem tất cả
        </a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Tác giả</th>
                <th>Danh mục</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>

            <?php if (!empty($recentPosts)): ?>
                <?php foreach ($recentPosts as $post): ?>
                    <tr>
                        <td><?php echo
                            $post['id']; ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['author_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($post['category_name'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                        <td>
                            <span class="badge badge--<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                <?php echo $post['status'] === 'published' ? 'Đã xuất bản' : 'Nháp'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo Router::url('/admin/posts/edit/' . $post['id']); ?>" class="btn btn--sm btn--primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p style="margin-top: 15px; color: var(--secondary-color);">Chưa có bài viết nào</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>