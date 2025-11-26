<?php

/**
 * Post Detail View with Comments
 * File: app/views/post/view.php
 * 
 * Hiển thị chi tiết bài viết với hệ thống comment threaded
 * Theo chuẩn BEM (Block Element Modifier)
 * 
 * Variables:
 * - $post: Array - Thông tin bài viết
 * - $comments: Array - Danh sách comments (threaded)
 * - $relatedPosts: Array - Bài viết liên quan
 */

require_once __DIR__ . '/../../helpers/ImageHelper.php';
require_once __DIR__ . '/../../helpers/CommentHelper.php';
?>

<!-- ============================================
     POST DETAIL CONTAINER
============================================ -->
<div class="post-detail">
    <div class="post-detail__container">

        <!-- ============================================
             POST CONTENT
        ============================================ -->
        <article class="post">

            <!-- Breadcrumb -->
            <nav class="post__breadcrumb">
                <a href="<?php echo Router::url('/'); ?>" class="post__breadcrumb-link">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
                <span class="post__breadcrumb-separator">/</span>
                <a href="<?php echo Router::url('/category/' . $post['category_slug']); ?>"
                    class="post__breadcrumb-link">
                    <?php echo Security::escape($post['category_name']); ?>
                </a>
                <span class="post__breadcrumb-separator">/</span>
                <span class="post__breadcrumb-current">
                    <?php echo Security::escape($post['title']); ?>
                </span>
            </nav>

            <!-- Post Header -->
            <header class="post__header">
                <h1 class="post__title"><?php echo Security::escape($post['title']); ?></h1>

                <div class="post__meta">
                    <div class="post__author">
                        <div class="post__author-avatar">
                            <?php echo strtoupper(substr($post['author_name'], 0, 2)); ?>
                        </div>
                        <div class="post__author-info">
                            <span class="post__author-name">
                                <?php echo Security::escape($post['author_name']); ?>
                            </span>
                            <div class="post__meta-items">
                                <span class="post__meta-item">
                                    <i class="far fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                                </span>
                                <span class="post__meta-item">
                                    <i class="far fa-eye"></i>
                                    <?php echo number_format($post['views']); ?> lượt xem
                                </span>
                                <span class="post__meta-item">
                                    <i class="far fa-comments"></i>
                                    <?php echo CommentHelper::countTotalComments($comments); ?> bình luận
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Share Buttons -->
                    <div class="post__share">
                        <button class="post__share-btn" title="Share on Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="post__share-btn" title="Share on Twitter">
                            <i class="fab fa-twitter"></i>
                        </button>
                        <button class="post__share-btn" title="Copy Link">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Hero Image -->
            <div class="post-hero__image">
                <img src="<?= ImageHelper::postCover($post['cover_image']) ?>"
                    alt="<?= htmlspecialchars($post['title']) ?>">
            </div>

            <!-- Author Avatar -->
            <div class="author-box__avatar">
                <img src="<?= ImageHelper::profile($post['author_avatar'], $post['author_email']) ?>"
                    alt="<?= htmlspecialchars($post['author_name']) ?>">
            </div>

            <!-- Post Content -->
            <div class="post__content">
                <?php echo $post['content']; ?>
            </div>

            <!-- Tags -->
            <?php if (!empty($post['tags'])): ?>
                <div class="post__tags">
                    <span class="post__tags-label">
                        <i class="fas fa-tags"></i> Tags:
                    </span>
                    <?php foreach ($post['tags'] as $tag): ?>
                        <a href="<?php echo Router::url('/tag/' . $tag['slug']); ?>"
                            class="post__tag">
                            <?php echo Security::escape($tag['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Post Footer Actions -->
            <footer class="post__footer">
                <div class="post__actions">
                    <button class="post__action-btn post__action-btn--like" id="likeBtn">
                        <i class="far fa-heart"></i>
                        <span>Thích</span>
                    </button>
                    <button class="post__action-btn" onclick="document.getElementById('commentForm').scrollIntoView({behavior: 'smooth'})">
                        <i class="far fa-comment"></i>
                        <span>Bình luận</span>
                    </button>
                    <button class="post__action-btn" id="shareBtn">
                        <i class="fas fa-share-alt"></i>
                        <span>Chia sẻ</span>
                    </button>
                </div>
            </footer>

        </article>

        <!-- ============================================
             COMMENTS SECTION
        ============================================ -->
        <section class="comments" id="comments">

            <div class="comments__header">
                <h2 class="comments__title">
                    <i class="fas fa-comments"></i>
                    <?php echo CommentHelper::getCommentCountText(CommentHelper::countTotalComments($comments)); ?>
                </h2>
            </div>

            <!-- Comment Form -->
            <?php if (Session::isLoggedIn()): ?>
                <div class="comment-form" id="commentForm">
                    <h3 class="comment-form__title">Để lại bình luận</h3>
                    <p class="comment-form__user">
                        Đăng nhập với tên: <strong><?php echo Security::escape(Session::get('user_data')['name'] ?? 'User'); ?></strong>
                    </p>

                    <form method="POST" action="<?php echo Router::url('/comment/create'); ?>" class="comment-form__form">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">

                        <div class="comment-form__field">
                            <textarea name="content"
                                class="comment-form__textarea"
                                rows="4"
                                placeholder="Nhập bình luận của bạn..."
                                required
                                minlength="3"
                                maxlength="5000"></textarea>
                            <small class="comment-form__hint">Tối thiểu 3 ký tự, tối đa 5000 ký tự</small>
                        </div>

                        <button type="submit" class="comment-form__submit btn btn--primary">
                            <i class="fas fa-paper-plane"></i> Gửi bình luận
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="comment-form comment-form--login">
                    <div class="comment-form__login-prompt">
                        <i class="fas fa-lock"></i>
                        <p>Bạn cần <a href="<?php echo Router::url('/login'); ?>" class="comment-form__login-link">đăng nhập</a> để bình luận.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments List -->
            <div class="comments__list">
                <?php if (empty($comments)): ?>
                    <div class="comments__empty">
                        <i class="far fa-comments"></i>
                        <p>Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                    </div>
                <?php else: ?>
                    <?php CommentHelper::renderCommentTree($comments, 0, 3); ?>
                <?php endif; ?>
            </div>

        </section>

        <!-- ============================================
             RELATED POSTS SECTION
        ============================================ -->
        <?php if (!empty($relatedPosts)): ?>
            <section class="related-posts">
                <h2 class="related-posts__title">Bài viết liên quan</h2>

                <div class="related-posts__grid">
                    <?php foreach (array_slice($relatedPosts, 0, 3) as $relatedPost): ?>
                        <article class="related-post">
                            <a href="<?php echo Router::url('/post/' . $relatedPost['slug']); ?>" class="related-post__link">
                                <?php if (!empty($relatedPost['cover_image'])): ?>
                                    <div class="related-post__image">
                                        <img src="<?php echo Router::url($relatedPost['cover_image']); ?>"
                                            alt="<?php echo Security::escape($relatedPost['title']); ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="related-post__content">
                                    <h3 class="related-post__title">
                                        <?php echo Security::escape($relatedPost['title']); ?>
                                    </h3>
                                    <div class="related-post__meta">
                                        <span class="related-post__date">
                                            <i class="far fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($relatedPost['created_at'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

    </div>
</div>