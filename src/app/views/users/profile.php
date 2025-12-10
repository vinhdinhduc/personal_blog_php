<div class="profile">
    <div class="profile__container">
        <div class="profile__header">
            <h1 class="profile__title">Thông tin cá nhân</h1>
            <p class="profile__subtitle">Quản lý thông tin tài khoản của bạn</p>
        </div>

        <div class="profile__content">
            <!-- Avatar Section -->
            <div class="profile__card profile__card--avatar">
                <div class="profile-avatar">
                    <!--  FORM UPLOAD ĐƠN GIẢN -->
                    <form action="<?php echo Router::url('/profile/update-avatar'); ?>"
                        method="POST"
                        enctype="multipart/form-data"
                        id="avatarForm">

                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                        <div class="profile-avatar__wrapper">
                            <img
                                src="<?= !empty($user['avatar']) ? '/personal-blog/public/' . ($user['avatar']) : '/public/images/default-avatar.png' ?>"
                                alt="Avatar"
                                class="profile-avatar__image"
                                id="avatarPreview">

                            <div class="profile-avatar__overlay">
                                <label for="avatarInput" class="profile-avatar__btn">
                                    <svg class="profile-avatar__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Đổi ảnh</span>
                                </label>
                                <input
                                    type="file"
                                    name="avatar"
                                    id="avatarInput"
                                    accept="image/*"
                                    class="profile-avatar__input"
                                    hidden>
                            </div>
                        </div>
                    </form>

                    <div class="profile-avatar__info">
                        <h3 class="profile-avatar__name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                        <p class="profile-avatar__email"><?= htmlspecialchars($user['email']) ?></p>
                        <span class="profile-avatar__role profile-avatar__role--<?= $user['role'] ?>">
                            <?= $user['role'] === 'admin' ? 'Quản trị viên' : 'Người dùng' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Personal Info Section -->
            <div class="profile__card">
                <div class="profile-section">
                    <div class="profile-section__header">
                        <h2 class="profile-section__title">Thông tin cá nhân</h2>
                        <p class="profile-section__desc">Cập nhật thông tin cơ bản của bạn</p>
                    </div>

                    <form action=<?php echo Router::url('/profile/update-info'); ?> method="POST" class="profile-form">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                        <div class="profile-form__row">
                            <div class="profile-form__group">
                                <label for="first_name" class="profile-form__label">
                                    Họ <span class="profile-form__required">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    class="profile-form__input"
                                    value="<?= htmlspecialchars($user['first_name']) ?>"
                                    required>
                            </div>

                            <div class="profile-form__group">
                                <label for="last_name" class="profile-form__label">
                                    Tên <span class="profile-form__required">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    class="profile-form__input"
                                    value="<?= htmlspecialchars($user['last_name']) ?>"
                                    required>
                            </div>
                        </div>

                        <div class="profile-form__group">
                            <label for="email" class="profile-form__label">
                                Email <span class="profile-form__required">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="profile-form__input"
                                value="<?= htmlspecialchars($user['email']) ?>"
                                required>
                        </div>

                        <div class="profile-form__actions">
                            <button type="submit" class="profile-btn profile-btn--primary">
                                <svg class="profile-btn__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="profile__card">
                <div class="profile-section">
                    <div class="profile-section__header">
                        <h2 class="profile-section__title">Đổi mật khẩu</h2>
                        <p class="profile-section__desc">Cập nhật mật khẩu để bảo mật tài khoản</p>
                    </div>

                    <form action="<?php echo Router::url('/profile/change-password'); ?>" method="POST" class="profile-form">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                        <div class="profile-form__group">
                            <label for="current_password" class="profile-form__label">
                                Mật khẩu hiện tại <span class="profile-form__required">*</span>
                            </label>
                            <div class="profile-form__input-wrapper">
                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="profile-form__input"
                                    required>
                                <button type="button" class="profile-form__toggle-password" data-target="current_password">
                                    <svg class="profile-form__eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="profile-form__group">
                            <label for="new_password" class="profile-form__label">
                                Mật khẩu mới <span class="profile-form__required">*</span>
                            </label>
                            <div class="profile-form__input-wrapper">
                                <input
                                    type="password"
                                    id="new_password"
                                    name="new_password"
                                    class="profile-form__input"
                                    minlength="6"
                                    required>
                                <button type="button" class="profile-form__toggle-password" data-target="new_password">
                                    <svg class="profile-form__eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <span class="profile-form__hint">Tối thiểu 6 ký tự</span>
                        </div>

                        <div class="profile-form__group">
                            <label for="confirm_password" class="profile-form__label">
                                Xác nhận mật khẩu mới <span class="profile-form__required">*</span>
                            </label>
                            <div class="profile-form__input-wrapper">
                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    class="profile-form__input"
                                    minlength="6"
                                    required>
                                <button type="button" class="profile-form__toggle-password" data-target="confirm_password">
                                    <svg class="profile-form__eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="profile-form__actions">
                            <button type="submit" class="profile-btn profile-btn--primary">
                                <svg class="profile-btn__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>