document.addEventListener("DOMContentLoaded", function () {
  const avatarInput = document.getElementById("avatarInput");
  const avatarPreview = document.getElementById("avatarPreview");
  const avatarForm = document.getElementById("avatarForm");

  if (avatarInput) {
    avatarInput.addEventListener("change", function (e) {
      const file = e.target.files[0];

      if (!file) return;

      // Validate
      const allowedTypes = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
      ];
      if (!allowedTypes.includes(file.type)) {
        alert("Vui lòng chọn file ảnh (JPG, PNG, GIF)");
        this.value = "";
        return;
      }

      // Check size (5MB)
      if (file.size > 5 * 1024 * 1024) {
        alert("Kích thước ảnh không được vượt quá 5MB");
        this.value = "";
        return;
      }

      // Preview
      const reader = new FileReader();
      reader.onload = function (event) {
        avatarPreview.src = event.target.result;
      };
      reader.readAsDataURL(file);

      //  AUTO SUBMIT FORM
      avatarForm.submit();
    });
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
