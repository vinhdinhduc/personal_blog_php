<?php
require_once __DIR__ . '/../../helpers/ImageHelper.php';


$posts = $posts ?? [];
$keyword = $keyword ?? '';
$totalPosts = $totalPosts ?? 0;
$totalPages = $totalPages ?? 0;
$page = $currentPage ?? 1;
$categories = $categories ?? [];
$tags = $tags ?? [];
$recentPosts = $recentPosts ?? [];
?>

<div class="search-page">
    <!-- Search Header -->
    <section class="search-header">
        <div class="search-header__overlay"></div>
        <div class="container">
            <div class="search-header__content">
                <div class="search-header__icon">
                    <i class="fas fa-search"></i>
                </div>
                <h1 class="search-header__title">
                    <?php if (!empty($keyword)): ?>
                        Kết quả tìm kiếm
                    <?php else: ?>
                        Tìm kiếm bài viết
                    <?php endif; ?>
                </h1>
                <?php if (!empty($keyword)): ?>
                    <p class="search-header__subtitle">
                        Tìm thấy <strong><?php echo number_format($totalPosts); ?></strong> kết quả cho
                        "<strong><?php echo htmlspecialchars($keyword); ?></strong>"
                    </p>
                <?php endif; ?>

                <!-- Form tìm kiếm -->
                <form action="<?php echo Router::url('/search'); ?>" method="GET" class="search-header__form">
                    <div class="search-header__input-wrapper">
                        <i class="fas fa-search search-header__input-icon"></i>
                        <input type="text"
                            name="q"
                            class="search-header__input"
                            placeholder="Nhập từ khóa tìm kiếm..."
                            value="<?php echo $keyword; ?>"
                            required
                            autofocus>
                        <button type="submit" class="search-header__submit">
                            Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="search-main">
        <div class="container">
            <div class="search-main__wrapper">
                <!-- Search Results -->
                <div class="search-content">
                    <?php if (empty($keyword)): ?>
                        <!-- No Search Query -->
                        <div class="search-content__prompt">
                            <i class="fas fa-search"></i>
                            <h2>Tìm kiếm bài viết</h2>
                            <p>Nhập từ khóa vào ô tìm kiếm phía trên để bắt đầu tìm kiếm</p>
                            <div class="search-content__suggestions">
                                <p class="search-content__suggestions-title">Gợi ý tìm kiếm:</p>
                                <div class="search-tags">
                                    <?php foreach (array_slice($tags, 0, 8) as $tag): ?>
                                        <a href="<?php echo Router::url('/search?q=' . urlencode($tag['name'])); ?>"
                                            class="search-tag">
                                            #<?php echo htmlspecialchars($tag['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php elseif (empty($posts)): ?>
                        <!-- No Results -->
                        <div class="search-content__empty">
                            <i class="fas fa-inbox"></i>
                            <h2>Không tìm thấy kết quả</h2>
                            <p>Không tìm thấy bài viết nào phù hợp với từ khóa "<strong><?php echo htmlspecialchars($keyword); ?></strong>"</p>

                            <div class="search-content__suggestions">
                                <h3>Gợi ý:</h3>
                                <ul>
                                    <li>Kiểm tra lại chính tả từ khóa</li>
                                    <li>Thử sử dụng từ khóa khác hoặc tổng quát hơn</li>
                                    <li>Tìm kiếm theo danh mục hoặc tag</li>
                                </ul>
                            </div>

                            <div class="search-content__actions">
                                <a href="<?php echo Router::url('/'); ?>" class="btn btn--primary">
                                    <i class="fas fa-home"></i> Về trang chủ
                                </a>
                                <a href="<?php echo Router::url('/posts'); ?>" class="btn btn--outline">
                                    <i class="fas fa-list"></i> Xem tất cả bài viết
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Results Info -->
                        <div class="search-results-info">
                            <p class="search-results-info__text">
                                Hiển thị <?php echo count($posts); ?> / <?php echo number_format($totalPosts); ?> kết quả
                            </p>
                        </div>

                        <!-- Results Grid -->
                        <div class="search-results-grid">
                            <?php foreach ($posts as $post): ?>
                                <article class="search-result">
                                    <a href="<?php echo Router::url('/post/' . $post['slug']); ?>"
                                        class="search-result__link">
                                        <div class="search-result__image">
                                            <img src="<?php echo ImageHelper::postCover($post['cover_image']); ?>"
                                                alt="<?php echo htmlspecialchars($post['title']); ?>"
                                                loading="lazy">
                                            <div class="search-result__overlay">
                                                <i class="fas fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </a>

                                    <div class="search-result__content">
                                        <?php if (!empty($post['category_name'])): ?>
                                            <a href="<?php echo Router::url('/category/' . $post['category_slug']); ?>"
                                                class="search-result__category">
                                                <i class="fas fa-folder"></i>
                                                <?php echo htmlspecialchars($post['category_name']); ?>
                                            </a>
                                        <?php endif; ?>

                                        <h3 class="search-result__title">
                                            <a href="<?php echo Router::url('/post/' . $post['slug']); ?>">
                                                <?php
                                                $highlightedTitle = preg_replace(
                                                    '/(' . preg_quote($keyword, '/') . ')/iu',
                                                    '<mark>$1</mark>',
                                                    htmlspecialchars($post['title'])
                                                );
                                                echo $highlightedTitle;
                                                ?>
                                            </a>
                                        </h3>

                                        <p class="search-result__excerpt">
                                            <?php
                                            $excerpt = htmlspecialchars(substr(strip_tags($post['excerpt'] ?? $post['content']), 0, 150));
                                            $highlightedExcerpt = preg_replace(
                                                '/(' . preg_quote($keyword, '/') . ')/iu',
                                                '<mark>$1</mark>',
                                                $excerpt
                                            );
                                            echo $highlightedExcerpt;
                                            ?>...
                                        </p>

                                        <div class="search-result__footer">
                                            <div class="search-result__author">
                                                <i class="fas fa-user-circle"></i>
                                                <span><?php echo htmlspecialchars($post['author_name']); ?></span>
                                            </div>
                                            <div class="search-result__meta">
                                                <span class="search-result__date">
                                                    <i class="far fa-calendar"></i>
                                                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                                </span>
                                                <span class="search-result__views">
                                                    <i class="far fa-eye"></i>
                                                    <?php echo number_format($post['views']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <!-- Phân trang -->
                        <?php if ($totalPages > 1): ?>
                            <nav class="pagination" aria-label="Phân trang">
                                <ul class="pagination__list">
                                    <?php if ($page > 1): ?>
                                        <li class="pagination__item">
                                            <a href="?q=<?php echo urlencode($keyword); ?>&page=<?php echo $page - 1; ?>"
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
                                            <a href="?q=<?php echo urlencode($keyword); ?>&page=1" class="pagination__link">1</a>
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
                                                <a href="?q=<?php echo urlencode($keyword); ?>&page=<?php echo $i; ?>"
                                                    class="pagination__link"><?php echo $i; ?></a>
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
                                            <a href="?q=<?php echo urlencode($keyword); ?>&page=<?php echo $totalPages; ?>"
                                                class="pagination__link"><?php echo $totalPages; ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="pagination__item">
                                            <a href="?q=<?php echo urlencode($keyword); ?>&page=<?php echo $page + 1; ?>"
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
                <aside class="search-sidebar">
                    <!-- Danh mục tìm kiếm -->
                    <div class="widget">
                        <h3 class="widget__title">Tìm theo danh mục</h3>
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
                    <?php if (!empty($recentPosts)): ?>
                        <div class="widget">
                            <h3 class="widget__title">Bài viết mới nhất</h3>
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
                    <?php endif; ?>

                    <!-- Tags Widget -->
                    <div class="widget">
                        <h3 class="widget__title">Tìm theo tag</h3>
                        <div class="tag-widget">
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?php echo Router::url('/search?q=' . urlencode($tag['name'])); ?>"
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