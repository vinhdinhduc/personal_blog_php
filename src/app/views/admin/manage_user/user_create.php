<div class="page-header">
    <div class="page-header__info">
        <h1 class="page-header__title">Thêm người dùng mới</h1>
        <nav class="page-header__breadcrumb">
            <a href="<?= Router::url('/admin/dashboard') ?>" class="page-header__breadcrumb-link">Admin</a>
            <span class="page-header__breadcrumb-separator">/</span>
            <a href="<?= Router::url('/admin/users') ?>" class="page-header__breadcrumb-link">Người dùng</a>
            <span class="page-header__breadcrumb-separator">/</span>
            <span class="page-header__breadcrumb-current">Thêm mới</span>
        </nav>
    </div>
    <a href="<?= Router::url('/admin/users') ?>" class="btn btn--secondary">
        <i class="fas fa-arrow-left"></i>
        <span>Quay lại</span>
    </a>
</div>

<!-- Form Container -->
<form action="<?= Router::url('/admin/users/create') ?>" method="POST" enctype="multipart/form-data" class="user-form">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">

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
                            <input type="text" id="first_name" name="first_name" class="form-group__input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group__label" for="last_name">
                                Tên <span class="form-group__required">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" class="form-group__input" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-group__label" for="email">
                            Email <span class="form-group__required">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="form-group__input" required>
                    </div>
                </div>
            </div>

            <!-- Avatar Upload -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-camera form-card__icon"></i>
                    <h3 class="form-card__title">Ảnh đại diện</h3>
                </div>
                <div class="form-card__body">
                    <div class="avatar-upload">
                        <div class="avatar-upload__preview" id="avatarPreview">
                            <div class="avatar-upload__placeholder">
                                <i class="fas fa-user" style="font-size: 48px;"></i>
                            </div>
                        </div>
                        <div class="avatar-upload__input-wrapper">
                            <label for="avatarInput" class="btn btn--secondary btn--block">
                                <i class="fas fa-upload"></i>
                                <span>Chọn ảnh</span>
                            </label>
                            <input type="file" id="avatarInput" name="avatar" class="avatar-upload__input"
                                accept="image/*" onchange="previewAvatar(this)">
                            <small class="form-group__hint">
                                <i class="fas fa-info-circle"></i>
                                JPG, PNG hoặc GIF. Tối đa 2MB
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-key form-card__icon"></i>
                    <h3 class="form-card__title">Mật khẩu</h3>
                </div>
                <div class="form-card__body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-group__label" for="password">
                                Mật khẩu <span class="form-group__required">*</span>
                            </label>
                            <input type="password" id="password" name="password"
                                class="form-group__input" minlength="6" required>
                        </div>
                        <div class="form-group">
                            <label class="form-group__label" for="password_confirm">
                                Xác nhận mật khẩu <span class="form-group__required">*</span>
                            </label>
                            <input type="password" id="password_confirm" name="password_confirm"
                                class="form-group__input" minlength="6" required>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-card__footer form-card__footer--between">
                    <div></div>
                    <button type="submit" class="btn btn--primary">
                        <i class="fas fa-save"></i>
                        <span>Tạo người dùng</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="user-form__sidebar">
            <!-- Role Selection -->
            <div class="form-card">
                <div class="form-card__header">
                    <i class="fas fa-user-tag form-card__icon"></i>
                    <h3 class="form-card__title">Vai trò</h3>
                </div>
                <div class="form-card__body">
                    <div class="form-group">
                        <select name="role" id="role" class="form-group__select">
                            <option value="user" selected>User - Người dùng</option>
                            <option value="admin">Admin - Quản trị viên</option>
                        </select>
                    </div>
                </div>
            </div>


        </div>
    </div>
</form>

<script>
    function previewAvatar(input) {
        const preview = document.getElementById('avatarPreview');
        const file = input.files[0];

        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                window.toast.error('Kích thước file quá lớn', 'File không được vượt quá 2MB');
                input.value = '';
                return;
            }

            if (!file.type.match('image.*')) {
                window.toast.error('File không hợp lệ', 'Vui lòng chọn file ảnh (JPG, PNG, GIF)');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="avatar-upload__image" alt="Preview">`;
                window.toast.success('Đã chọn ảnh', 'Ảnh đại diện sẽ được tải lên khi lưu');
            };
            reader.readAsDataURL(file);
        }
    }

    // Validate password match
    document.querySelector('.user-form')?.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirm').value;

        if (password !== confirm) {
            e.preventDefault();
            window.toast.error('Mật khẩu không khớp', 'Vui lòng kiểm tra lại mật khẩu xác nhận');
            document.getElementById('password_confirm').focus();
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            window.toast.error('Mật khẩu quá ngắn', 'Mật khẩu phải có ít nhất 6 ký tự');
            document.getElementById('password').focus();
            return;
        }

        // Show loading toast
        window.toast.info('Đang xử lý...', 'Vui lòng đợi trong giây lát', 0);
    });
</script>

<style>
    /* ...existing avatar upload styles from user_edit.php... */
    .avatar-upload {
        display: flex;
        gap: 30px;
        align-items: center;
    }

    .avatar-upload__preview {
        flex-shrink: 0;
    }

    .avatar-upload__image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color);
    }

    .avatar-upload__placeholder {
        width: 150px;
        height: 150px;
        background: var(--light-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--secondary-color);
    }

    .avatar-upload__input {
        display: none;
    }
</style>