<?php
 
?>
<div class="auth">
    <div class="auth__container">
        <div class="auth__box">
            <!-- Header -->
            <div class="auth__header">
                <h1 class="auth__title">Tạo tài khoản</h1>
                <p class="auth__subtitle">Tham gia cộng đồng MyBlog ngay hôm nay</p>
            </div>

            <!-- Form -->
            <form class="auth-form" method="POST" action="<?php echo Router::url('/register'); ?>" novalidate>
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken ?? ''; ?>">

                <!-- Name Fields -->
                <div class="auth-form__row">
                    <div class="form-group">
                        <label for="fName" class="form-group__label">
                            Họ <span class="form-group__required">*</span>
                        </label>
                        <input
                            type="text"
                            id="fName"
                            name="fName"
                            class="form-group__input"
                            placeholder="Nguyễn"
                            required
                            autocomplete="given-name">
                        <span class="form-group__error" id="fName-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="lName" class="form-group__label">
                            Tên <span class="form-group__required">*</span>
                        </label>
                        <input
                            type="text"
                            id="lName"
                            name="lName"
                            class="form-group__input"
                            placeholder="Văn A"
                            required
                            autocomplete="family-name">
                        <span class="form-group__error" id="lName-error"></span>
                    </div>
                </div>

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
                            autocomplete="email">
                    </div>
                    <span class="form-group__error" id="email-error"></span>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-group__label">
                        Mật khẩu <span class="form-group__required">*</span>
                    </label>
                    <div class="form-group__input-wrapper">
                        <span class="form-group__icon"><i class="fa-solid fa-lock input-icon"></i></span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-group__input form-group__input--with-icon"
                            placeholder="Tối thiểu 6 ký tự"
                            required
                            minlength="6"
                            autocomplete="new-password">
                        <button type="button" class="form-group__toggle-password" onclick="togglePassword('password')">
                            <i class="fa-solid fa-eye toggle-password"></i>
                        </button>
                    </div>
                    <div class="form-group__hint">
                        <i class="fa-solid fa-info-circle info-icon"></i> Mật khẩu phải có ít nhất 6 ký tự
                    </div>
                    <span class="form-group__error" id="password-error"></span>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirm" class="form-group__label">
                        Xác nhận mật khẩu <span class="form-group__required">*</span>
                    </label>
                    <div class="form-group__input-wrapper">
                        <span class="form-group__icon"><i class="fa-solid fa-lock input-icon"></i></span>
                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            class="form-group__input form-group__input--with-icon"
                            placeholder="Nhập lại mật khẩu"
                            required
                            autocomplete="new-password">
                        <button type="button" class="form-group__toggle-password" onclick="togglePassword('password_confirm')">
                            <i class="fa-solid fa-eye toggle-password"></i>
                        </button>
                    </div>
                    <span class="form-group__error" id="password_confirm-error"></span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-form__submit">
                    <span class="auth-form__submit-text">Đăng ký</span>
                    <span class="auth-form__submit-icon"><i class="fa-solid fa-user-plus"></i></span>
                </button>

                <!-- Terms -->
                <p class="auth-form__terms">
                    Bằng việc đăng ký, bạn đồng ý với
                    <a href="#" class="auth-form__link">Điều khoản dịch vụ</a> và
                    <a href="#" class="auth-form__link">Chính sách bảo mật</a> của chúng tôi.
                </p>

                <!-- Footer -->
                <div class="auth-form__footer">
                    Đã có tài khoản?
                    <a href="<?php echo Router::url('/login'); ?>" class="auth-form__link auth-form__link--primary">
                        Đăng nhập ngay
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

        // Validate first name
        const fName = document.getElementById('fName');
        if (!fName.value.trim()) {
            showError('fName', 'Vui lòng nhập họ');
            isValid = false;
        }

        // Validate last name
        const lName = document.getElementById('lName');
        if (!lName.value.trim()) {
            showError('lName', 'Vui lòng nhập tên');
            isValid = false;
        }

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
</script>