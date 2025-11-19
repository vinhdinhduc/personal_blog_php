<?php
$page_title = "Quản lý bài viết";
include 'header.php';

// Mock posts data for admin
$posts = [
    ['id' => 1, 'title' => 'Hướng dẫn học PHP từ cơ bản đến nâng cao', 'category' => 'Lập trình', 'author' => 'Nguyễn Văn A', 'status' => 'published', 'views' => 1523, 'created_at' => '2025-11-10 14:30:00'],
    ['id' => 2, 'title' => 'Top 10 công cụ thiết kế UI/UX năm 2025', 'category' => 'Thiết kế', 'author' => 'Trần Thị B', 'status' => 'published', 'views' => 987, 'created_at' => '2025-11-09 10:15:00'],
    ['id' => 3, 'title' => 'Machine Learning cho người mới bắt đầu', 'category' => 'Công nghệ', 'author' => 'Lê Văn C', 'status' => 'published', 'views' => 2145, 'created_at' => '2025-11-08 16:45:00'],
    ['id' => 4, 'title' => 'Cách tối ưu hiệu suất website với CDN', 'category' => 'Lập trình', 'author' => 'Phạm Thị D', 'status' => 'published', 'views' => 756, 'created_at' => '2025-11-07 09:20:00'],
    ['id' => 5, 'title' => 'Xu hướng thiết kế web 2025', 'category' => 'Thiết kế', 'author' => 'Hoàng Văn E', 'status' => 'draft', 'views' => 0, 'created_at' => '2025-11-06 13:00:00'],
    ['id' => 6, 'title' => 'Docker và Kubernetes: Hướng dẫn thực hành', 'category' => 'Công nghệ', 'author' => 'Nguyễn Văn A', 'status' => 'published', 'views' => 1234, 'created_at' => '2025-11-05 11:30:00'],
    ['id' => 7, 'title' => 'RESTful API Design Best Practices', 'category' => 'Lập trình', 'author' => 'Nguyễn Văn A', 'status' => 'published', 'views' => 892, 'created_at' => '2025-11-04 08:45:00'],
    ['id' => 8, 'title' => 'Git và GitHub cho người mới bắt đầu', 'category' => 'Lập trình', 'author' => 'Trần Thị B', 'status' => 'draft', 'views' => 0, 'created_at' => '2025-11-03 15:20:00'],
];

// Pagination
$posts_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_posts = count($posts);
$total_pages = ceil($total_posts / $posts_per_page);
$offset = ($current_page - 1) * $posts_per_page;
$current_posts = array_slice($posts, $offset, $posts_per_page);
?>

<style>
    .admin-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e5e5e5;
    }

    .admin-header h1 {
        font-size: 2rem;
        color: #1a1a1a;
    }

    .btn-create {
        padding: 0.75rem 1.5rem;
        background-color: #1a1a1a;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-create:hover {
        background-color: #333;
        transform: translateY(-2px);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        padding: 1.5rem;
    }

    .stat-box-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .stat-box-label {
        color: #666;
        font-size: 0.9rem;
    }

    .table-container {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background-color: #f9f9f9;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #1a1a1a;
        border-bottom: 2px solid #e5e5e5;
        font-size: 0.9rem;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
    }

    .table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .post-title-cell {
        max-width: 400px;
    }

    .post-title-link {
        color: #1a1a1a;
        text-decoration: none;
        font-weight: 500;
    }

    .post-title-link:hover {
        color: #6366f1;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }

    .status-published {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-draft {
        background-color: #fef3c7;
        color: #92400e;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        padding: 0.4rem 0.75rem;
        border: 1px solid #e5e5e5;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        color: #666;
        font-size: 0.9rem;
    }

    .btn-icon:hover {
        border-color: #6366f1;
        color: #6366f1;
    }

    .btn-delete {
        color: #dc2626;
    }

    .btn-delete:hover {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        text-decoration: none;
        color: #1a1a1a;
        transition: all 0.3s;
    }

    .pagination a:hover {
        background-color: #f9f9f9;
    }

    .pagination .active {
        background-color: #1a1a1a;
        color: white;
        border-color: #1a1a1a;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: 1rem;
        }

        .admin-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .btn-create {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            min-width: 800px;
        }
    }
</style>

<div class="admin-container">
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>📝 Quản lý bài viết</h1>
        <a href="admin_post_create.php" class="btn-create">
            ➕ Tạo bài viết mới
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-box-value"><?php echo count($posts); ?></div>
            <div class="stat-box-label">Tổng bài viết</div>
        </div>
        <div class="stat-box">
            <div class="stat-box-value">
                <?php echo count(array_filter($posts, fn($p) => $p['status'] === 'published')); ?>
            </div>
            <div class="stat-box-label">Đã xuất bản</div>
        </div>
        <div class="stat-box">
            <div class="stat-box-value">
                <?php echo count(array_filter($posts, fn($p) => $p['status'] === 'draft')); ?>
            </div>
            <div class="stat-box-label">Bản nháp</div>
        </div>
        <div class="stat-box">
            <div class="stat-box-value">
                <?php echo number_format(array_sum(array_column($posts, 'views'))); ?>
            </div>
            <div class="stat-box-label">Tổng lượt xem</div>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Tác giả</th>
                    <th>Trạng thái</th>
                    <th>Lượt xem</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($current_posts as $post): ?>
                    <tr>
                        <td>#<?php echo $post['id']; ?></td>
                        <td class="post-title-cell">
                            <a href="post_detail.php?id=<?php echo $post['id']; ?>" class="post-title-link">
                                <?php echo $post['title']; ?>
                            </a>
                        </td>
                        <td><?php echo $post['category']; ?></td>
                        <td><?php echo $post['author']; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $post['status']; ?>">
                                <?php echo $post['status'] === 'published' ? 'Đã xuất bản' : 'Bản nháp'; ?>
                            </span>
                        </td>
                        <td><?php echo number_format($post['views']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="post_detail.php?id=<?php echo $post['id']; ?>" class="btn-icon" title="Xem">👁️</a>
                                <a href="admin_post_edit.php?id=<?php echo $post['id']; ?>" class="btn-icon" title="Sửa">✏️</a>
                                <a href="#" class="btn-icon btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')">🗑️</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo $current_page - 1; ?>">← Trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $current_page): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo $current_page + 1; ?>">Sau →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>