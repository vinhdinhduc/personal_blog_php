/**
 * ADMIN TAGS MANAGEMENT - JavaScript
 * Xử lý CRUD operations cho tags
 */

// ========================================
// GLOBAL STATE
// ========================================

let selectedTags = new Set();
let currentDeleteTagId = null;

// ========================================
// DELETE TAG FUNCTIONALITY
// ========================================

/**
 * Mở modal xác nhận xóa tag
 * @param {number} tagId - ID của tag cần xóa
 * @param {string} tagName - Tên tag để hiển thị
 * @param {number} postCount - Số bài viết đang sử dụng tag
 */
function deleteTag(tagId, tagName, postCount) {
  currentDeleteTagId = tagId;

  // Cập nhật nội dung modal
  const deleteMessage = document.getElementById("deleteMessage");
  deleteMessage.innerHTML = `Bạn có chắc chắn muốn xóa tag <strong>"${escapeHtml(
    tagName
  )}"</strong>?`;

  // Hiển thị cảnh báo nếu tag đang được sử dụng
  const deleteWarning = document.getElementById("deleteWarning");
  if (postCount > 0) {
    deleteWarning.style.display = "flex";
    // Cập nhật số lượng bài viết
    const warningText = deleteWarning.querySelector("span");
    if (!warningText) {
      const span = document.createElement("span");
      span.textContent = `Tag này đang được sử dụng trong ${postCount} bài viết và sẽ bị gỡ khỏi tất cả bài viết!`;
      deleteWarning.appendChild(span);
    } else {
      warningText.textContent = `Tag này đang được sử dụng trong ${postCount} bài viết và sẽ bị gỡ khỏi tất cả bài viết!`;
    }
  } else {
    deleteWarning.style.display = "none";
  }

  // Cập nhật form action
  const deleteForm = document.getElementById("deleteForm");
  deleteForm.action = `${baseUrl}admin/tags/delete/${tagId}`;

  // Hiển thị modal
  const modal = document.getElementById("deleteModal");
  modal.style.display = "flex";

  // Focus vào nút Hủy
  setTimeout(() => {
    const cancelBtn = modal.querySelector(".tags__btn--secondary");
    if (cancelBtn) cancelBtn.focus();
  }, 100);
}

/**
 * Đóng modal xác nhận xóa
 */
function closeDeleteModal() {
  const modal = document.getElementById("deleteModal");
  modal.style.display = "none";
  currentDeleteTagId = null;
}

/**
 * Xử lý submit form xóa
 */
function setupDeleteForm() {
  const deleteForm = document.getElementById("deleteForm");
  if (!deleteForm) return;

  deleteForm.addEventListener("submit", function (e) {
    // Thêm loading state vào button
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';
    }
    // Form sẽ submit bình thường
  });
}

// ========================================
// BULK DELETE FUNCTIONALITY
// ========================================

/**
 * Xử lý thay đổi checkbox
 */
function handleCheckboxChange() {
  const checkboxes = document.querySelectorAll(".tag-checkbox");
  const selectAllCheckbox = document.getElementById("selectAll");
  const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");
  const selectedCountSpan = document.getElementById("selectedCount");

  // Cập nhật set các tag đã chọn
  selectedTags.clear();
  checkboxes.forEach((checkbox) => {
    if (checkbox.checked) {
      selectedTags.add(parseInt(checkbox.value));
    }
  });

  // Cập nhật UI
  const selectedCount = selectedTags.size;
  selectedCountSpan.textContent = selectedCount;
  bulkDeleteBtn.disabled = selectedCount === 0;

  // Cập nhật trạng thái checkbox "Select All"
  if (selectAllCheckbox) {
    const allChecked =
      checkboxes.length > 0 && Array.from(checkboxes).every((cb) => cb.checked);
    const someChecked = selectedCount > 0;

    selectAllCheckbox.checked = allChecked;
    selectAllCheckbox.indeterminate = someChecked && !allChecked;
  }
}

/**
 * Xử lý checkbox "Select All"
 */
function handleSelectAll() {
  const selectAllCheckbox = document.getElementById("selectAll");
  if (!selectAllCheckbox) return;

  selectAllCheckbox.addEventListener("change", function () {
    const checkboxes = document.querySelectorAll(".tag-checkbox");
    checkboxes.forEach((checkbox) => {
      checkbox.checked = this.checked;
    });
    handleCheckboxChange();
  });
}

/**
 * Xử lý nút xóa hàng loạt
 */
function handleBulkDelete() {
  const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");
  if (!bulkDeleteBtn) return;

  bulkDeleteBtn.addEventListener("click", function () {
    if (selectedTags.size === 0) return;

    const confirmed = confirm(
      `Bạn có chắc chắn muốn xóa ${selectedTags.size} tag đã chọn?\n\n` +
        `Lưu ý: Các tag đang được sử dụng sẽ bị gỡ khỏi tất cả bài viết!`
    );

    if (confirmed) {
      // Tạo form và submit
      const form = document.createElement("form");
      form.method = "POST";
      form.action = `${baseUrl}admin/tags/bulk-delete`;

      // Thêm CSRF token
      const csrfInput = document.createElement("input");
      csrfInput.type = "hidden";
      csrfInput.name = "csrf_token";
      csrfInput.value = document.querySelector(
        'input[name="csrf_token"]'
      ).value;
      form.appendChild(csrfInput);

      // Thêm các tag ID đã chọn
      selectedTags.forEach((tagId) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "tag_ids[]";
        input.value = tagId;
        form.appendChild(input);
      });

      // Thêm force delete flag
      const forceInput = document.createElement("input");
      forceInput.type = "hidden";
      forceInput.name = "force";
      forceInput.value = "1";
      form.appendChild(forceInput);

      document.body.appendChild(form);

      this.disabled = true;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';

      form.submit();
    }
  });
}

/**
 * Setup tất cả event listeners cho checkboxes
 */
function setupCheckboxes() {
  const checkboxes = document.querySelectorAll(".tag-checkbox");
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", handleCheckboxChange);
  });

  handleSelectAll();
  handleBulkDelete();
}

// ========================================
// SEARCH FUNCTIONALITY
// ========================================

/**
 * Xử lý form tìm kiếm
 */
function setupSearch() {
  const searchForm = document.querySelector(".tags__search-form");
  if (!searchForm) return;

  const searchInput = searchForm.querySelector(".tags__search-input");

  // Xử lý nút Clear
  const clearBtn = searchForm.querySelector(".tags__btn--clear");
  if (clearBtn) {
    clearBtn.addEventListener("click", function (e) {
      e.preventDefault();
      searchInput.value = "";
      searchForm.submit();
    });
  }

  // Auto-submit khi nhấn Enter
  searchInput.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      searchForm.submit();
    }
  });

  // Focus vào search input với Ctrl/Cmd + K
  document.addEventListener("keydown", function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === "k") {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    }
  });
}

// ========================================
// MODAL FUNCTIONALITY
// ========================================

/**
 * Đóng modal khi click bên ngoài
 */
function setupModalClickOutside() {
  const modal = document.getElementById("deleteModal");
  if (!modal) return;

  modal.addEventListener("click", function (e) {
    if (e.target === modal) {
      closeDeleteModal();
    }
  });
}

/**
 * Đóng modal khi nhấn ESC
 */
function setupModalEscapeKey() {
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const modal = document.getElementById("deleteModal");
      if (modal && modal.style.display === "flex") {
        closeDeleteModal();
      }
    }
  });
}

// ========================================
// UTILITY FUNCTIONS
// ========================================

/**
 * Escape HTML để tránh XSS
 * @param {string} text - Text cần escape
 * @returns {string} - Text đã được escape
 */
function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return String(text).replace(/[&<>"']/g, (m) => map[m]);
}

/**
 * Hiển thị toast notification
 * @param {string} message - Message cần hiển thị
 * @param {string} type - Loại toast (success, error, info, warning)
 */
function showToast(message, type = "info") {
  // Kiểm tra nếu có hệ thống toast (từ toast.js)
  if (typeof window.showToast === "function") {
    window.showToast(message, type);
  } else {
    // Fallback sang alert
    alert(message);
  }
}

// ========================================
// TABLE INTERACTIONS
// ========================================

/**
 * Thêm hover effects cho table rows
 */
function setupTableRowHover() {
  const rows = document.querySelectorAll(".tags__table-row");
  rows.forEach((row) => {
    // Thêm transition
    row.style.transition = "all 0.2s ease";
  });
}

/**
 * Click vào row để chọn
 */
function setupRowClick() {
  const rows = document.querySelectorAll(".tags__table-row");
  rows.forEach((row) => {
    row.addEventListener("click", function (e) {
      // Không trigger nếu click vào action buttons hoặc checkbox
      if (
        e.target.closest(".tags__actions") ||
        e.target.closest(".tags__checkbox")
      ) {
        return;
      }

      // Toggle checkbox
      const checkbox = this.querySelector(".tag-checkbox");
      if (checkbox) {
        checkbox.checked = !checkbox.checked;
        handleCheckboxChange();
      }
    });
  });
}

// ========================================
// KEYBOARD SHORTCUTS
// ========================================

/**
 * Setup keyboard shortcuts
 */
function setupKeyboardShortcuts() {
  document.addEventListener("keydown", function (e) {
    // Ctrl/Cmd + A: Select all (khi không trong input)
    if (
      (e.ctrlKey || e.metaKey) &&
      e.key === "a" &&
      !e.target.matches("input, textarea")
    ) {
      e.preventDefault();
      const selectAllCheckbox = document.getElementById("selectAll");
      if (selectAllCheckbox) {
        selectAllCheckbox.checked = !selectAllCheckbox.checked;
        selectAllCheckbox.dispatchEvent(new Event("change"));
      }
    }

    // Delete key: Trigger bulk delete nếu có items được chọn
    if (
      e.key === "Delete" &&
      selectedTags.size > 0 &&
      !e.target.matches("input, textarea")
    ) {
      const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");
      if (bulkDeleteBtn && !bulkDeleteBtn.disabled) {
        bulkDeleteBtn.click();
      }
    }
  });
}

// ========================================
// ANIMATION HELPERS
// ========================================

/**
 * Animate khi xóa row
 * @param {HTMLElement} row - Row element cần animate
 */
function animateRowDeletion(row) {
  row.style.transition = "all 0.3s ease";
  row.style.opacity = "0";
  row.style.transform = "translateX(-20px)";

  setTimeout(() => {
    row.remove();

    // Kiểm tra nếu table trống
    const tbody = document.querySelector(".tags__table-body");
    if (tbody && tbody.children.length === 0) {
      location.reload(); // Reload để hiển thị empty state
    }
  }, 300);
}

// ========================================
// INITIALIZATION
// ========================================

/**
 * Khởi tạo tất cả chức năng khi DOM ready
 */
document.addEventListener("DOMContentLoaded", function () {
  console.log("🏷️ Admin Tags JS initialized");

  // Setup core functionality
  setupDeleteForm();
  setupCheckboxes();
  setupSearch();

  // Setup modal
  setupModalClickOutside();
  setupModalEscapeKey();

  // Setup table interactions
  setupTableRowHover();
  setupRowClick();

  // Setup keyboard shortcuts
  setupKeyboardShortcuts();

  // Log trạng thái
  console.log(" Tags management ready");
});

// ========================================
// EXPORT FUNCTIONS CHO INLINE USAGE
// ========================================

// Làm cho các function có thể gọi từ inline onclick handlers
window.deleteTag = deleteTag;
window.closeDeleteModal = closeDeleteModal;

console.log(" admin-tags.js loaded successfully");
