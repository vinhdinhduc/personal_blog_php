<div class="page-header">
    <div class="page-header__info">
        <h1 class="page-header__title">Chỉnh sửa người dùng #<?= $user['id'] ?? '' ?></h1>
        <nav class="page-header__breadcrumb">
            <a href="/admin/dashboard" class="page-header__breadcrumb-link">Admin</a>
            <span class="page-header__breadcrumb-separator">/</span>
            <a href="/admin/users" class="page-header__breadcrumb-link">Người dùng</a>
            <span class="page-header__breadcrumb-separator">/</span>
            <span class="page-header__breadcrumb-current">Chỉnh sửa</span>
        </nav>
    </div>
    <a href="/admin/users" class="btn btn--secondary">
        <i class="fas fa-arrow-left"></i>
        <span>Quay lại</span>
    </a>
</div>

<!-- Alert Message -->
<?php if (isset($message)): ?>
    <div class="alert alert--<?= $message['type'] ?>">
        <i class="fas fa-<?= $message['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> alert__icon"></i>
        <span class="alert__text"><?= $message['text'] ?></span>
    </div>
<?php endif; ?>

<!-- Form Container -->
<form action=<?php echo Router::url("/admin/users/update/{$user['id']}") ?> method="POST" enctype="multipart/form-data" class="user-form">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">

    <div class="user-form__layout">
        <!-- Main Content -->
        <div class="user-form__main">
            <!-- Basic Info -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-user-circle form-card__icon"></i>
                    <h3 class="form-card__title">Thông tin cơ bản</h3>
                </div>
                <div class="form-card__body">
                    <!-- Name Fields -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-group__label" for="first_name">
                                Họ <span class="form-group__required">*</span>
                            </label>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                class="form-group__input"
                                value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-group__label" for="last_name">
                                Tên <span class="form-group__required">*</span>
                            </label>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                class="form-group__input"
                                value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-group__label" for="email">
                            Email <span class="form-group__required">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-group__input"
                            value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                            required>
                    </div>
                </div>
            </div>

            <!-- Avatar Upload Card -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-camera form-card__icon"></i>
                    <h3 class="form-card__title">Ảnh đại diện</h3>
                </div>
                <div class="form-card__body">
                    <div class="avatar-upload">
                        <div class="avatar-upload__preview" id="avatarPreview">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= Router::url($user['avatar']) ?>"
                                    class="avatar-upload__image"
                                    alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-upload__placeholder">
                                    <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="avatar-upload__input-wrapper">
                            <label for="avatarInput" class="btn btn--secondary btn--block">
                                <i class="fas fa-upload"></i>
                                <span>Chọn ảnh mới</span>
                            </label>
                            <input type="file"
                                id="avatarInput"
                                name="avatar"
                                class="avatar-upload__input"
                                accept="image/*"
                                onchange="previewAvatar(this)">

                            <small class="form-group__hint">
                                <i class="fas fa-info-circle"></i>
                                JPG, PNG hoặc GIF. Kích thước khuyến nghị: 300x300px (Tối đa 2MB)
                            </small>

                            <?php if (!empty($user['avatar'])): ?>
                                <button type="button" class="btn btn--danger btn--sm" onclick="removeAvatar()" style="margin-top: 10px;">
                                    <i class="fas fa-trash"></i>
                                    Xóa ảnh hiện tại
                                </button>
                                <input type="hidden" name="remove_avatar" id="removeAvatarFlag" value="0">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-key form-card__icon"></i>
                    <h3 class="form-card__title">Đổi mật khẩu (Tùy chọn)</h3>
                </div>
                <div class="form-card__body">
                    <div class="alert alert--warning alert--inline">
                        <i class="fas fa-exclamation-triangle alert__icon"></i>
                        <span class="alert__text">Chỉ điền vào nếu bạn muốn thay đổi mật khẩu</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-group__label" for="password">Mật khẩu mới</label>
                            <div class="form-group__input-wrapper">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-group__input form-group__input--has-icon"
                                    placeholder="Để trống nếu không đổi"
                                    minlength="6">
                                <button type="button" class="form-group__toggle-password" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-group__label" for="password_confirm">Xác nhận mật khẩu</label>
                            <div class="form-group__input-wrapper">
                                <input
                                    type="password"
                                    id="password_confirm"
                                    name="password_confirm"
                                    class="form-group__input form-group__input--has-icon"
                                    placeholder="Xác nhận mật khẩu mới"
                                    minlength="6">
                                <button type="button" class="form-group__toggle-password" data-target="password_confirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-card__footer form-card__footer--between">
                    <?php if (isset($_SESSION['user_id']) && $user['id'] != $_SESSION['user_id']): ?>
                        <button type="button" class="btn btn--danger" onclick="confirmDelete(<?= $user['id'] ?>)">
                            <i class="fas fa-trash"></i>
                            <span>Xóa người dùng</span>
                        </button>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn--primary">
                        <i class="fas fa-save"></i>
                        <span>Cập nhật</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="user-form__sidebar">
            <!-- User Avatar -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-user form-card__icon"></i>
                    <h3 class="form-card__title">Avatar</h3>
                </div>
                <div class="form-card__body">
                    <div class="user-avatar">
                        <div class="user-avatar__image">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?= Router::url($user['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="user-avatar__name">
                            <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
                        </div>
                        <div class="user-avatar__email">
                            <?= htmlspecialchars($user['email'] ?? '') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Selection -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-user-tag form-card__icon"></i>
                    <h3 class="form-card__title">Vai trò</h3>
                </div>
                <div class="form-card__body">
                    <div class="form-group">
                        <select name="role" id="role" class="form-group__select"
                            <?= (isset($_SESSION['user_id']) && $user['id'] == $_SESSION['user_id']) ? 'disabled' : '' ?>>
                            <option value="user" <?= ($user['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>
                                User - Người dùng
                            </option>
                            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                Admin - Quản trị viên
                            </option>
                        </select>
                        <?php if (isset($_SESSION['user_id']) && $user['id'] == $_SESSION['user_id']): ?>
                            <span class="form-group__hint form-group__hint--warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Không thể đổi vai trò của chính mình
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-info-circle form-card__icon"></i>
                    <h3 class="form-card__title">Thông tin tài khoản</h3>
                </div>
                <div class="form-card__body">
                    <ul class="info-list">
                        <li class="info-list__item">
                            <span class="info-list__label">
                                <i class="fas fa-hashtag"></i> ID
                            </span>
                            <span class="info-list__value">#<?= $user['id'] ?></span>
                        </li>
                        <li class="info-list__item">
                            <span class="info-list__label">
                                <i class="fas fa-calendar-plus"></i> Ngày tạo
                            </span>
                            <span class="info-list__value">
                                <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                            </span>
                        </li>
                        <li class="info-list__item">
                            <span class="info-list__label">
                                <i class="fas fa-clock"></i> Cập nhật
                            </span>
                            <span class="info-list__value">
                                <?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Toggle password visibility
    document.querySelectorAll('.form-group__toggle-password').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Validate password match
    document.querySelector('.user-form')?.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;

        if (password || confirm) {
            if (password !== confirm) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                document.getElementById('password_confirm').focus();
                return;
            }
            if (password.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                document.getElementById('password').focus();
            }
        }
    });

    // Avatar preview
    function previewAvatar(input) {
        const preview = document.getElementById('avatarPreview');
        const removeFlag = document.getElementById('removeAvatarFlag');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="avatar-upload__image" alt="Avatar preview">`;
            };

            reader.readAsDataURL(input.files[0]);

            // Reset remove flag when new image is selected
            if (removeFlag) {
                removeFlag.value = '0';
            }
        }
    }

    // Remove avatar
    function removeAvatar() {
        if (confirm('Bạn có chắc muốn xóa ảnh đại diện?')) {
            const preview = document.getElementById('avatarPreview');
            const removeFlag = document.getElementById('removeAvatarFlag');
            const input = document.getElementById('avatarInput');

            // Clear file input
            input.value = '';

            // Show placeholder
            preview.innerHTML = `<div class="avatar-upload__placeholder"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?></div>`;

            // Set remove flag
            if (removeFlag) {
                removeFlag.value = '1';
            }
        }
    }

    // Confirm delete
    function confirmDelete(userId) {
        if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/users/delete/${userId}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = 'csrf_token';
            csrf.value = '<?= $csrfToken ?? '' ?>';
            form.appendChild(csrf);

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>