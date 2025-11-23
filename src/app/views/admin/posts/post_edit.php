<div class="add-post-page-header">
    <div class="page-header__container">
        <div class="page-header__content">
            <h1 class="page-header__title">
                <i class="fas fa-edit" style="color: #3498db;"></i>
                <span>Chỉnh sửa bài viết</span>
                <span class="page-header__id">#<?= $post['id'] ?? '' ?></span>
            </h1>
            <nav class="breadcrumb">
                <a href="/admin/dashboard" class="breadcrumb__link">
                    <i class="fas fa-home"></i>
                    Admin
                </a>
                <span class="breadcrumb__separator">/</span>
                <a href="/admin/posts" class="breadcrumb__link">Bài viết</a>
                <span class="breadcrumb__separator">/</span>
                <span>Chỉnh sửa</span>
            </nav>
        </div>

        <div class="page-header__actions">
            <a href="/admin/posts" class="btn btn--secondary">
                <i class="btn__icon fas fa-list"></i>
                Danh sách
            </a>
            <button type="button"
                class="btn btn--danger"
                onclick="confirmDelete()">
                <i class="btn__icon fas fa-trash"></i>
                Xóa bài viết
            </button>
        </div>
    </div>
</div>



<?php

$isEdit = true;
$formAction = Router::url() . '/admin/posts/update/' . $post['id'];
$csrfToken = $csrfToken ?? Security::generateCSRFToken();

require_once __DIR__ . '/_form.php';
?>



<script>
    // Delete confirmation
    function confirmDelete() {
        const postTitle = <?= json_encode($post['title'] ?? '') ?>;

        if (confirm(`Bạn có chắc chắn muốn xóa bài viết "${postTitle}"?\n\nHành động này không thể hoàn tác!`)) {
            // Create form for DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/posts/delete/<?= $post['id'] ?>';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= $csrfToken ?>';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Auto-hide flash messages
    document.addEventListener('DOMContentLoaded', function() {
        const flashAlert = document.getElementById('flashAlert');
        if (flashAlert) {
            setTimeout(() => {
                flashAlert.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => {
                    flashAlert.remove();
                }, 300);
            }, 5000);
        }

        // Load last saved time
        const lastSaved = localStorage.getItem('post_<?= $post['id'] ?>_last_saved');
        if (lastSaved) {
            const savedTime = new Date(parseInt(lastSaved));
            const now = new Date();
            const diffMinutes = Math.floor((now - savedTime) / 60000);

            if (diffMinutes < 60) {
                showNotification(`Lần lưu cuối: ${diffMinutes} phút trước`, 'info');
            }
        }
    });

    // Track edit time
    let editStartTime = Date.now();

    window.addEventListener('beforeunload', function() {
        const editDuration = Math.floor((Date.now() - editStartTime) / 1000);
        console.log('Edit duration:', editDuration, 'seconds');
    });

    // Slide up animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    `;
    document.head.appendChild(style);
</script>