/**
 * CATEGORIES.JS - Category Management JavaScript
 */

// Generate slug from name
function generateCategorySlug() {
  const name = document.getElementById("category_name").value;
  const slug = vietnameseToSlug(name);
  document.getElementById("category_slug").value = slug;
}

// Convert Vietnamese to slug
function vietnameseToSlug(text) {
  // Convert to lowercase
  text = text.toLowerCase();

  // Vietnamese character map
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

  // Replace Vietnamese characters
  for (let char in vietnameseMap) {
    text = text.replace(new RegExp(char, "g"), vietnameseMap[char]);
  }

  // Remove special characters
  text = text.replace(/[^a-z0-9\s-]/g, "");

  // Replace spaces and multiple hyphens with single hyphen
  text = text.replace(/[\s-]+/g, "-");

  // Trim hyphens from start and end
  return text.replace(/^-+|-+$/g, "");
}

// Preview icon
function previewIcon() {
  const iconClass =
    document.getElementById("category_icon").value || "fas fa-folder";
  document.getElementById(
    "iconPreview"
  ).innerHTML = `<i class="${iconClass}"></i>`;
}

// Edit category
function editCategory(category) {
  // Update form title
  document.getElementById("formTitle").innerHTML =
    '<i class="fas fa-edit"></i> Chỉnh sửa danh mục';
  document.getElementById("submitBtnText").textContent = "Cập nhật";

  // Update form action
  const form = document.getElementById("categoryForm");
  form.action = form.action.replace("/create", "/update/" + category.id);

  // Fill form fields
  document.getElementById("category_id").value = category.id;
  document.getElementById("category_name").value = category.name;
  document.getElementById("category_slug").value = category.slug;
  document.getElementById("category_description").value =
    category.description || "";
  document.getElementById("category_parent").value = category.parent_id || "";
  document.getElementById("category_icon").value =
    category.icon || "fas fa-folder";
  document.getElementById("category_sort_order").value =
    category.sort_order || 0;
  document.getElementById("category_meta_title").value =
    category.meta_title || "";
  document.getElementById("category_meta_description").value =
    category.meta_description || "";

  // Set color
  const colorInput = document.querySelector(
    `input[name="color"][value="${category.color}"]`
  );
  if (colorInput) {
    colorInput.checked = true;
  }

  // Set status
  const statusInput = document.querySelector(
    `input[name="status"][value="${category.status || "active"}"]`
  );
  if (statusInput) {
    statusInput.checked = true;
  }

  // Preview icon
  previewIcon();

  // Scroll to form
  document.getElementById("categoryForm").scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
}

// Reset form
function resetForm() {
  const form = document.getElementById("categoryForm");
  form.reset();

  // Reset form title and action
  document.getElementById("formTitle").innerHTML =
    '<i class="fas fa-plus-circle"></i> Thêm danh mục mới';
  document.getElementById("submitBtnText").textContent = "Thêm danh mục";
  form.action = form.action.replace(/\/update\/\d+/, "/create");
  document.getElementById("category_id").value = "";

  // Reset icon preview
  document.getElementById("iconPreview").innerHTML =
    '<i class="fas fa-folder"></i>';

  // Check default color
  const defaultColor = document.querySelector(
    'input[name="color"][value="#4e73df"]'
  );
  if (defaultColor) {
    defaultColor.checked = true;
  }
}

// Confirm delete
function confirmDelete(postCount) {
  if (postCount > 0) {
    return confirm(
      `CẢNH BÁO: Danh mục này có ${postCount} bài viết. Bạn có chắc muốn xóa?`
    );
  }
  return confirm("Bạn có chắc muốn xóa danh mục này?");
}

// Filter categories by status
function filterCategories(status) {
  const rows = document.querySelectorAll("#categoriesTable tbody tr");

  rows.forEach((row) => {
    if (!row.dataset.categoryId) {
      return; // Skip empty state row
    }

    if (!status || row.dataset.status === status) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Search categories
function searchCategories(query) {
  const rows = document.querySelectorAll("#categoriesTable tbody tr");
  const lowerQuery = query.toLowerCase();

  rows.forEach((row) => {
    if (!row.dataset.categoryId) {
      return; // Skip empty state row
    }

    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(lowerQuery) ? "" : "none";
  });
}

// Get selected category IDs
function getSelectedIds() {
  const checkboxes = document.querySelectorAll(".category-checkbox:checked");
  return Array.from(checkboxes).map((cb) => cb.value);
}

// Bulk activate
function bulkActivate() {
  const ids = getSelectedIds();

  if (ids.length === 0) {
    alert("Vui lòng chọn ít nhất một danh mục!");
    return;
  }

  if (confirm(`Kích hoạt ${ids.length} danh mục đã chọn?`)) {
    window.location.href = `/admin/categories/bulk-activate?ids=${ids.join(
      ","
    )}`;
  }
}

// Bulk deactivate
function bulkDeactivate() {
  const ids = getSelectedIds();

  if (ids.length === 0) {
    alert("Vui lòng chọn ít nhất một danh mục!");
    return;
  }

  if (confirm(`Tạm dừng ${ids.length} danh mục đã chọn?`)) {
    window.location.href = `/admin/categories/bulk-deactivate?ids=${ids.join(
      ","
    )}`;
  }
}

// Bulk delete
function bulkDelete() {
  const ids = getSelectedIds();

  if (ids.length === 0) {
    alert("Vui lòng chọn ít nhất một danh mục!");
    return;
  }

  if (
    confirm(
      `CẢNH BÁO: Xóa vĩnh viễn ${ids.length} danh mục đã chọn?\n\nLưu ý: Các danh mục có bài viết sẽ không bị xóa.`
    )
  ) {
    window.location.href = `/admin/categories/bulk-delete?ids=${ids.join(",")}`;
  }
}

// Select all checkboxes
document.addEventListener("DOMContentLoaded", function () {
  const selectAllCheckbox = document.getElementById("selectAll");

  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      const checkboxes = document.querySelectorAll(".category-checkbox");
      checkboxes.forEach((checkbox) => {
        checkbox.checked = this.checked;
      });
    });
  }

  // Update select all when individual checkboxes change
  const categoryCheckboxes = document.querySelectorAll(".category-checkbox");
  categoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const allChecked = Array.from(categoryCheckboxes).every(
        (cb) => cb.checked
      );
      const someChecked = Array.from(categoryCheckboxes).some(
        (cb) => cb.checked
      );

      if (selectAllCheckbox) {
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
      }
    });
  });
});

// Form validation
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("categoryForm");

  if (form) {
    form.addEventListener("submit", function (e) {
      const name = document.getElementById("category_name").value.trim();

      if (!name) {
        e.preventDefault();
        alert("Vui lòng nhập tên danh mục!");
        document.getElementById("category_name").focus();
        return false;
      }

      // Auto-generate slug if empty
      const slug = document.getElementById("category_slug").value.trim();
      if (!slug) {
        generateCategorySlug();
      }
    });
  }
});

// Keyboard shortcuts
document.addEventListener("keydown", function (e) {
  // Ctrl/Cmd + K: Focus search
  if ((e.ctrlKey || e.metaKey) && e.key === "k") {
    e.preventDefault();
    const searchInput = document.querySelector(
      '.category-table__search input[type="text"]'
    );
    if (searchInput) {
      searchInput.focus();
    }
  }

  // Ctrl/Cmd + N: Focus name input (new category)
  if ((e.ctrlKey || e.metaKey) && e.key === "n") {
    e.preventDefault();
    resetForm();
    document.getElementById("category_name").focus();
  }

  // Escape: Clear search
  if (e.key === "Escape") {
    const searchInput = document.querySelector(
      '.category-table__search input[type="text"]'
    );
    if (searchInput && searchInput.value) {
      searchInput.value = "";
      searchCategories("");
    }
  }
});

// Auto-save form data to sessionStorage (prevent data loss)
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("categoryForm");

  if (form) {
    // Load saved data
    const savedData = sessionStorage.getItem("categoryFormData");
    if (savedData) {
      try {
        const data = JSON.parse(savedData);
        if (data.name)
          document.getElementById("category_name").value = data.name;
        if (data.slug)
          document.getElementById("category_slug").value = data.slug;
        if (data.description)
          document.getElementById("category_description").value =
            data.description;
      } catch (e) {
        console.error("Error loading saved form data:", e);
      }
    }

    // Save data on input
    const inputs = form.querySelectorAll('input[type="text"], textarea');
    inputs.forEach((input) => {
      input.addEventListener("input", function () {
        const data = {
          name: document.getElementById("category_name").value,
          slug: document.getElementById("category_slug").value,
          description: document.getElementById("category_description").value,
        };
        sessionStorage.setItem("categoryFormData", JSON.stringify(data));
      });
    });

    // Clear saved data on successful submit
    form.addEventListener("submit", function () {
      sessionStorage.removeItem("categoryFormData");
    });
  }
});

// Tooltip functionality
document.addEventListener("DOMContentLoaded", function () {
  const tooltipElements = document.querySelectorAll("[data-tooltip]");

  tooltipElements.forEach((element) => {
    element.addEventListener("mouseenter", function (e) {
      const tooltipText = this.getAttribute("data-tooltip");
      const tooltip = document.createElement("div");
      tooltip.className = "tooltip-popup";
      tooltip.textContent = tooltipText;
      tooltip.style.cssText = `
                position: fixed;
                background: rgba(0, 0, 0, 0.9);
                color: #fff;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 13px;
                pointer-events: none;
                z-index: 10000;
                white-space: nowrap;
                animation: fadeIn 0.2s ease;
            `;

      document.body.appendChild(tooltip);

      const rect = this.getBoundingClientRect();
      tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + "px";
      tooltip.style.left =
        rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px";

      this._tooltip = tooltip;
    });

    element.addEventListener("mouseleave", function () {
      if (this._tooltip) {
        this._tooltip.remove();
        this._tooltip = null;
      }
    });
  });
});
