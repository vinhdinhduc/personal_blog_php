<?php

/**
 * Category List View - Hiển thị tất cả danh mục
 * Dữ liệu nhận từ CategoryPageController::index()
 */
require_once __DIR__ . '/../../helpers/ImageHelper.php';
?>

<!-- Category Hero -->
<section class="category-hero">
    <div class="category-hero__container">
        <h1 class="category-hero__title">Khám phá danh mục</h1>
        <p class="category-hero__description">
            Tìm kiếm và khám phá các bài viết theo chủ đề yêu thích của bạn.
            Chúng tôi có nhiều danh mục phong phú với nội dung chất lượng cao.
        </p>
        <div class="category-hero__stats">
            <div class="category-hero__stat">
                <div class="category-hero__stat-value"><?= count($categories ?? []) ?></div>
                <div class="category-hero__stat-label">Danh mục</div>
            </div>
            <div class="category-hero__stat">
                <div class="category-hero__stat-value">
                    <?= array_sum(array_column($categories ?? [], 'post_count')) ?>
                </div>
                <div class="category-hero__stat-label">Bài viết</div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Categories -->
<?php if (isset($popularCategories) && count($popularCategories) > 0): ?>
    <section class="category-grid" style="padding-top: 80px;">
        <div class="category-grid__container">
            <div class="category-grid__header">
                <h2 class="category-grid__title">Danh mục phổ biến</h2>
                <p class="category-grid__subtitle">Các danh mục được quan tâm nhiều nhất</p>
            </div>
            <div class="category-grid__list">
                <?php foreach ($popularCategories as $category): ?>
                    <article class="category-card"
                        style="--card-color: <?= $category['color'] ?? '#667eea' ?>; --card-color-dark: <?= $category['color_dark'] ?? '#764ba2' ?>">
                        <div class="category-card__header">
                            <div class="category-card__icon">
                                <i class="<?= htmlspecialchars($category['icon'] ?? 'fas fa-folder') ?>"></i>
                            </div>
                            <h3 class="category-card__name"><?= htmlspecialchars($category['name']) ?></h3>
                            <div class="category-card__count">
                                <i class="fas fa-newspaper"></i>
                                <?= $category['post_count'] ?> bài viết
                            </div>
                        </div>
                        <div class="category-card__body">
                            <?php if (!empty($category['description'])): ?>
                                <p class="category-card__description">
                                    <?= htmlspecialchars($category['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="category-card__footer">
                            <a href="<?php echo Router::url('/category/' . $category['slug']); ?>"
                                class="category-card__link">
                                Xem bài viết
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- All Categories -->
<section class="category-grid">
    <div class="category-grid__container">
        <div class="category-grid__header">
            <h2 class="category-grid__title">Tất cả danh mục</h2>
            <p class="category-grid__subtitle">Khám phá toàn bộ nội dung của chúng tôi</p>
        </div>

        <?php if (isset($categories) && count($categories) > 0): ?>
            <div class="category-grid__list">
                <?php foreach ($categories as $category): ?>
                    <article class="category-card"
                        style="--card-color: <?= $category['color'] ?? '#667eea' ?>; --card-color-dark: <?= $category['color_dark'] ?? '#764ba2' ?>">
                        <div class="category-card__header">
                            <div class="category-card__icon">
                                <i class="<?= htmlspecialchars($category['icon'] ?? 'fas fa-folder') ?>"></i>
                            </div>
                            <h3 class="category-card__name"><?= htmlspecialchars($category['name']) ?></h3>
                            <div class="category-card__count">
                                <i class="fas fa-newspaper"></i>
                                <?= $category['post_count'] ?> bài viết
                            </div>
                        </div>

                        <div class="category-card__body">
                            <?php if (!empty($category['description'])): ?>
                                <p class="category-card__description">
                                    <?= htmlspecialchars($category['description']) ?>
                                </p>
                            <?php endif; ?>

                            <?php if (isset($category['recent_posts']) && count($category['recent_posts']) > 0): ?>
                                <div class="category-card__posts">
                                    <h4 class="category-card__posts-title">
                                        <i class="fas fa-fire"></i>
                                        Bài viết mới nhất
                                    </h4>
                                    <div class="category-card__posts-list">
                                        <?php foreach (array_slice($category['recent_posts'], 0, 3) as $post): ?>
                                            <a href="<?php echo Router::url('/post/' . $post['slug']); ?>"
                                                class="category-card__post-item">
                                                <i class="fas fa-chevron-right category-card__post-icon"></i>
                                                <span><?= htmlspecialchars($post['title']) ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="category-card__footer">
                            <a href="<?php echo Router::url('/category/' . $category['slug']); ?>"
                                class="category-card__link">
                                Xem tất cả bài viết
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open empty-state__icon"></i>
                <h3 class="empty-state__title">Chưa có danh mục nào</h3>
                <p class="empty-state__text">Hiện tại chưa có danh mục nào được tạo. Vui lòng quay lại sau!</p>
                <a href="<?php echo Router::url('/'); ?>" class="empty-state__button">
                    <i class="fas fa-home"></i>
                    Về trang chủ
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    /* Color variants for different categories */
    .category-card:nth-child(6n+1) {
        --card-color: #667eea;
        --card-color-dark: #764ba2;
    }

    .category-card:nth-child(6n+2) {
        --card-color: #f093fb;
        --card-color-dark: #f5576c;
    }

    .category-card:nth-child(6n+3) {
        --card-color: #4facfe;
        --card-color-dark: #00f2fe;
    }

    .category-card:nth-child(6n+4) {
        --card-color: #43e97b;
        --card-color-dark: #38f9d7;
    }

    .category-card:nth-child(6n+5) {
        --card-color: #fa709a;
        --card-color-dark: #fee140;
    }

    .category-card:nth-child(6n+6) {
        --card-color: #30cfd0;
        --card-color-dark: #330867;
    }
</style>