/**
 * Admin Panel JavaScript
 * File: public/js/admin.js
 * Version: 1.0.0
 *
 * Xử lý các tương tác trong admin panel
 */

// ========================================
// Initialize khi DOM loaded
// ========================================
document.addEventListener("DOMContentLoaded", function () {
  initSidebarToggle();
  initMobileMenu();
  initTooltips();
  initConfirmDialogs();
  initAutoHideAlerts();
  initTableRowHover();
  initSearchAutocomplete();
});

// ========================================
// Sidebar Toggle
// ========================================
function initSidebarToggle() {
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("sidebarToggle");

  if (!sidebar || !toggleBtn) return;

  // Load saved state từ localStorage
  const savedState = localStorage.getItem("sidebarCollapsed");
  if (savedState === "true") {
    sidebar.classList.add("collapsed");
  }

  // Toggle khi click
  toggleBtn.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");

    // Save state
    const isCollapsed = sidebar.classList.contains("collapsed");
    localStorage.setItem("sidebarCollapsed", isCollapsed);
  });
}

// ========================================
// Mobile Menu
// ========================================
function initMobileMenu() {
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("sidebarToggle");

  if (window.innerWidth <= 768) {
    // Close sidebar when clicking outside
    document.addEventListener("click", function (e) {
      if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove("show");
      }
    });

    // Toggle sidebar on mobile
    if (toggleBtn) {
      toggleBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        sidebar.classList.toggle("show");
      });
    }
  }
}

// ========================================
// Bootstrap Tooltips
// ========================================
function initTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );

  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// ========================================
// Confirm Delete Dialogs
// ========================================
function initConfirmDialogs() {
  // Confirm delete buttons
  document.querySelectorAll("[data-confirm]").forEach((button) => {
    button.addEventListener("click", function (e) {
      const message =
        this.getAttribute("data-confirm") ||
        "Are you sure you want to delete this item?";

      if (!confirm(message)) {
        e.preventDefault();
        return false;
      }
    });
  });

  // Delete buttons với icon trash
  document
    .querySelectorAll('.btn-danger[title*="Delete"]')
    .forEach((button) => {
      button.addEventListener("click", function (e) {
        if (!confirm("Bạn có chắc muốn xóa item này?")) {
          e.preventDefault();
          return false;
        }
      });
    });
}

// ========================================
// Auto Hide Alerts
// ========================================
function initAutoHideAlerts() {
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");

  alerts.forEach((alert) => {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });
}

// ========================================
// Table Row Hover Effect
// ========================================
function initTableRowHover() {
  document.querySelectorAll(".table tbody tr").forEach((row) => {
    row.addEventListener("mouseenter", function () {
      this.style.transform = "scale(1.01)";
    });

    row.addEventListener("mouseleave", function () {
      this.style.transform = "scale(1)";
    });
  });
}

// ========================================
// Search Autocomplete
// ========================================
function initSearchAutocomplete() {
  const searchInput = document.getElementById("topbarSearch");

  if (!searchInput) return;

  let debounceTimer;

  searchInput.addEventListener("input", function () {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
      const query = this.value.trim();

      if (query.length >= 2) {
        // TODO: Implement AJAX search
        console.log("Searching for:", query);
      }
    }, 300);
  });
}

// ========================================
// Select All Checkboxes
// ========================================
function initSelectAllCheckboxes() {
  const selectAllCheckbox = document.querySelector(
    'thead input[type="checkbox"]'
  );

  if (!selectAllCheckbox) return;

  selectAllCheckbox.addEventListener("change", function () {
    const checkboxes = document.querySelectorAll(
      'tbody input[type="checkbox"]'
    );
    checkboxes.forEach((checkbox) => {
      checkbox.checked = this.checked;
    });
  });
}

// ========================================
// Bulk Actions
// ========================================
function initBulkActions() {
  const bulkActionForm = document.getElementById("bulkActionForm");

  if (!bulkActionForm) return;

  bulkActionForm.addEventListener("submit", function (e) {
    const selectedItems = document.querySelectorAll(
      'tbody input[type="checkbox"]:checked'
    );

    if (selectedItems.length === 0) {
      e.preventDefault();
      alert("Vui lòng chọn ít nhất một item");
      return false;
    }

    const action = document.querySelector('[name="bulk_action"]').value;

    if (action === "delete") {
      if (!confirm(`Xác nhận xóa ${selectedItems.length} items?`)) {
        e.preventDefault();
        return false;
      }
    }
  });
}

// ========================================
// Image Preview for Upload
// ========================================
function previewImage(input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      const preview = document.getElementById(previewId);
      if (preview) {
        preview.src = e.target.result;
        preview.style.display = "block";
      }
    };

    reader.readAsDataURL(input.files[0]);
  }
}

// ========================================
// Auto-generate Slug from Title
// ========================================
function generateSlug(text) {
  return text
    .toLowerCase()
    .replace(/[^\w\s-]/g, "")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-")
    .trim();
}

function initSlugGenerator() {
  const titleInput = document.querySelector('input[name="title"]');
  const slugInput = document.querySelector('input[name="slug"]');

  if (!titleInput || !slugInput) return;

  let manualEdit = false;

  titleInput.addEventListener("input", function () {
    if (!manualEdit) {
      slugInput.value = generateSlug(this.value);
    }
  });

  slugInput.addEventListener("input", function () {
    manualEdit = true;
  });
}

// ========================================
// Rich Text Editor (TinyMCE)
// ========================================
function initRichTextEditor() {
  if (typeof tinymce !== "undefined") {
    tinymce.init({
      selector: ".rich-text-editor",
      height: 400,
      menubar: false,
      plugins: [
        "advlist",
        "autolink",
        "lists",
        "link",
        "image",
        "charmap",
        "preview",
        "anchor",
        "searchreplace",
        "visualblocks",
        "code",
        "fullscreen",
        "insertdatetime",
        "media",
        "table",
        "help",
        "wordcount",
      ],
      toolbar:
        "undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code",
      content_style:
        'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
    });
  }
}

// ========================================
// AJAX Delete Function
// ========================================
async function deleteItem(url, confirmMessage = "Bạn có chắc muốn xóa?") {
  if (!confirm(confirmMessage)) {
    return false;
  }

  try {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          ?.content,
      },
    });

    const data = await response.json();

    if (data.success) {
      showToast("Xóa thành công", "success");
      setTimeout(() => {
        location.reload();
      }, 1000);
    } else {
      showToast(data.message || "Xóa thất bại", "error");
    }
  } catch (error) {
    console.error("Error:", error);
    showToast("Có lỗi xảy ra", "error");
  }
}

// ========================================
// Toast Notifications
// ========================================
function showToast(message, type = "info") {
  const toast = document.createElement("div");
  toast.className = `toast-notification toast-${type}`;
  toast.innerHTML = `
        <i class="fas fa-${getToastIcon(type)}"></i>
        <span>${message}</span>
    `;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.classList.add("show");
  }, 100);

  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => {
      toast.remove();
    }, 300);
  }, 3000);
}

function getToastIcon(type) {
  const icons = {
    success: "check-circle",
    error: "exclamation-circle",
    warning: "exclamation-triangle",
    info: "info-circle",
  };
  return icons[type] || "info-circle";
}

// ========================================
// Loading Overlay
// ========================================
function showLoading() {
  const overlay = document.createElement("div");
  overlay.id = "loadingOverlay";
  overlay.className = "loading-overlay";
  overlay.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
  document.body.appendChild(overlay);
}

function hideLoading() {
  const overlay = document.getElementById("loadingOverlay");
  if (overlay) {
    overlay.remove();
  }
}

// ========================================
// Export Functions
// ========================================
window.adminJS = {
  deleteItem,
  showToast,
  showLoading,
  hideLoading,
  generateSlug,
  previewImage,
};
