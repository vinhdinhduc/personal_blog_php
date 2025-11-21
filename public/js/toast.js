/**
 * Toast Notification System
 * File: public/assets/js/toast.js
 * Version: 1.0.0
 */

class Toast {
  constructor(options = {}) {
    this.options = {
      position: "top-right", // top-left, top-right, top-center, bottom-left, bottom-right, bottom-center
      duration: 5000, // milliseconds
      closeable: true,
      ...options,
    };

    this.container = null;
    this.init();
  }

  /**
   * Initialize toast container
   */
  init() {
    // Check if container already exists
    this.container = document.querySelector(".toast-container");

    if (!this.container) {
      this.container = document.createElement("div");
      this.container.className = `toast-container toast-container--${this.options.position}`;
      document.body.appendChild(this.container);
    }
  }

  /**
   * Show toast notification
   * @param {string} type - success, error, warning, info
   * @param {string} title - Toast title
   * @param {string} message - Toast message (optional)
   * @param {number} duration - Custom duration (optional)
   */
  show(type, title, message = "", duration = null) {
    const toastDuration = duration || this.options.duration;

    // Create toast element
    const toast = this.createToast(type, title, message, toastDuration);

    // Add to container
    this.container.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
      toast.classList.add("toast--show");
    }, 10);

    // Auto remove after duration
    if (toastDuration > 0) {
      setTimeout(() => {
        this.remove(toast);
      }, toastDuration);
    }

    return toast;
  }

  /**
   * Create toast element
   */
  createToast(type, title, message, duration) {
    const toast = document.createElement("div");
    toast.className = `toast toast--${type}`;

    // Get icon based on type
    const icon = this.getIcon(type);

    // Build toast HTML
    let html = `
            <div class="toast__icon">${icon}</div>
            <div class="toast__content">
                <p class="toast__title">${this.escapeHtml(title)}</p>
                ${
                  message
                    ? `<p class="toast__message">${this.escapeHtml(
                        message
                      )}</p>`
                    : ""
                }
            </div>
        `;

    // Add close button if closeable
    if (this.options.closeable) {
      html += `<button class="toast__close" aria-label="Close">×</button>`;
    }

    // Add progress bar if duration is set
    if (duration > 0) {
      html += `<div class="toast__progress" style="animation-duration: ${duration}ms"></div>`;
    }

    toast.innerHTML = html;

    // Add close event listener
    if (this.options.closeable) {
      const closeBtn = toast.querySelector(".toast__close");
      closeBtn.addEventListener("click", () => this.remove(toast));
    }

    return toast;
  }

  /**
   * Get icon for toast type
   */
  getIcon(type) {
    const icons = {
      success: "✓",
      error: "✕",
      warning: "⚠",
      info: "i",
    };
    return icons[type] || "i";
  }

  /**
   * Remove toast
   */
  remove(toast) {
    toast.classList.remove("toast--show");
    toast.classList.add("toast--hide");

    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  }

  /**
   * Escape HTML to prevent XSS
   */
  escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  /**
   * Convenience methods
   */
  success(title, message, duration) {
    return this.show("success", title, message, duration);
  }

  error(title, message, duration) {
    return this.show("error", title, message, duration);
  }

  warning(title, message, duration) {
    return this.show("warning", title, message, duration);
  }

  info(title, message, duration) {
    return this.show("info", title, message, duration);
  }

  /**
   * Clear all toasts
   */
  clearAll() {
    const toasts = this.container.querySelectorAll(".toast");
    toasts.forEach((toast) => this.remove(toast));
  }
}

// Create global instance
window.toast = new Toast();

// Export for module usage
if (typeof module !== "undefined" && module.exports) {
  module.exports = Toast;
}
