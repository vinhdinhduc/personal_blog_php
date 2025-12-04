<?php
require_once __DIR__ . '/../../helpers/ImageHelper.php';

// Lấy dữ liệu từ controller
$tag = $tag ?? null;
$posts = $posts ?? [];
$totalPosts = $totalPosts ?? 0;
$totalPages = $totalPages ?? 1;
$page = $page ?? 1;
$allCategories = $allCategories ?? [];
$allTags = $allTags ?? [];
$recentPosts = $recentPosts ?? [];

if (!$tag) {
    header('Location: ' . Router::url('/'));
    exit;
}
?>

<div class="tag-page">
    <div class="tag-page__container">
        <!-- Tag Header -->
        <section class="tag-header">
            <div class="tag-header__content">
                <div class="tag-header__icon">
                    <i class="fas fa-tag"></i>
                </div>
                <h1 class="tag-header__title">#<?php echo htmlspecialchars($tag['name']); ?></h1>
                <p class="tag-header__count">
                    <?php echo number_format($totalPosts); ?> bài viết
                </p>
                <div class="tag-header__breadcrumb">
                    <a href="<?php echo Router::url('/'); ?>">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                    <span><i class="fas fa-chevron-right"></i></span>
                    <span>Tag: <?php echo htmlspecialchars($tag['name']); ?></span>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="tag-content">
            <!-- Posts Section -->
            <div class="tag-posts">
                <div class="tag-posts__header">
                    <h2 class="tag-posts__title">Tất cả bài viết</h2>
                    <div class="tag-posts__sort">
                        <label class="tag-posts__sort-label" for="sortSelect">Sắp xếp:</label>
                        <select id="sortSelect" class="tag-posts__sort-select" onchange="handleSort(this.value)">
                            <option value="newest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>
                                Mới nhất
                            </option>
                            <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>
                                Cũ nhất
                            </option>
                            <option value="most-viewed" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'most-viewed') ? 'selected' : ''; ?>>
                                Xem nhiều nhất
                            </option>
                            <option value="title-asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'title-asc') ? 'selected' : ''; ?>>
                                Tiêu đề A-Z
                            </option>
                            <option value="title-desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'title-desc') ? 'selected' : ''; ?>>
                                Tiêu đề Z-A
                            </option>
                        </select>
                    </div>
                </div>

                <?php if (!empty($posts)): ?>
                    <!-- Posts Grid -->
                    <div class="tag-posts__grid">
                        <?php foreach ($posts as $post): ?>
                            <article class="post-card">
                                <div class="post-card__image">
                                    <img src="<?php echo ImageHelper::postCover($post['cover_image']); ?>"
                                        alt="<?php echo htmlspecialchars($post['title']); ?>"
                                        loading="lazy">
                                </div>
                                <div class="post-card__content">
                                    <?php if (!empty($post['category_name'])): ?>
                                        <a href="<?php echo Router::url('/category/' . $post['category_slug']); ?>"
                                            class="post-card__category">
                                            <?php echo htmlspecialchars($post['category_name']); ?>
                                        </a>
                                    <?php endif; ?>

                                    <h3 class="post-card__title">
                                        <a href="<?php echo Router::url('/post/' . $post['slug']); ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h3>

                                    <p class="post-card__excerpt">
                                        <?php echo htmlspecialchars(substr(strip_tags($post['excerpt'] ?? $post['content']), 0, 150)); ?>...
                                    </p>

                                    <div class="post-card__footer">
                                        <div class="post-card__author">
                                            <i class="fas fa-user-circle"></i>
                                            <span><?php echo htmlspecialchars($post['author_name']); ?></span>
                                        </div>
                                        <div class="post-card__stats">
                                            <span>
                                                <i class="far fa-eye"></i>
                                                <?php echo number_format($post['views']); ?>
                                            </span>
                                            <span>
                                                <i class="far fa-calendar"></i>
                                                <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="tag-pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>"
                                    class="tag-pagination__link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <span class="tag-pagination__link tag-pagination__link--disabled">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            <?php endif; ?>

                            <?php
                            // Hiển thị pagination thông minh
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);

                            if ($start > 1): ?>
                                <a href="?page=1<?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>"
                                    class="tag-pagination__link">1</a>
                                <?php if ($start > 2): ?>
                                    <span class="tag-pagination__link tag-pagination__link--disabled">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="tag-pagination__link tag-pagination__link--active">
                                        <?php echo $i; ?>
                                    </span>
                                <?php else: ?>
                                    <a href="?page=<?php echo $i; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>"
                                        class="tag-pagination__link">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($end < $totalPages): ?>
                                <?php if ($end < $totalPages - 1): ?>
                                    <span class="tag-pagination__link tag-pagination__link--disabled">...</span>
                                <?php endif; ?>
                                <a href="?page=<?php echo $totalPages; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>"
                                    class="tag-pagination__link">
                                    <?php echo $totalPages; ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>"
                                    class="tag-pagination__link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <span class="tag-pagination__link tag-pagination__link--disabled">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="tag-empty">
                        <div class="tag-empty__icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="tag-empty__title">Chưa có bài viết nào</h3>
                        <p class="tag-empty__description">
                            Tag này chưa có bài viết. Hãy quay lại sau hoặc khám phá các tag khác.
                        </p>
                        <a href="<?php echo Router::url('/'); ?>" class="tag-empty__button">
                            <i class="fas fa-home"></i>
                            Về trang chủ
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="tag-sidebar">
                <!-- Search Widget -->
                <div class="widget widget--search">
                    <h3 class="widget__title">Tìm kiếm</h3>
                    <form action="<?php echo Router::url('/search'); ?>" method="GET" class="search-form">
                        <input type="text"
                            name="q"
                            placeholder="Tìm kiếm bài viết..."
                            required
                            class="search-form__input">
                        <button type="submit" class="search-form__button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Related Tags Widget -->
                <?php if (!empty($allTags)): ?>
                    <div class="widget widget--related-tags">
                        <h3 class="widget__title">Tags liên quan</h3>
                        <div class="tag-cloud">
                            <?php
                            // Lấy ngẫu nhiên 10 tags khác
                            $relatedTags = array_filter($allTags, function ($t) use ($tag) {
                                return $t['id'] != $tag['id'];
                            });
                            $relatedTags = array_slice($relatedTags, 0, 10);
                            ?>
                            <?php foreach ($relatedTags as $relatedTag): ?>
                                <a href="<?php echo Router::url('/tag/' . $relatedTag['slug']); ?>"
                                    class="tag-cloud__item">
                                    #<?php echo htmlspecialchars($relatedTag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Categories Widget -->
                <?php if (!empty($allCategories)): ?>
                    <div class="widget widget--categories">
                        <h3 class="widget__title">Danh mục</h3>
                        <ul class="category-list">
                            <?php foreach (array_slice($allCategories, 0, 8) as $category): ?>
                                <li class="category-list__item">
                                    <a href="<?php echo Router::url('/category/' . $category['slug']); ?>"
                                        class="category-list__link">
                                        <span class="category-list__name">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </span>
                                        <span class="category-list__count">
                                            <?php echo $category['post_count'] ?? 0; ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Recent/Popular Posts Widget -->
                <?php if (!empty($recentPosts)): ?>
                    <div class="widget widget--popular">
                        <h3 class="widget__title">Bài viết gần đây</h3>
                        <ul class="popular-list">
                            <?php foreach ($recentPosts as $recent): ?>
                                <li class="popular-list__item">
                                    <div class="popular-list__image">
                                        <img src="<?php echo ImageHelper::postCover($recent['cover_image']); ?>"
                                            alt="<?php echo htmlspecialchars($recent['title']); ?>"
                                            loading="lazy">
                                    </div>
                                    <div class="popular-list__content">
                                        <h4 class="popular-list__title">
                                            <a href="<?php echo Router::url('/post/' . $recent['slug']); ?>">
                                                <?php echo htmlspecialchars($recent['title']); ?>
                                            </a>
                                        </h4>
                                        <div class="popular-list__meta">
                                            <span>
                                                <i class="far fa-calendar"></i>
                                                <?php echo date('d/m/Y', strtotime($recent['created_at'])); ?>
                                            </span>
                                            <span>
                                                <i class="far fa-eye"></i>
                                                <?php echo number_format($recent['views']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Handle sort change
    function handleSort(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', value);
        url.searchParams.set('page', '1'); // Reset về trang 1 khi sort
        window.location.href = url.toString();
    }

    // Add animation on scroll
    document.addEventListener('DOMContentLoaded', function() {
        // Animate post cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(20px)';

                    setTimeout(() => {
                        entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);

                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all post cards
        document.querySelectorAll('.post-card').forEach(card => {
            observer.observe(card);
        });

        // Smooth scroll for breadcrumb
        document.querySelectorAll('.tag-header__breadcrumb a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    });

    // Add loading state to search form
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const button = this.querySelector('.search-form__button');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
    }
</script>

<style>
    /* Search Form Styles */
    .search-form {
        display: flex;
        gap: 0.5rem;
    }

    .search-form__input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .search-form__input:focus {
        outline: none;
        border-color: var(--primary-color, #667eea);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-form__button {
        padding: 0.75rem 1.25rem;
        background: var(--primary-color, #667eea);
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-form__button:hover {
        background: var(--secondary-color, #764ba2);
        transform: translateY(-2px);
    }
</style>