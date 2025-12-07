<?php

/**
 * Category Detail View - Hiển thị bài viết trong một danh mục
 * Dữ liệu nhận từ CategoryPageController::show()
 */
require_once __DIR__ . '/../../helpers/ImageHelper.php';

// Đảm bảo có category data
if (!isset($category) || !$category) {
    header('Location: ' . Router::url('/category'));
    exit;
}

$slug = $category['slug'] ?? '';
$totalPosts = $totalPosts ?? 0;
$totalPages = $totalPages ?? 1;
$currentPage = $currentPage ?? 1;
$posts = $posts ?? [];

$relatedCategories = $relatedCategories ?? [];
$featuredPosts = $featuredPosts ?? [];
?>

<!-- Category Header -->
<section class="category-hero" style="background: linear-gradient(135deg, <?= $category['color'] ?? '#667eea' ?> 0%, <?= $category['color_dark'] ?? '#764ba2' ?> 100%);">
    <div class="category-hero__container">
        <div class="category-card__icon" style="font-size: 64px; margin-bottom: 20px;">
            <i class="<?= htmlspecialchars($category['icon'] ?? 'fas fa-folder') ?>"></i>
        </div>
        <h1 class="category-hero__title"><?= htmlspecialchars($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p class="category-hero__description">
                <?= htmlspecialchars($category['description']) ?>
            </p>
        <?php endif; ?>
        <div class="category-hero__stats">
            <div class="category-hero__stat">
                <div class="category-hero__stat-value"><?= $totalPosts ?></div>
                <div class="category-hero__stat-label">Bài viết</div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="posts-section">
    <div class="posts-section__container">

        <div style="display: grid; grid-template-columns: 1fr 320px; gap: 40px;">
            <!-- Posts Grid -->
            <div>
                <div class="posts-section__header">
                    <h2 class="posts-section__title">Bài viết</h2>
                </div>

                <?php if (isset($posts) && count($posts) > 0): ?>
                    <div class="posts-section__grid">
                        <?php foreach ($posts as $post): ?>
                            <article class="post-card_user">
                                <a href="<?php echo Router::url('/post/' . $post['slug']); ?>" class="post-card_user__image-wrapper">
                                    <img src="<?= ImageHelper::postCover($post['cover_image'] ?? '') ?>"
                                        alt="<?= htmlspecialchars($post['title']) ?>"
                                        class="post-card_user__image">
                                    <?php if (!empty($post['is_featured'])): ?>
                                        <span class="post-card_user__badge">
                                            <i class="fas fa-star"></i> Nổi bật
                                        </span>
                                    <?php endif; ?>
                                </a>

                                <div class="post-card_user__body">
                                    <h3 class="post-card_user__title">
                                        <a href="<?php echo Router::url('/post/' . $post['slug']); ?>" style="text-decoration: none; color: inherit;">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h3>

                                    <?php if (!empty($post['excerpt'])): ?>
                                        <p class="post-card_user__excerpt">
                                            <?= htmlspecialchars($post['excerpt']) ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="post-card_user__meta">
                                        <div class="post-card_user__author">

                                            <img src="<?= ImageHelper::avatar($post['author_avatar'] ?? '') ?>"
                                                alt="<?= htmlspecialchars($post['author_name'] ?? 'Author') ?>"
                                                class="post-card_user__author-avatar">
                                            <span><?= htmlspecialchars($post['author_name'] ?? 'Anonymous') ?></span>
                                        </div>
                                        <div class="post-card_user__date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d/m/Y', strtotime($post['created_at'])) ?>
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
                                <a href="?page=<?= $currentPage - 1 ?>" class="pagination__item">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                                    <a href="?page=<?= $i ?>"
                                        class="pagination__item <?= $i == $currentPage ? 'pagination__item--active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php elseif (abs($i - $currentPage) == 3): ?>
                                    <span class="pagination__item pagination__item--disabled">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>" class="pagination__item">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-newspaper empty-state__icon"></i>
                        <h3 class="empty-state__title">Chưa có bài viết</h3>
                        <p class="empty-state__text">
                            Danh mục này chưa có bài viết nào. Hãy quay lại sau để khám phá thêm!
                        </p>
                        <a href="<?php echo Router::url('/category'); ?>" class="empty-state__button">
                            <i class="fas fa-arrow-left"></i>
                            Xem danh mục khác
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Featured Posts -->
                <?php if (isset($featuredPosts) && count($featuredPosts) > 0): ?>
                    <div class="sidebar__widget">
                        <h3 class="sidebar__widget-title">
                            <i class="fas fa-star"></i>
                            Bài viết nổi bật
                        </h3>
                        <div class="sidebar__category-list">
                            <?php foreach ($featuredPosts as $post): ?>
                                <a href="<?php echo Router::url('/post/' . $post['slug']); ?>"
                                    class="sidebar__category-item">
                                    <div class="sidebar__category-name">
                                        <i class="fas fa-fire" style="color: #f6c23e;"></i>
                                        <span style="font-size: 14px;">
                                            <?= htmlspecialchars(mb_substr($post['title'], 0, 50)) ?>...
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Related Categories -->
                <?php if (isset($relatedCategories) && count($relatedCategories) > 0): ?>
                    <div class="sidebar__widget">
                        <h3 class="sidebar__widget-title">
                            <i class="fas fa-layer-group"></i>
                            Danh mục liên quan
                        </h3>
                        <div class="sidebar__category-list">
                            <?php foreach ($relatedCategories as $relatedCat): ?>
                                <a href="<?php echo Router::url('/category/' . $relatedCat['slug']); ?>"
                                    class="sidebar__category-item">
                                    <div class="sidebar__category-name">
                                        <i class="<?= htmlspecialchars($relatedCat['icon'] ?? 'fas fa-folder') ?>"></i>
                                        <span><?= htmlspecialchars($relatedCat['name']) ?></span>
                                    </div>
                                    <span class="sidebar__category-count">
                                        <?= $relatedCat['post_count'] ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Back to Categories -->
                <div class="sidebar__widget" style="text-align: center;">
                    <a href="<?php echo Router::url('/category'); ?>"
                        class="empty-state__button"
                        style="display: inline-flex; width: auto;">
                        <i class="fas fa-arrow-left"></i>
                        Tất cả danh mục
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<style>
    /* Responsive sidebar */
    @media (max-width: 1024px) {
        .posts-section__container>div {
            grid-template-columns: 1fr !important;
        }

        .sidebar {
            margin-top: 40px;
        }
    }
</style>