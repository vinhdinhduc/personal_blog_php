<?php
$page_title = "Đặt lại mật khẩu";
?>

<div class="auth">
    <div class="auth__container">
        <div class="auth__box">
            <!-- Header -->
            <div class="auth__header">
                <div class="auth__icon-wrapper auth__icon-wrapper--success">
                    <i class="fa-solid fa-key auth__icon"></i>
                </div>
                <h1 class="auth__title">Đặt lại mật khẩu</h1>
                <p class="auth__subtitle">
                    Tạo mật khẩu mới cho tài khoản <strong><?php echo htmlspecialchars($email ?? ''); ?></strong>
                </p>
            </div>

            <!-- Form -->
            <form class="auth-form" method="POST" action="<?php echo Router::url('/reset-password'); ?>" novalidate>
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken ?? ''; ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">

                <!-- New Password -->
                <div class="form-group">
                    <label for="password" class="form-group__label">
                        Mật khẩu mới <span class="form-group__required">*</span>
                    </label>
                    <div class="form-group__input-wrapper">
                        <span class="form-group__icon">
                            <i class="fa-solid fa-lock input-icon"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-group__input form-group__input--with-icon"
                            placeholder="Nhập mật khẩu mới"
                            required
                            minlength="6"
                            autocomplete="new-password"
                            autofocus>
                        <button type="button" class="form-group__toggle-password" onclick="togglePassword('password')">
                            <i class="fa-solid fa-eye toggle-password"></i>
                        </button>
                    </div>
                    <span class="form-group__error" id="password-error"></span>
                    <span class="form-group__hint">
                        <i class="fa-solid fa-circle-info"></i>
                        Mật khẩu phải có ít nhất 6 ký tự
                    </span>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirm" class="form-group__label">
                        Xác nhận mật khẩu <span class="form-group__required">*</span>
                    </label>
                    <div class="form-group__input-wrapper">
                        <span class="form-group__icon">
                            <i class="fa-solid fa-lock input-icon"></i>
                        </span>
                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="form-group__input form-group__input--with-icon"
                            placeholder="Nhập lại mật khẩu mới"
                            required
                            minlength="6"
                            autocomplete="new-password">
                        <button type="button" class="form-group__toggle-password" onclick="togglePassword('password_confirm')">
                            <i class="fa-solid fa-eye toggle-password"></i>
                        </button>
                    </div>
                    <span class="form-group__error" id="password_confirm-error"></span>
                </div>

                <!-- Độ mạnh mật khẩu -->
                <div class="password-strength">
                    <div class="password-strength__label">Độ mạnh mật khẩu:</div>
                    <div class="password-strength__bar">
                        <div class="password-strength__fill" id="strengthBar"></div>
                    </div>
                    <div class="password-strength__text" id="strengthText">Chưa đánh giá</div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-form__submit">
                    <span class="auth-form__submit-text">Đặt lại mật khẩu</span>
                    <span class="auth-form__submit-icon">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </button>

                <!-- Security Tips -->
                <div class="auth__tips">
                    <div class="auth__tips-title">
                        <i class="fa-solid fa-shield-halved"></i>
                        Gợi ý tạo mật khẩu an toàn:
                    </div>
                    <ul class="auth__tips-list">
                        <li>Sử dụng ít nhất 8 ký tự</li>
                        <li>Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
                        <li>Không sử dụng thông tin cá nhân dễ đoán</li>
                        <li>Không tái sử dụng mật khẩu cũ</li>
                    </ul>
                </div>
            </form>
        </div>

        <!-- Các phần tử trang trí -->
        <div class="auth__decoration auth__decoration--top"></div>
        <div class="auth__decoration auth__decoration--bottom"></div>
    </div>
</div>

<style>
    /* Thêm css */
    .auth__icon-wrapper--success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .form-group__hint {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #718096;
    }

    .form-group__hint i {
        margin-right: 0.25rem;
        color: #667eea;
    }

    /* Password Strength Indicator */
    .password-strength {
        margin: 1.5rem 0;
        padding: 1rem;
        background: #f7fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .password-strength__label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .password-strength__bar {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .password-strength__fill {
        height: 100%;
        width: 0;
        transition: all 0.3s ease;
        border-radius: 4px;
    }

    .password-strength__fill--weak {
        width: 33%;
        background: #f56565;
    }

    .password-strength__fill--medium {
        width: 66%;
        background: #ed8936;
    }

    .password-strength__fill--strong {
        width: 100%;
        background: #48bb78;
    }

    .password-strength__text {
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
    }

    .password-strength__text--weak {
        color: #f56565;
    }

    .password-strength__text--medium {
        color: #ed8936;
    }

    .password-strength__text--strong {
        color: #48bb78;
    }

    /* Security Tips */
    .auth__tips {
        margin-top: 1.5rem;
        padding: 1.25rem;
        background: #f0f4ff;
        border-radius: 10px;
        border-left: 4px solid #667eea;
    }

    .auth__tips-title {
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .auth__tips-title i {
        color: #667eea;
        font-size: 1.1rem;
    }

    .auth__tips-list {
        margin: 0;
        padding-left: 1.5rem;
        color: #4a5568;
        line-height: 1.8;
    }

    .auth__tips-list li {
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    @media (max-width: 480px) {
        .auth__tips {
            padding: 1rem;
        }

        .auth__tips-list {
            font-size: 0.875rem;
        }
    }
</style>

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

    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);

        // Remove all classes
        strengthBar.className = 'password-strength__fill';
        strengthText.className = 'password-strength__text';

        if (password.length === 0) {
            strengthText.textContent = 'Chưa đánh giá';
            return;
        }

        if (strength < 40) {
            strengthBar.classList.add('password-strength__fill--weak');
            strengthText.classList.add('password-strength__text--weak');
            strengthText.textContent = 'Yếu';
        } else if (strength < 70) {
            strengthBar.classList.add('password-strength__fill--medium');
            strengthText.classList.add('password-strength__text--medium');
            strengthText.textContent = 'Trung bình';
        } else {
            strengthBar.classList.add('password-strength__fill--strong');
            strengthText.classList.add('password-strength__text--strong');
            strengthText.textContent = 'Mạnh';
        }
    });

    function calculatePasswordStrength(password) {
        let strength = 0;

        // Length
        if (password.length >= 6) strength += 20;
        if (password.length >= 8) strength += 20;
        if (password.length >= 12) strength += 10;

        // Complexity
        if (/[a-z]/.test(password)) strength += 10;
        if (/[A-Z]/.test(password)) strength += 15;
        if (/[0-9]/.test(password)) strength += 15;
        if (/[^a-zA-Z0-9]/.test(password)) strength += 20;

        return Math.min(strength, 100);
    }

    // Form validation
    document.querySelector('.auth-form').addEventListener('submit', function(e) {
        // Clear previous errors
        document.querySelectorAll('.form-group__error').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-group__input').forEach(el => el.classList.remove('form-group__input--error'));

        let isValid = true;

        // Validate password
        const password = document.getElementById('password');
        if (!password.value) {
            showError('password', 'Vui lòng nhập mật khẩu mới');
            isValid = false;
        } else if (password.value.length < 6) {
            showError('password', 'Mật khẩu phải có ít nhất 6 ký tự');
            isValid = false;
        }

        // Validate password confirmation
        const passwordConfirm = document.getElementById('password_confirm');
        if (!passwordConfirm.value) {
            showError('password_confirm', 'Vui lòng xác nhận mật khẩu');
            isValid = false;
        } else if (password.value !== passwordConfirm.value) {
            showError('password_confirm', 'Mật khẩu xác nhận không khớp');
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

    // Real-time validation for password confirmation
    document.getElementById('password_confirm').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        if (this.value && password !== this.value) {
            showError('password_confirm', 'Mật khẩu xác nhận không khớp');
        } else if (this.classList.contains('form-group__input--error')) {
            this.classList.remove('form-group__input--error');
            document.getElementById('password_confirm-error').textContent = '';
        }
    });
</script>