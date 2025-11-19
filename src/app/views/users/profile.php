<?php
// Mock user data
$user = [
    'id' => 1,
    'first_name' => 'Nguyễn',
    'last_name' => 'Văn A',
    'email' => 'nguyenvana@example.com',
    'role' => 'admin',
    'created_at' => '2025-01-15 10:00:00',
    'total_posts' => 8,
    'total_views' => 12456
];

// Mock user posts
$user_posts = [
    ['id' => 1, 'title' => 'Hướng dẫn học PHP từ cơ bản đến nâng cao', 'status' => 'published', 'views' => 1523, 'created_at' => '2025-11-10', 'cover_image' => 'https://images.unsplash.com/photo-1599507593499-a3f7d7d97667?w=400'],
    ['id' => 7, 'title' => 'RESTful API Design Best Practices', 'status' => 'published', 'views' => 892, 'created_at' => '2025-11-04', 'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400'],
    ['id' => 6, 'title' => 'Docker và Kubernetes: Hướng dẫn thực hành', 'status' => 'published', 'views' => 1234, 'created_at' => '2025-11-05', 'cover_image' => 'https://images.unsplash.com/photo-1605745341112-85968b19335b?w=400'],
    ['id' => 10, 'title' => 'Microservices Architecture Explained', 'status' => 'draft', 'views' => 0, 'created_at' => '2025-11-12', 'cover_image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=400'],
];

$page_title = "Hồ sơ của " . $user['first_name'] . ' ' . $user['last_name'];
include 'header.php';
?>

<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 3rem;
        color: white;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 700;
        color: #667eea;
        flex-shrink: 0;
    }

    .profile-info h1 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .profile-email {
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .profile-badge {
        display: inline-block;
        background-color: rgba(255, 255, 255, 0.2);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #666;
        font-size: 0.95rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-header h2 {
        font-size: 1.5rem;
        color: #1a1a1a;
    }

    .btn-new-post {
        padding: 0.6rem 1.5rem;
        background-color: #1a1a1a;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-new-post:hover {
        background-color: #333;
    }

    .posts-list {
        display: grid;
        gap: 1.5rem;
    }

    .post-item {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        gap: 1.5rem;
        transition: all 0.3s;
    }

    .post-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .post-item-thumbnail {
        width: 150px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .post-item-content {
        flex: 1;
    }

    .post-item-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.75rem;
    }

    .post-item-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .post-item-title a {
        color: inherit;
        text-decoration: none;
    }

    .post-item-title a:hover {
        color: #6366f1;
    }

    .post-status {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-published {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-draft {
        background-color: #fef3c7;
        color: #92400e;
    }

    .post-item-meta {
        display: flex;
        gap: 1.5rem;
        color: #666;
        font-size: 0.9rem;
    }

    .post-item-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .btn-action {
        padding: 0.4rem 1rem;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        text-decoration: none;
        color: #666;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .btn-action:hover {
        border-color: #6366f1;
        color: #6366f1;
    }

    .btn-delete {
        color: #dc2626;
    }

    .btn-delete:hover {
        border-color: #dc2626;
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
            padding: 2rem;
        }

        .profile-stats {
            grid-template-columns: 1fr;
        }

        .post-item {
            flex-direction: column;
        }

        .post-item-thumbnail {
            width: 100%;
            height: 200px;
        }

        .section-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .btn-new-post {
            text-align: center;
        }
    }
</style>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
        </div>
        <div class="profile-info">
            <h1><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h1>
            <p class="profile-email">📧 <?php echo $user['email']; ?></p>
            <span class="profile-badge">
                <?php echo $user['role'] === 'admin' ? '👑 Quản trị viên' : '👤 Thành viên'; ?>
            </span>
        </div>
    </div>

    <!-- Stats -->
    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-value"><?php echo $user['total_posts']; ?></div>
            <div class="stat-label">Bài viết</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo number_format($user['total_views']); ?></div>
            <div class="stat-label">Lượt xem</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></div>
            <div class="stat-label">Tham gia</div>
        </div>
    </div>

    <!-- Posts Section -->
    <div class="section-header">
        <h2>Bài viết của tôi</h2>
        <a href="admin_post_create.php" class="btn-new-post">+ Tạo bài viết mới</a>
    </div>

    <div class="posts-list">
        <?php foreach ($user_posts as $post): ?>
            <div class="post-item">
                <img src="<?php echo $post['cover_image']; ?>" alt="<?php echo $post['title']; ?>" class="post-item-thumbnail">

                <div class="post-item-content">
                    <div class="post-item-header">
                        <div>
                            <h3 class="post-item-title">
                                <a href="post_detail.php?id=<?php echo $post['id']; ?>">
                                    <?php echo $post['title']; ?>
                                </a>
                            </h3>
                            <div class="post-item-meta">
                                <span>📅 <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                                <span>👁️ <?php echo number_format($post['views']); ?> lượt xem</span>
                            </div>
                        </div>
                        <span class="post-status status-<?php echo $post['status']; ?>">
                            <?php echo $post['status'] === 'published' ? 'Đã xuất bản' : 'Bản nháp'; ?>
                        </span>
                    </div>

                    <div class="post-item-actions">
                        <a href="post_detail.php?id=<?php echo $post['id']; ?>" class="btn-action">👁️ Xem</a>
                        <a href="admin_post_edit.php?id=<?php echo $post['id']; ?>" class="btn-action">✏️ Chỉnh sửa</a>
                        <a href="#" class="btn-action btn-delete" onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')">🗑️ Xóa</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>