<?php
// Mock data - Categories
$categories = [
    1 => ['id' => 1, 'name' => 'Công nghệ', 'slug' => 'cong-nghe', 'description' => 'Tin tức và xu hướng công nghệ mới nhất'],
    2 => ['id' => 2, 'name' => 'Lập trình', 'slug' => 'lap-trinh', 'description' => 'Hướng dẫn và kinh nghiệm lập trình'],
    3 => ['id' => 3, 'name' => 'Thiết kế', 'slug' => 'thiet-ke', 'description' => 'UI/UX và thiết kế sáng tạo'],
    4 => ['id' => 4, 'name' => 'Cuộc sống', 'slug' => 'cuoc-song', 'description' => 'Chia sẻ về cuộc sống và trải nghiệm']
];

// Get category from URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 2;
$current_category = $categories[$category_id] ?? $categories[2];

// Mock posts by category
$all_posts = [
    ['id' => 1, 'category_id' => 2, 'title' => 'Hướng dẫn học PHP từ cơ bản đến nâng cao', 'excerpt' => 'PHP là một trong những ngôn ngữ lập trình web phổ biến nhất hiện nay...', 'cover_image' => 'https://images.unsplash.com/photo-1599507593499-a3f7d7d97667?w=800', 'author' => 'Nguyễn Văn A', 'views' => 1523, 'created_at' => '2025-11-10'],
    ['id' => 4, 'category_id' => 2, 'title' => 'Cách tối ưu hiệu suất website với CDN', 'excerpt' => 'CDN là giải pháp tuyệt vời để tăng tốc độ tải trang web...', 'cover_image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800', 'author' => 'Phạm Thị D', 'views' => 756, 'created_at' => '2025-11-07'],
    ['id' => 7, 'category_id' => 2, 'title' => 'RESTful API Design Best Practices', 'excerpt' => 'Thiết kế API là một kỹ năng quan trọng cho mọi backend developer...', 'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800', 'author' => 'Nguyễn Văn A', 'views' => 892, 'created_at' => '2025-11-04'],
    ['id' => 8, 'category_id' => 2, 'title' => 'Git và GitHub cho người mới bắt đầu', 'excerpt' => 'Version control là công cụ không thể thiếu trong lập trình hiện đại...', 'cover_image' => 'https://images.unsplash.com/photo-1556075798-4825dfaaf498?w=800', 'author' => 'Trần Thị B', 'views' => 1654, 'created_at' => '2025-11-03'],
    ['id' => 9, 'category_id' => 2, 'title' => 'SQL vs NoSQL: Nên chọn gì?', 'excerpt' => 'So sánh chi tiết giữa SQL và NoSQL để chọn database phù hợp...', 'cover_image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800', 'author' => 'Lê Văn C', 'views' => 1123, 'created_at' => '2025-11-02'],
];

// Filter posts by category
$posts = array_filter($all_posts, function ($post) use ($category_id) {
    return $post['category_id'] == $category_id;
});

// Pagination
$posts_per_page = 6;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_posts = count($posts);
$total_pages = ceil($total_posts / $posts_per_page);
$offset = ($current_page - 1) * $posts_per_page;
$current_posts = array_slice($posts, $offset, $posts_per_page);

$page_title = $current_category['name'];
include 'header.php';
?>

<style>
    .category-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
        margin-bottom: 3rem;
    }

    .category-header h1 {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        font-weight: 700;
    }

    .category-header p {
        font-size: 1.1rem;
        opacity: 0.95;
    }

    .category-nav {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }

    .category-nav a {
        padding: 0.6rem 1.5rem;
        border: 1px solid #e5e5e5;
        border-radius: 25px;
        text-decoration: none;
        color: #666;
        transition: all 0.3s;
        font-weight: 500;
    }

    .category-nav a:hover {
        border-color: #6366f1;
        color: #6366f1;
    }

    .category-nav a.active {
        background-color: #1a1a1a;
        color: white;
        border-color: #1a1a1a;
    }

    .posts-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .post-card {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
        cursor: pointer;
    }

    .post-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .post-thumbnail {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .post-content {
        padding: 1.5rem;
    }

    .post-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .post-excerpt {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .post-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        color: #999;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }

    .post-author {
        font-weight: 500;
        color: #666;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin: 3rem 0;
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

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #666;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    @media (max-width: 992px) {
        .posts-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .category-header h1 {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .posts-grid {
            grid-template-columns: 1fr;
        }

        .category-header {
            padding: 2rem 1rem;
        }

        .category-header h1 {
            font-size: 1.75rem;
        }

        .category-nav {
            padding: 0 1rem;
        }
    }
</style>

<!-- Category Header -->
<div class="category-header">
    <h1><?php echo $current_category['name']; ?></h1>
    <p><?php echo $current_category['description']; ?></p>
</div>

<div class="container">
    <!-- Category Navigation -->
    <div class="category-nav">
        <?php foreach ($categories as $cat): ?>
            <a href="category.php?id=<?php echo $cat['id']; ?>"
                class="<?php echo $cat['id'] == $category_id ? 'active' : ''; ?>">
                <?php echo $cat['name']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (count($current_posts) > 0): ?>
        <!-- Posts Grid -->
        <div class="posts-grid">
            <?php foreach ($current_posts as $post): ?>
                <article class="post-card" onclick="window.location.href='post_detail.php?id=<?php echo $post['id']; ?>'">
                    <img src="<?php echo $post['cover_image']; ?>" alt="<?php echo $post['title']; ?>" class="post-thumbnail">

                    <div class="post-content">
                        <h2 class="post-title"><?php echo $post['title']; ?></h2>

                        <p class="post-excerpt"><?php echo $post['excerpt']; ?></p>

                        <div class="post-meta">
                            <span class="post-author"><?php echo $post['author']; ?></span>
                            <span>👁️ <?php echo number_format($post['views']); ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?id=<?php echo $category_id; ?>&page=<?php echo $current_page - 1; ?>">← Trước</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?id=<?php echo $category_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?id=<?php echo $category_id; ?>&page=<?php echo $current_page + 1; ?>">Sau →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <h3>Chưa có bài viết nào</h3>
            <p>Danh mục này hiện chưa có bài viết. Hãy quay lại sau nhé!</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>