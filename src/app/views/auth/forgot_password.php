<?php
$page_title = "Quên mật khẩu";
?>

<div class="auth">
    <div class="auth__container">
        <div class="auth__box">
            <!-- Header -->
            <div class="auth__header">
                <div class="auth__icon-wrapper">
                    <i class="fa-solid fa-lock auth__icon"></i>
                </div>
                <h1 class="auth__title">Quên mật khẩu?</h1>
                <p class="auth__subtitle">
                    Nhập email của bạn và chúng tôi sẽ gửi link để đặt lại mật khẩu
                </p>
            </div>

            <!-- Form -->
            <form class="auth-form" method="POST" action="<?php echo Router::url('/forgot-password'); ?>" novalidate>
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken ?? ''; ?>">

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-group__label">
                        Email <span class="form-group__required">*</span>
                    </label>
                    <div class="form-group__input-wrapper">
                        <span class="form-group__icon">
                            <i class="fa-solid fa-envelope input-icon"></i>
                        </span>
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
                    <span class="form-group__hint">
                        <i class="fa-solid fa-circle-info"></i>
                        Nhập email bạn đã dùng để đăng ký tài khoản
                    </span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="auth-form__submit">
                    <span class="auth-form__submit-text">Gửi link đặt lại mật khẩu</span>
                    <span class="auth-form__submit-icon">
                        <i class="fa-solid fa-paper-plane"></i>
                    </span>
                </button>

                <!-- Back to Login -->
                <div class="auth-form__footer auth-form__footer--center">
                    <a href="<?php echo Router::url('/login'); ?>" class="auth-form__link auth-form__link--back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Quay lại đăng nhập
                    </a>
                </div>
            </form>

            <!-- Info Box -->
            <div class="auth__info-box">
                <div class="auth__info-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div class="auth__info-content">
                    <h3 class="auth__info-title">Làm thế nào để đặt lại mật khẩu?</h3>
                    <ol class="auth__info-list">
                        <li>Nhập email bạn đã đăng ký</li>
                        <li>Kiểm tra hộp thư email của bạn</li>
                        <li>Nhấn vào link trong email (có hiệu lực trong 1 giờ)</li>
                        <li>Tạo mật khẩu mới</li>
                    </ol>
                    <p class="auth__info-note">
                        <strong>Lưu ý:</strong> Nếu không thấy email, hãy kiểm tra thư mục Spam.
                    </p>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="auth__decoration auth__decoration--top"></div>
        <div class="auth__decoration auth__decoration--bottom"></div>
    </div>
</div>



<script>
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
    document.getElementById('email').addEventListener('input', function() {
        if (this.value.trim() && this.classList.contains('form-group__input--error')) {
            this.classList.remove('form-group__input--error');
            document.getElementById('email-error').textContent = '';
        }
    });
</script>