<?php
require_once __DIR__ . '/../../helpers/ImageHelper.php';

$postModel = new PostModel();
$categoryModel = new CategoryModel();
$tagModel = new TagModel();

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;

// Lấy dữ liệu
$posts = $postModel->getPublishedPosts($page, $perPage);
$totalPosts = $postModel->countPublishedPosts();
$totalPages = ceil($totalPosts / $perPage);

// Sidebar data
$categories = $categoryModel->getAll();
$tags = $tagModel->getAll();
$recentPosts = $postModel->getRecentPosts(5);
?>

<div class="all-posts-page">
    <!-- Page Header -->
    <section class="all-posts-header">
        <div class="all-posts-header__overlay"></div>
        <div class="container">
            <div class="all-posts-header__content">
                <h1 class="all-posts-header__title">Tất cả bài viết</h1>
                <p class="all-posts-header__description">
                    Khám phá <?php echo number_format($totalPosts); ?> bài viết về công nghệ, lập trình và nhiều chủ đề khác
                </p>
                <div class="all-posts-header__breadcrumb">
                    <a href="<?php echo Router::url('/'); ?>">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                    <span>/</span>
                    <span>Tất cả bài viết</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="all-posts-main">
        <div class="container">
            <div class="all-posts-main__wrapper">
                <!-- Posts Section -->
                <div class="all-posts-content">
                    <!-- Filter Bar -->
                    <div class="filter-bar">
                        <div class="filter-bar__info">
                            <h2 class="filter-bar__title">
                                Hiển thị <?php echo count($posts); ?> / <?php echo number_format($totalPosts); ?> bài viết
                            </h2>
                        </div>
                        <div class="filter-bar__actions">
                            <select class="filter-bar__select" id="sortBy" onchange="handleSort(this.value)">
                                <option value="newest">Mới nhất</option>
                                <option value="oldest">Cũ nhất</option>
                                <option value="most-viewed">Xem nhiều nhất</option>
                                <option value="title-asc">Tên A-Z</option>
                                <option value="title-desc">Tên Z-A</option>
                            </select>
                        </div>
                    </div>

                    <?php if (empty($posts)): ?>
                        <div class="all-posts-content__empty">
                            <i class="fas fa-inbox"></i>
                            <h3>Chưa có bài viết nào</h3>
                            <p>Hãy quay lại sau để khám phá nội dung mới!</p>
                            <a href="<?php echo Router::url('/'); ?>" class="btn btn--primary">
                                <i class="fas fa-home"></i> Về trang chủ
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Posts Grid -->
                        <div class="all-posts-grid">
                            <?php foreach ($posts as $post): ?>
                                <article class="post-item">
                                    <a href="<?php echo Router::url('/post/' . $post['slug']); ?>" class="post-item__link">
                                        <div class="post-item__image">
                                            <img src="<?php echo ImageHelper::postCover($post['cover_image']); ?>"
                                                alt="<?php echo htmlspecialchars($post['title']); ?>"
                                                loading="lazy">
                                            <div class="post-item__overlay">
                                                <span class="post-item__read-more">
                                                    <i class="fas fa-arrow-right"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </a>

                                    <div class="post-item__content">
                                        <?php if (!empty($post['category_name'])): ?>
                                            <a href="<?php echo Router::url('/category/' . $post['category_slug']); ?>"
                                                class="post-item__category">
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </a>
                                        <?php endif; ?>

                                        <h3 class="post-item__title">
                                            <a href="<?php echo Router::url('/post/' . $post['slug']); ?>">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h3>

                                        <p class="post-item__excerpt">
                                            <?php echo htmlspecialchars(substr(strip_tags($post['excerpt'] ?? $post['content']), 0, 120)); ?>...
                                        </p>

                                        <div class="post-item__footer">
                                            <div class="post-item__author">
                                                <i class="fas fa-user-circle"></i>
                                                <span><?php echo htmlspecialchars($post['author_name']); ?></span>
                                            </div>
                                            <div class="post-item__meta">
                                                <span class="post-item__date">
                                                    <i class="far fa-calendar"></i>
                                                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                                </span>
                                                <span class="post-item__views">
                                                    <i class="far fa-eye"></i>
                                                    <?php echo number_format($post['views']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav class="pagination" aria-label="Phân trang">
                                <ul class="pagination__list">
                                    <?php if ($page > 1): ?>
                                        <li class="pagination__item">
                                            <a href="?page=<?php echo $page - 1; ?>"
                                                class="pagination__link pagination__link--prev">
                                                <i class="fas fa-chevron-left"></i>
                                                <span>Trước</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php
                                    $start = max(1, $page - 2);
                                    $end = min($totalPages, $page + 2);

                                    if ($start > 1): ?>
                                        <li class="pagination__item">
                                            <a href="?page=1" class="pagination__link">1</a>
                                        </li>
                                        <?php if ($start > 2): ?>
                                            <li class="pagination__item pagination__ellipsis">
                                                <span>...</span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $start; $i <= $end; $i++): ?>
                                        <li class="pagination__item">
                                            <?php if ($i == $page): ?>
                                                <span class="pagination__link pagination__link--active"><?php echo $i; ?></span>
                                            <?php else: ?>
                                                <a href="?page=<?php echo $i; ?>" class="pagination__link"><?php echo $i; ?></a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($end < $totalPages): ?>
                                        <?php if ($end < $totalPages - 1): ?>
                                            <li class="pagination__item pagination__ellipsis">
                                                <span>...</span>
                                            </li>
                                        <?php endif; ?>
                                        <li class="pagination__item">
                                            <a href="?page=<?php echo $totalPages; ?>" class="pagination__link"><?php echo $totalPages; ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="pagination__item">
                                            <a href="?page=<?php echo $page + 1; ?>"
                                                class="pagination__link pagination__link--next">
                                                <span>Sau</span>
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="all-posts-sidebar">
                    <!-- Search Widget -->
                    <div class="widget">
                        <h3 class="widget__title">Tìm kiếm</h3>
                        <form action="<?php echo Router::url('/search'); ?>" method="GET" class="search-widget">
                            <div class="search-widget__input-group">
                                <input type="text"
                                    name="q"
                                    class="search-widget__input"
                                    placeholder="Tìm kiếm bài viết..."
                                    required>
                                <button type="submit" class="search-widget__button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Categories Widget -->
                    <div class="widget">
                        <h3 class="widget__title">Danh mục</h3>
                        <ul class="category-widget">
                            <?php foreach ($categories as $cat): ?>
                                <li class="category-widget__item">
                                    <a href="<?php echo Router::url('/category/' . $cat['slug']); ?>"
                                        class="category-widget__link">
                                        <div class="category-widget__icon"
                                            style="background-color: <?php echo $cat['color'] ?? '#667eea'; ?>">
                                            <i class="<?php echo $cat['icon'] ?? 'fas fa-folder'; ?>"></i>
                                        </div>
                                        <div class="category-widget__info">
                                            <span class="category-widget__name"><?php echo htmlspecialchars($cat['name']); ?></span>
                                            <span class="category-widget__count"><?php echo $cat['post_count']; ?> bài viết</span>
                                        </div>
                                        <i class="fas fa-chevron-right category-widget__arrow"></i>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Recent Posts Widget -->
                    <div class="widget">
                        <h3 class="widget__title">Bài viết gần đây</h3>
                        <ul class="recent-widget">
                            <?php foreach ($recentPosts as $recent): ?>
                                <li class="recent-widget__item">
                                    <a href="<?php echo Router::url('/post/' . $recent['slug']); ?>"
                                        class="recent-widget__link">
                                        <h4 class="recent-widget__title">
                                            <?php echo htmlspecialchars($recent['title']); ?>
                                        </h4>
                                        <span class="recent-widget__date">
                                            <i class="far fa-clock"></i>
                                            <?php echo date('d/m/Y', strtotime($recent['created_at'])); ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Tags Widget -->
                    <div class="widget">
                        <h3 class="widget__title">Tags phổ biến</h3>
                        <div class="tag-widget">
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?php echo Router::url('/tag/' . $tag['slug']); ?>"
                                    class="tag-widget__item">
                                    #<?php echo htmlspecialchars($tag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>
</div>

<script>
    function handleSort(value) {
        // Implement sort logic or redirect with sort parameter
        const url = new URL(window.location.href);
        url.searchParams.set('sort', value);
        url.searchParams.set('page', '1'); // Reset to page 1
        window.location.href = url.toString();
    }
</script>