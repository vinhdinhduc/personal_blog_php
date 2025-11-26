/**
 * Profile Page JavaScript
 */

document.addEventListener("DOMContentLoaded", function () {
  // ====================================
  // Avatar Upload Handler
  // ====================================
  const avatarInput = document.getElementById("avatarInput");
  const avatarPreview = document.getElementById("avatarPreview");
  const avatarWrapper = document.querySelector(".profile-avatar__wrapper");

  if (avatarInput) {
    avatarInput.addEventListener("change", function (e) {
      const file = e.target.files[0];

      if (!file) return;

      // Validate file type
      const allowedTypes = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
      ];
      if (!allowedTypes.includes(file.type)) {
        alert("Vui lòng chọn file ảnh (JPG, PNG, GIF)");
        return;
      }

      // Validate file size (max 5MB)
      const maxSize = 5 * 1024 * 1024; // 5MB in bytes
      if (file.size > maxSize) {
        alert("Kích thước ảnh không được vượt quá 5MB");
        return;
      }

      // Preview image
      const reader = new FileReader();
      reader.onload = function (event) {
        avatarPreview.src = event.target.result;
      };
      reader.readAsDataURL(file);

      // Upload avatar
      uploadAvatar(file);
    });
  }

  function uploadAvatar(file) {
    const formData = new FormData();
    formData.append("avatar", file);
    formData.append(
      "csrf_token",
      document.querySelector('input[name="csrf_token"]').value
    );

    // Add loading state
    avatarWrapper.classList.add("is-loading");

    // Show loading overlay
    const loadingOverlay = document.createElement("div");
    loadingOverlay.className = "profile-avatar__loading";
    loadingOverlay.innerHTML = '<div class="spinner"></div>';
    avatarWrapper.appendChild(loadingOverlay);

    fetch("/profile/update-avatar", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Update avatar in header if exists
          const headerAvatar = document.querySelector(".header__avatar img");
          if (headerAvatar) {
            headerAvatar.src = data.avatar_url;
          }

          // Show success message
          showToast("success", data.message);
        } else {
          // Revert preview on error
          avatarPreview.src =
            avatarPreview.dataset.original ||
            "/public/images/default-avatar.png";
          showToast("error", data.message);
        }
      })
      .catch((error) => {
        console.error("Upload error:", error);
        avatarPreview.src =
          avatarPreview.dataset.original || "/public/images/default-avatar.png";
        showToast("error", "Có lỗi xảy ra khi tải ảnh lên");
      })
      .finally(() => {
        // Remove loading state
        avatarWrapper.classList.remove("is-loading");
        if (loadingOverlay) {
          loadingOverlay.remove();
        }
      });
  }

  // ====================================
  // Password Toggle Handler
  // ====================================
  const passwordToggles = document.querySelectorAll(
    ".profile-form__toggle-password"
  );

  passwordToggles.forEach((toggle) => {
    toggle.addEventListener("click", function () {
      const targetId = this.dataset.target;
      const input = document.getElementById(targetId);

      if (!input) return;

      if (input.type === "password") {
        input.type = "text";
        this.innerHTML = `
                    <svg class="profile-form__eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                `;
      } else {
        input.type = "password";
        this.innerHTML = `
                    <svg class="profile-form__eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                `;
      }
    });
  });

  // ====================================
  // Password Confirmation Validation
  // ====================================
  const newPasswordInput = document.getElementById("new_password");
  const confirmPasswordInput = document.getElementById("confirm_password");

  if (newPasswordInput && confirmPasswordInput) {
    confirmPasswordInput.addEventListener("input", function () {
      if (this.value !== newPasswordInput.value) {
        this.setCustomValidity("Mật khẩu xác nhận không khớp");
      } else {
        this.setCustomValidity("");
      }
    });

    newPasswordInput.addEventListener("input", function () {
      if (
        confirmPasswordInput.value &&
        confirmPasswordInput.value !== this.value
      ) {
        confirmPasswordInput.setCustomValidity("Mật khẩu xác nhận không khớp");
      } else {
        confirmPasswordInput.setCustomValidity("");
      }
    });
  }

  // ====================================
  // Form Validation
  // ====================================
  const forms = document.querySelectorAll(".profile-form");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();

        // Find first invalid input and focus it
        const firstInvalid = form.querySelector(":invalid");
        if (firstInvalid) {
          firstInvalid.focus();

          // Show validation message
          const message =
            firstInvalid.validationMessage || "Vui lòng điền đầy đủ thông tin";
          showToast("error", message);
        }
      }

      form.classList.add("was-validated");
    });
  });

  // ====================================
  // Toast Notification Helper
  // ====================================
  function showToast(type, message) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll(".profile-toast");
    existingToasts.forEach((toast) => toast.remove());

    // Create toast element
    const toast = document.createElement("div");
    toast.className = `profile-toast profile-toast--${type}`;
    toast.innerHTML = `
            <div class="profile-toast__icon">
                ${
                  type === "success"
                    ? `
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                `
                    : `
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                `
                }
            </div>
            <div class="profile-toast__message">${message}</div>
            <button class="profile-toast__close">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

    // Add to page
    document.body.appendChild(toast);

    // Close button
    toast
      .querySelector(".profile-toast__close")
      .addEventListener("click", () => {
        toast.classList.add("profile-toast--hide");
        setTimeout(() => toast.remove(), 300);
      });

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (toast.parentElement) {
        toast.classList.add("profile-toast--hide");
        setTimeout(() => toast.remove(), 300);
      }
    }, 5000);
  }

  // ====================================
  // Input Character Counter (Optional)
  // ====================================
  const inputs = document.querySelectorAll(".profile-form__input[maxlength]");

  inputs.forEach((input) => {
    const maxLength = input.getAttribute("maxlength");
    if (maxLength) {
      const counter = document.createElement("span");
      counter.className = "profile-form__counter";
      counter.textContent = `0/${maxLength}`;

      input.parentElement.appendChild(counter);

      input.addEventListener("input", function () {
        counter.textContent = `${this.value.length}/${maxLength}`;
      });
    }
  });

  // ====================================
  // Save original avatar URL for revert
  // ====================================
  if (avatarPreview) {
    avatarPreview.dataset.original = avatarPreview.src;
  }
});

// ====================================
// Toast Styles (add to CSS or here)
// ====================================
const toastStyles = document.createElement("style");
toastStyles.textContent = `
    .profile-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 1rem;
        z-index: 9999;
        animation: slideInRight 0.3s ease-out;
        max-width: 400px;
    }

    .profile-toast--success {
        border-left: 4px solid #48bb78;
    }

    .profile-toast--error {
        border-left: 4px solid #f56565;
    }

    .profile-toast__icon {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
    }

    .profile-toast--success .profile-toast__icon {
        color: #48bb78;
    }

    .profile-toast--error .profile-toast__icon {
        color: #f56565;
    }

    .profile-toast__message {
        flex: 1;
        font-size: 0.95rem;
        color: #2d3748;
    }

    .profile-toast__close {
        width: 20px;
        height: 20px;
        background: none;
        border: none;
        color: #718096;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .profile-toast__close:hover {
        color: #2d3748;
    }

    .profile-toast--hide {
        animation: slideOutRight 0.3s ease-out forwards;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .profile-avatar__loading {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top-color: #667eea;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .profile-toast {
            top: 10px;
            right: 10px;
            left: 10px;
            max-width: none;
        }
    }
`;
document.head.appendChild(toastStyles);
