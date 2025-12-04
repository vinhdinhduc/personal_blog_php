/**
 * ADMIN-CATEGORY.JS - Category Management
 * Version 2.0 - Separate Add & Edit Forms
 */

// ============================================
// VIETNAMESE TO SLUG CONVERTER (SHARED)
// ============================================

function vietnameseToSlug(text) {
  if (!text) return "";

  text = text.toLowerCase().trim();

  const vietnameseMap = {
    à: "a",
    á: "a",
    ả: "a",
    ã: "a",
    ạ: "a",
    ă: "a",
    ằ: "a",
    ắ: "a",
    ẳ: "a",
    ẵ: "a",
    ặ: "a",
    â: "a",
    ầ: "a",
    ấ: "a",
    ẩ: "a",
    ẫ: "a",
    ậ: "a",
    è: "e",
    é: "e",
    ẻ: "e",
    ẽ: "e",
    ẹ: "e",
    ê: "e",
    ề: "e",
    ế: "e",
    ể: "e",
    ễ: "e",
    ệ: "e",
    ì: "i",
    í: "i",
    ỉ: "i",
    ĩ: "i",
    ị: "i",
    ò: "o",
    ó: "o",
    ỏ: "o",
    õ: "o",
    ọ: "o",
    ô: "o",
    ồ: "o",
    ố: "o",
    ổ: "o",
    ỗ: "o",
    ộ: "o",
    ơ: "o",
    ờ: "o",
    ớ: "o",
    ở: "o",
    ỡ: "o",
    ợ: "o",
    ù: "u",
    ú: "u",
    ủ: "u",
    ũ: "u",
    ụ: "u",
    ư: "u",
    ừ: "u",
    ứ: "u",
    ử: "u",
    ữ: "u",
    ự: "u",
    ỳ: "y",
    ý: "y",
    ỷ: "y",
    ỹ: "y",
    ỵ: "y",
    đ: "d",
  };

  for (let char in vietnameseMap) {
    text = text.replace(new RegExp(char, "g"), vietnameseMap[char]);
  }

  text = text.replace(/[^a-z0-9\s-]/g, "");
  text = text.replace(/[\s-]+/g, "-");
  return text.replace(/^-+|-+$/g, "");
}

// ============================================
// DELETE CONFIRMATION
// ============================================

function confirmDelete(categoryName, postCount) {
  if (postCount > 0) {
    alert(
      `CẢNH BÁO: Không thể xóa danh mục "${categoryName}"!\n\n` +
        `Danh mục này có ${postCount} bài viết.\n` +
        `Vui lòng di chuyển hoặc xóa tất cả bài viết trước khi xóa danh mục.`
    );
    return false;
  }

  return confirm(
    `Bạn có chắc muốn xóa danh mục "${categoryName}"?\n\n` +
      `Hành động này không thể hoàn tác!`
  );
}

// ============================================
// SEARCH FUNCTION
// ============================================

function searchCategories(query) {
  const rows = document.querySelectorAll("#categoriesTable tbody tr");
  const lowerQuery = query.toLowerCase().trim();
  let visibleCount = 0;

  rows.forEach((row) => {
    if (
      row.classList.contains("table__row--empty") ||
      !row.dataset.categoryId
    ) {
      return;
    }

    const text = row.textContent.toLowerCase();
    const isMatch = text.includes(lowerQuery);

    row.style.display = isMatch ? "" : "none";
    if (isMatch) visibleCount++;
  });

  if (visibleCount === 0 && lowerQuery) {
    showNoResultsMessage();
  } else {
    hideNoResultsMessage();
  }
}

function showNoResultsMessage() {
  const tbody = document.querySelector("#categoriesTable tbody");
  if (tbody.querySelector(".no-results-row")) return;

  const tr = document.createElement("tr");
  tr.className = "no-results-row";
  tr.innerHTML = `
        <td colspan="6" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-search" style="font-size: 48px; color: #e3e6f0; display: block; margin-bottom: 15px;"></i>
            <p style="margin: 0; font-size: 16px; color: #858796; font-weight: 600;">Không tìm thấy kết quả</p>
            <p style="margin: 5px 0 0 0; font-size: 14px; color: #858796;">Thử tìm kiếm với từ khóa khác</p>
        </td>
    `;
  tbody.appendChild(tr);
}

function hideNoResultsMessage() {
  const msg = document.querySelector(".no-results-row");
  if (msg) msg.remove();
}

// ============================================
// BULK ACTIONS
// ============================================

function updateBulkActions() {
  const checkboxes = document.querySelectorAll(".category-checkbox:checked");
  const count = checkboxes.length;
  const countSpan = document.getElementById("selected_count");
  const deleteBtn = document.getElementById("bulk_delete_btn");

  if (countSpan) countSpan.textContent = count;
  if (deleteBtn) deleteBtn.disabled = count === 0;
}

function getSelectedIds() {
  const checkboxes = document.querySelectorAll(".category-checkbox:checked");
  return Array.from(checkboxes).map((cb) => cb.value);
}

function bulkDelete() {
  const ids = getSelectedIds();

  if (ids.length === 0) {
    alert("Vui lòng chọn ít nhất một danh mục để xóa!");
    return;
  }

  if (
    !confirm(
      `Bạn có chắc muốn xóa ${ids.length} danh mục đã chọn?\n\n` +
        `LƯU Ý: Các danh mục có bài viết sẽ không bị xóa.\n` +
        `Hành động này không thể hoàn tác!`
    )
  ) {
    return;
  }

  // Create and submit a form for the first selected category
  // In a real scenario, you'd want to handle multiple deletes server-side
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  const form = document.createElement("form");
  form.method = "POST";
  form.action = window.location.pathname; // Will be handled by backend

  const csrfInput = document.createElement("input");
  csrfInput.type = "hidden";
  csrfInput.name = "csrf_token";
  csrfInput.value = csrfToken;

  const idsInput = document.createElement("input");
  idsInput.type = "hidden";
  idsInput.name = "bulk_delete_ids";
  idsInput.value = ids.join(",");

  form.appendChild(csrfInput);
  form.appendChild(idsInput);
  document.body.appendChild(form);

  // For now, just delete the first one
  window.location.href = `/admin/categories/delete/${ids[0]}`;
}

// ============================================
// EVENT LISTENERS
// ============================================

document.addEventListener("DOMContentLoaded", function () {
  // Select All Checkbox
  const selectAllCheckbox = document.getElementById("selectAll");
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      const checkboxes = document.querySelectorAll(".category-checkbox");
      checkboxes.forEach((checkbox) => {
        checkbox.checked = this.checked;
      });
      updateBulkActions();
    });
  }

  // Individual checkboxes
  const categoryCheckboxes = document.querySelectorAll(".category-checkbox");
  categoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      if (selectAllCheckbox) {
        const allChecked = Array.from(categoryCheckboxes).every(
          (cb) => cb.checked
        );
        const someChecked = Array.from(categoryCheckboxes).some(
          (cb) => cb.checked
        );

        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
      }
      updateBulkActions();
    });
  });

  // Keyboard shortcuts
  document.addEventListener("keydown", function (e) {
    // Don't trigger if typing in input
    if (e.target.tagName === "INPUT" || e.target.tagName === "TEXTAREA") {
      return;
    }

    // Ctrl/Cmd + K: Focus search
    if ((e.ctrlKey || e.metaKey) && e.key === "k") {
      e.preventDefault();
      document.getElementById("search_input")?.focus();
    }

    // Ctrl/Cmd + N: New category
    if ((e.ctrlKey || e.metaKey) && e.key === "n") {
      e.preventDefault();
      openAddModal();
    }
  });

  // Initialize bulk actions
  updateBulkActions();
});

// ============================================
// UTILITY FUNCTIONS
// ============================================

function showNotification(message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `notification notification--${type}`;
  notification.innerHTML = `
        <i class="fas fa-${
          type === "success" ? "check-circle" : "exclamation-circle"
        }"></i>
        <span>${message}</span>
    `;
  notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === "success" ? "#1cc88a" : "#e74a3b"};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        font-weight: 600;
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        animation: slideInUp 0.3s ease;
    `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.animation = "slideOutDown 0.3s ease";
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Copy slug to clipboard
function copySlug(slug) {
  navigator.clipboard
    .writeText(slug)
    .then(() => {
      showNotification("Đã copy slug vào clipboard!", "success");
    })
    .catch((err) => {
      console.error("Error copying:", err);
      showNotification("Không thể copy slug", "error");
    });
}

// Export to CSV
function exportCategories() {
  const categories = [];
  const rows = document.querySelectorAll("#categoriesTable tbody tr");

  rows.forEach((row) => {
    if (row.dataset.categoryId) {
      const cells = row.querySelectorAll("td");
      categories.push({
        id: cells[1].textContent.trim(),
        name: row.querySelector(".category-info__name").textContent.trim(),
        slug: row.querySelector(".table__code").textContent.trim(),
        posts: row.querySelector(".category-count span").textContent.trim(),
      });
    }
  });

  let csv = "\uFEFF"; // BOM for UTF-8
  csv += "ID,Tên danh mục,Slug,Số bài viết\n";
  categories.forEach((cat) => {
    csv += `${cat.id},"${cat.name}",${cat.slug},${cat.posts}\n`;
  });

  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  const timestamp = new Date().toISOString().slice(0, 10);
  link.href = URL.createObjectURL(blob);
  link.download = `categories_${timestamp}.csv`;
  link.click();

  showNotification("Đã xuất dữ liệu thành công!", "success");
}

// Add animation styles
const style = document.createElement("style");
style.textContent = `
    @keyframes slideInUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutDown {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(100px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
