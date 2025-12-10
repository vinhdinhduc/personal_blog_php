<!-- Page Header -->
<div class="add-post-page-header">
    <div class="page-header__container">
        <div>
            <h1 class="page-header__title">
                <i class="fas fa-plus-circle" style="color: #27ae60; margin-right: 8px;"></i>
                Thêm bài viết mới
            </h1>
            <nav class="breadcrumb">
                <a href="/admin/dashboard" class="breadcrumb__link">
                    <i class="fas fa-home"></i>
                    Admin
                </a>
                <span class="breadcrumb__separator">/</span>
                <a href=<?php echo Router::url("/admin/posts") ?> class="breadcrumb__link">Bài viết</a>
                <span class="breadcrumb__separator">/</span>
                <span>Thêm mới</span>
            </nav>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
            <a href=<?php echo Router::url("/admin/posts") ?> class="btn btn--secondary btn--small">
                <i class="btn__icon fas fa-list"></i>
                Danh sách
            </a>
        </div>
    </div>
</div>

<?php


$post = [];
$isEdit = false;
$formAction = Router::url("/admin/posts/store");
$csrfToken = $csrfToken ?? Security::generateCSRFToken();


require_once __DIR__ . '/_form.php';
?>


<!-- Additional JS for Add Page -->
<script>
    // Show welcome message for new post
    document.addEventListener('DOMContentLoaded', function() {
        // Set default values
        const now = new Date();
        const formatted = now.toISOString().slice(0, 16);

        // Auto-fill publish date with current time
        const publishInput = document.querySelector('input[name="published_at"]');
        if (publishInput && !publishInput.value) {
            publishInput.value = formatted;
        }

        // Focus on title field
        const titleInput = document.getElementById('postTitle');
        if (titleInput) {
            titleInput.focus();
        }


    });
</script>