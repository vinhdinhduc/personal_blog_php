<?php
// Mock data - Chi tiết bài viết
$post = [
    'id' => 1,
    'title' => 'Hướng dẫn học PHP từ cơ bản đến nâng cao',
    'slug' => 'huong-dan-hoc-php',
    'content' => '<p>PHP (Hypertext Preprocessor) là một ngôn ngữ lập trình kịch bản phía máy chủ được thiết kế đặc biệt cho phát triển web. PHP là một trong những ngôn ngữ phổ biến nhất để xây dựng các trang web động và ứng dụng web.</p>

<h2>1. Giới thiệu về PHP</h2>
<p>PHP được tạo ra bởi Rasmus Lerdorf vào năm 1994 và đã trải qua nhiều phiên bản cải tiến. Hiện tại, PHP 8.x là phiên bản mới nhất với nhiều tính năng hiện đại và cải thiện hiệu suất đáng kể.</p>

<h2>2. Cài đặt môi trường PHP</h2>
<p>Để bắt đầu lập trình PHP, bạn cần cài đặt:</p>
<ul>
    <li>Web server (Apache, Nginx)</li>
    <li>PHP interpreter</li>
    <li>Database (MySQL, PostgreSQL)</li>
</ul>

<h2>3. Cú pháp cơ bản</h2>
<p>PHP code được nhúng trong các thẻ <code>&lt;?php ?&gt;</code>. Ví dụ đơn giản:</p>
<pre><code>&lt;?php
echo "Hello World!";
?&gt;</code></pre>

<h2>4. Biến và kiểu dữ liệu</h2>
<p>PHP là ngôn ngữ động, bạn không cần khai báo kiểu dữ liệu cho biến. Các kiểu dữ liệu cơ bản bao gồm: string, integer, float, boolean, array, object.</p>

<h2>5. Kết luận</h2>
<p>PHP là một ngôn ngữ mạnh mẽ và linh hoạt cho phát triển web. Với cộng đồng lớn và nhiều framework hỗ trợ, PHP vẫn là lựa chọn tuyệt vời cho các dự án web hiện đại.</p>',
    'cover_image' => 'https://images.unsplash.com/photo-1599507593499-a3f7d7d97667?w=1200',
    'category' => ['id' => 2, 'name' => 'Lập trình'],
    'author' => [
        'id' => 1,
        'first_name' => 'Nguyễn',
        'last_name' => 'Văn A',
        'email' => 'nguyenvana@example.com'
    ],
    'tags' => ['PHP', 'Backend', 'Web Development'],
    'views' => 1523,
    'created_at' => '2025-11-10 14:30:00',
    'updated_at' => '2025-11-10 14:30:00'
];

// Mock comments
$comments = [
    [
        'id' => 1,
        'user' => ['first_name' => 'Trần', 'last_name' => 'Thị B'],
        'content' => 'Bài viết rất hữu ích! Cảm ơn tác giả đã chia sẻ.',
        'created_at' => '2025-11-11 10:20:00',
        'is_approved' => true
    ],
    [
        'id' => 2,
        'user' => ['first_name' => 'Lê', 'last_name' => 'Văn C'],
        'content' => 'Mình đang học PHP, bài này giúp mình hiểu rõ hơn nhiều. Cảm ơn bạn!',
        'created_at' => '2025-11-11 15:45:00',
        'is_approved' => true
    ]
];

$page_title = $post['title'];
include 'header.php';
?>

<style>
    .post-detail-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .post-header {
        margin-bottom: 2rem;
    }

    .post-category-tag {
        display: inline-block;
        background-color: #6366f1;
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .post-header h1 {
        font-size: 2.5rem;
        line-height: 1.3;
        margin-bottom: 1.5rem;
        color: #1a1a1a;
        font-weight: 700;
    }

    .post-meta-info {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 1rem 0;
        border-top: 1px solid #e5e5e5;
        border-bottom: 1px solid #e5e5e5;
        color: #666;
        font-size: 0.95rem;
    }

    .author-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .author-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    .post-cover {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 12px;
        margin: 2rem 0;
    }

    .post-body {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }

    .post-body h2 {
        font-size: 1.75rem;
        margin: 2rem 0 1rem;
        color: #1a1a1a;
        font-weight: 600;
    }

    .post-body p {
        margin-bottom: 1.5rem;
    }

    .post-body ul,
    .post-body ol {
        margin: 1rem 0 1.5rem 2rem;
    }

    .post-body li {
        margin-bottom: 0.5rem;
    }

    .post-body code {
        background-color: #f5f5f5;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 0.95em;
    }

    .post-body pre {
        background-color: #1a1a1a;
        color: #f5f5f5;
        padding: 1.5rem;
        border-radius: 8px;
        overflow-x: auto;
        margin: 1.5rem 0;
    }

    .post-body pre code {
        background: none;
        padding: 0;
        color: inherit;
    }

    .post-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin: 2rem 0;
        padding: 2rem 0;
        border-top: 1px solid #e5e5e5;
    }

    .tag {
        background-color: #f0f0f0;
        color: #666;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s;
    }

    .tag:hover {
        background-color: #e0e0e0;
    }

    /* Comments Section */
    .comments-section {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 2px solid #e5e5e5;
    }

    .comments-section h3 {
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        color: #1a1a1a;
    }

    .comment-form {
        background-color: #f9f9f9;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .comment-form textarea {
        width: 100%;
        min-height: 100px;
        padding: 1rem;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        font-size: 1rem;
        resize: vertical;
        font-family: inherit;
    }

    .comment-form textarea:focus {
        outline: none;
        border-color: #6366f1;
    }

    .comment-form button {
        margin-top: 1rem;
        padding: 0.75rem 1.5rem;
        background-color: #1a1a1a;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .comment-form button:hover {
        background-color: #333;
    }

    .comment {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e5e5;
    }

    .comment:last-child {
        border-bottom: none;
    }

    .comment-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .comment-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .comment-author {
        font-weight: 600;
        color: #1a1a1a;
    }

    .comment-date {
        color: #999;
        font-size: 0.85rem;
    }

    .comment-content {
        color: #333;
        line-height: 1.6;
        padding-left: 3rem;
    }

    @media (max-width: 768px) {
        .post-detail-container {
            padding: 1rem;
        }

        .post-header h1 {
            font-size: 1.75rem;
        }

        .post-cover {
            height: 250px;
        }

        .post-meta-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<div class="post-detail-container">
    <!-- Post Header -->
    <article class="post-header">
        <a href="category.php?id=<?php echo $post['category']['id']; ?>" class="post-category-tag">
            <?php echo $post['category']['name']; ?>
        </a>

        <h1><?php echo $post['title']; ?></h1>

        <div class="post-meta-info">
            <div class="author-info">
                <div class="author-avatar">
                    <?php echo strtoupper(substr($post['author']['first_name'], 0, 1)); ?>
                </div>
                <span><?php echo $post['author']['first_name'] . ' ' . $post['author']['last_name']; ?></span>
            </div>
            <span>📅 <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
            <span>👁️ <?php echo number_format($post['views']); ?> lượt xem</span>
        </div>
    </article>

    <!-- Cover Image -->
    <img src="<?php echo $post['cover_image']; ?>" alt="<?php echo $post['title']; ?>" class="post-cover">

    <!-- Post Content -->
    <div class="post-body">
        <?php echo $post['content']; ?>
    </div>

    <!-- Tags -->
    <div class="post-tags">
        <strong style="margin-right: 0.5rem;">Tags:</strong>
        <?php foreach ($post['tags'] as $tag): ?>
            <a href="#" class="tag">#<?php echo $tag; ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Comments Section -->
    <section class="comments-section">
        <h3>Bình luận (<?php echo count($comments); ?>)</h3>

        <!-- Comment Form -->
        <div class="comment-form">
            <form method="POST" action="">
                <textarea name="comment" placeholder="Viết bình luận của bạn..." required></textarea>
                <button type="submit">Gửi bình luận</button>
            </form>
        </div>

        <!-- Comments List -->
        <div class="comments-list">
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="comment-header">
                        <div class="comment-avatar">
                            <?php echo strtoupper(substr($comment['user']['first_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="comment-author">
                                <?php echo $comment['user']['first_name'] . ' ' . $comment['user']['last_name']; ?>
                            </div>
                            <div class="comment-date">
                                <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                    <div class="comment-content">
                        <?php echo $comment['content']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>