<?php

/**
 * Admin Dashboard View
 * File: app/views/admin/dashboard.php
 * 
 * Trang dashboard với thống kê tổng quan
 */

// Dữ liệu mẫu - thay bằng dữ liệu thực từ database
$stats = [
    'total_posts' => 245,
    'total_users' => 1245,
    'total_comments' => 3456,
    'total_views' => 45678
];

// Recent posts mẫu
$recentPosts = [
    ['id' => 1, 'title' => 'Getting Started with PHP', 'author' => 'John Doe', 'status' => 'published', 'date' => '2024-01-15'],
    ['id' => 2, 'title' => 'MySQL Database Tutorial', 'author' => 'Jane Smith', 'status' => 'draft', 'date' => '2024-01-14'],
    ['id' => 3, 'title' => 'JavaScript ES6 Features', 'author' => 'Mike Johnson', 'status' => 'published', 'date' => '2024-01-13'],
];
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <!-- Total Posts -->
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_posts']); ?></h3>
            <p>Total Posts</p>
        </div>
    </div>

    <!-- Total Users -->
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_users']); ?></h3>
            <p>Total Users</p>
        </div>
    </div>

    <!-- Total Comments -->
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-comments"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_comments']); ?></h3>
            <p>Total Comments</p>
        </div>
    </div>

    <!-- Total Views -->
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-eye"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_views']); ?></h3>
            <p>Total Views</p>
        </div>
    </div>
</div>

<!-- Recent Posts -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Recent Posts</h2>
        <a href="/admin/posts" class="btn btn-primary btn-sm">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPosts as $post): ?>
                        <tr>
                            <td>#<?php echo str_pad($post['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['author']); ?></td>
                            <td>
                                <?php if ($post['status'] === 'published'): ?>
                                    <span class="badge badge-published">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-draft">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($post['date'])); ?></td>
                            <td>
                                <a href="/admin/posts/<?php echo $post['id']; ?>"
                                    class="btn btn-secondary btn-icon btn-sm"
                                    title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/admin/posts/<?php echo $post['id']; ?>/edit"
                                    class="btn btn-success btn-icon btn-sm"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="adminJS.deleteItem('/admin/posts/<?php echo $post['id']; ?>/delete')"
                                    class="btn btn-danger btn-icon btn-sm"
                                    title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/posts/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Create New Post
                    </a>
                    <a href="/admin/categories" class="btn btn-secondary">
                        <i class="fas fa-folder me-2"></i> Manage Categories
                    </a>
                    <a href="/admin/users" class="btn btn-info">
                        <i class="fas fa-users me-2"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">System Info</h2>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-server text-primary me-2"></i>
                        <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-database text-success me-2"></i>
                        <strong>Database:</strong> MySQL 8.0
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-hdd text-warning me-2"></i>
                        <strong>Disk Space:</strong> 45GB / 100GB
                    </li>
                    <li>
                        <i class="fas fa-clock text-info me-2"></i>
                        <strong>Last Backup:</strong> 2024-01-15 10:30 AM
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>