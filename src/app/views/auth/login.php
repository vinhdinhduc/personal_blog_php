<?php

/**
 * Login Page
 * File: src/app/views/auth/login.php
 */
$page_title = "Đăng nhập";
?>

<div class="auth">
    <div class="auth__container">
        <div class="auth__box">
            <!-- Header -->
            <div class="auth__header">
                <h1 class="auth__title">Chào mừng trở lại</h1>
                <p class="auth__subtitle">Đăng nhập để tiếp tục với MyBlog</p>
            </div>

            <!-- Form -->
            <form class="auth-form" method="POST" action="<?php echo Router::url('/login'); ?>" novalidate>
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken ?? ''; ?>">

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-group__label">
                        Email <span class="form-group__required">*</span>
                    </label>
                    <div class="form-group__input-wrapper">
                        <span class="form-group__icon"> <i class="fa-solid fa-envelope input-icon"></i></span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-group__input form-group__input--with-icon"
                            placeholder="name@example.com"
                            required
                            autocomplete="email"
                            autofocus>
                    </div>
                    <span class="form-group__error" id="email-error"></span>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-group__label">
                        Mật khẩu <span class="form-group__required">*</span>
                    </label>
                    <div class="form-group__input-wrapper">
                        <span class="form-group__icon"> <i class="fa-solid fa-lock input-icon"></i></span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-group__input form-group__input--with-icon"
                            placeholder="Nhập mật khẩu"
                            required
                            autocomplete="current-password">
                        <button type="button" class="form-group__toggle-password" onclick="togglePassword('password')">
                            <i class="fa-solid fa-eye toggle-password"></i>
                        </button>
                    </div>
                    <span class="form-group__error" id="password-error"></span>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="auth-form__options">
                    <label class="auth-form__checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="auth-form__checkbox-label">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="auth-form__link auth-form__link--forgot">Quên mật khẩu?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-form__submit">
                    <span class="auth-form__submit-text">Đăng nhập</span>
                    <span class="auth-form__submit-icon"><i class="fa-solid fa-right-to-bracket"></i>
                    </span>
                </button>

                <!-- Divider -->
                <div class="auth-form__divider">
                    <span class="auth-form__divider-text">hoặc</span>
                </div>

                <!-- Social Login (Optional) -->
                <div class="auth-form__social">
                    <button type="button" class="auth-form__social-btn auth-form__social-btn--google">
                        <span class="auth-form__social-icon">G</span>
                        <span class="auth-form__social-text">Đăng nhập với Google</span>
                    </button>
                </div>

                <!-- Footer -->
                <div class="auth-form__footer">
                    Chưa có tài khoản?
                    <a href="<?php echo Router::url('/register'); ?>" class="auth-form__link auth-form__link--primary">
                        Đăng ký ngay
                    </a>
                </div>
            </form>
        </div>

        <!-- Decorative Elements -->
        <div class="auth__decoration auth__decoration--top"></div>
        <div class="auth__decoration auth__decoration--bottom"></div>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.parentElement.querySelector('.form-group__toggle-password');

        if (input.type === 'password') {
            input.type = 'text';
            button.innerHTML = '<i class="fa-solid fa-eye-slash toggle-password"></i>';
        } else {
            input.type = 'password';
            button.innerHTML = '<i class="fa-solid fa-eye toggle-password"></i>';
        }
    }

    // Form validation
    document.querySelector('.auth-form').addEventListener('submit', function(e) {
        // Clear previous errors
        document.querySelectorAll('.form-group__error').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-group__input').forEach(el => el.classList.remove('form-group__input--error'));

        let isValid = true;

        // Validate email
        const email = document.getElementById('email');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            showError('email', 'Vui lòng nhập email');
            isValid = false;
        } else if (!emailPattern.test(email.value)) {
            showError('email', 'Email không hợp lệ');
            isValid = false;
        }

        // Validate password
        const password = document.getElementById('password');
        if (!password.value) {
            showError('password', 'Vui lòng nhập mật khẩu');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(inputId, message) {
        const input = document.getElementById(inputId);
        const errorSpan = document.getElementById(inputId + '-error');

        input.classList.add('form-group__input--error');
        errorSpan.textContent = message;
    }

    // Real-time validation
    document.querySelectorAll('.form-group__input').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() && this.classList.contains('form-group__input--error')) {
                this.classList.remove('form-group__input--error');
                const errorSpan = document.getElementById(this.id + '-error');
                if (errorSpan) errorSpan.textContent = '';
            }
        });
    });

    // Enter key to submit
    document.querySelectorAll('.form-group__input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.auth-form').submit();
            }
        });
    });
</script>