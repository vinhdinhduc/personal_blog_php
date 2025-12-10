<?php
require_once __DIR__ . '/../helpers/ImageHelper.php';

//  Đảm bảo các biến từ Controller tồn tại
$posts = $posts ?? [];
$categories = $categories ?? [];
$tags = $tags ?? [];
$recentPosts = $recentPosts ?? [];
$totalPages = $totalPages ?? 1;
$currentPage = $currentPage ?? 1;

// Lấy bài viết featured (bài mới nhất)
$featuredPost = !empty($posts) ? $posts[0] : null;
?>







<!-- Hero Section -->
<div class="home-page-container">
    <?php if ($featuredPost): ?>
        <section class="hero">
            <div class="hero-bg" style="background-image: url('<?php echo $featuredPost['cover_image'] ?? 'assets/images/default-hero.jpg'; ?>')"></div>
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="hero-content">
                    <span class="hero-badge">Bài viết nổi bật</span>
                    <h1 class="hero-title"><?php echo htmlspecialchars($featuredPost['title']); ?></h1>
                    <p class="hero-excerpt"><?php echo htmlspecialchars(substr($featuredPost['excerpt'] ?? '', 0, 150)); ?>...</p>
                    <div class="hero-meta">
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($featuredPost['author_name']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($featuredPost['created_at'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo number_format($featuredPost['views']); ?> lượt xem</span>
                    </div>
                    <a href="<?php echo Router::url('/post/' . $featuredPost['slug']); ?>" class="btn-hero">Đọc ngay</a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-wrapper">
                <!-- Posts Grid -->
                <div class="posts-section">
                    <div class="section-header">
                        <h2>Bài viết mới nhất</h2>
                        <a href="<?php echo Router::url('/posts'); ?>" class="view-all">
                            Xem tất cả <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="posts-grid">
                        <?php foreach ($posts as $post): ?>
                            <article class="post-card">
                                <div class="post-image">
                                    <img src="<?= ImageHelper::postCover($post['cover_image']) ?>"
                                        alt="<?= htmlspecialchars($post['title']) ?>">
                                </div>
                                <div class="post-content">
                                    <h3 class="post-title">
                                        <a href="<?= Router::url('/post/' . $post['slug']) ?>">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h3>

                                    <p class="post-excerpt">
                                        <?php echo htmlspecialchars(substr(strip_tags($post['excerpt'] ?? $post['content']), 0, 120)); ?>...
                                    </p>

                                    <div class="post-meta">
                                        <div class="post-author">
                                            <i class="fas fa-user-circle"></i>
                                            <span><?php echo htmlspecialchars($post['author_name']); ?></span>
                                        </div>
                                        <div class="post-stats">
                                            <span><i class="fas fa-eye"></i> <?php echo number_format($post['views']); ?></span>
                                            <span><i class="far fa-calendar"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?php echo $currentPage - 1; ?>" class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="page-link active"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($currentPage  < $totalPages): ?>
                                <a href="?page=<?php echo $currentPage  + 1; ?>" class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Search -->
                    <div class="widget search-widget">
                        <form action="<?php echo Router::url('/search'); ?>" method="GET" class="search-form">
                            <input type="text" name="q" placeholder="Tìm kiếm bài viết..." required>
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="widget">
                        <h3 class="widget-title">Danh mục</h3>
                        <ul class="category-list">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="category.php?slug=<?php echo $category['slug']; ?>">
                                        <span class="category-name"><?php echo htmlspecialchars($category['name']); ?></span>
                                        <span class="category-count"><?php echo $category['post_count']; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Recent Posts -->
                    <div class="widget">
                        <h3 class="widget-title">Bài viết gần đây</h3>
                        <ul class="recent-posts">
                            <?php foreach ($recentPosts as $recent): ?>
                                <li>
                                    <a href="post.php?slug=<?php echo $recent['slug']; ?>">
                                        <h4><?php echo htmlspecialchars($recent['title']); ?></h4>
                                        <span class="recent-date">
                                            <i class="far fa-clock"></i>
                                            <?php echo date('d/m/Y', strtotime($recent['created_at'])); ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Tags -->
                    <div class="widget">
                        <h3 class="widget-title">Tags phổ biến</h3>
                        <div class="tag-cloud">
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?php echo Router::url('/tag/' . $tag['slug']); ?>" class="tag">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</div>