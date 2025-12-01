// Lấy checkbox "Chọn tất cả" và thiết lập sự kiện thay đổi
document.addEventListener("DOMContentLoaded", function () {
  const selectAllCheckbox = document.getElementById("selectAll");

  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      const checkboxes = document.querySelectorAll(".comment-checkbox");
      checkboxes.forEach((checkbox) => {
        checkbox.checked = this.checked;
      });
    });
  }

  // Cập nhật checkbox "Chọn tất cả" khi các checkbox cá nhân thay đổi
  const commentCheckboxes = document.querySelectorAll(".comment-checkbox");
  commentCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      const allChecked = Array.from(commentCheckboxes).every(
        (cb) => cb.checked
      );
      const someChecked = Array.from(commentCheckboxes).some(
        (cb) => cb.checked
      );

      if (selectAllCheckbox) {
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
      }
    });
  });
});

//Lấy danh sách ID bình luận đã chọn
function getSelectedCommentIds() {
  const checkboxes = document.querySelectorAll(".comment-checkbox:checked");
  return Array.from(checkboxes).map((cb) => cb.value);
}

//Handle phê duyệt
function bulkApprove(actionUrl) {
  const ids = getSelectedCommentIds();

  if (ids.length === 0) {
    alert("Vui lòng chọn ít nhất một bình luận!");
    return;
  }

  if (!confirm(`Phê duyệt ${ids.length} bình luận đã chọn?`)) {
    return;
  }
  // Tạo form và submit
  const form = document.createElement("form");
  form.method = "POST";
  form.action = actionUrl + "admin/comments/bulk-approve";

  // Thêm CSRF token
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  const csrfInput = document.createElement("input");
  csrfInput.type = "hidden";
  csrfInput.name = "csrf_token";
  csrfInput.value = csrfToken;
  form.appendChild(csrfInput);

  // Thêm ID bình luận
  ids.forEach((id) => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "comment_ids[]";
    input.value = id;
    form.appendChild(input);
  });

  document.body.appendChild(form);
  form.submit();
}

//Xoá nhiều bình luận
function bulkDelete(actionUrl) {
  const ids = getSelectedCommentIds();

  if (ids.length === 0) {
    alert("Vui lòng chọn ít nhất một bình luận!");
    return;
  }

  if (
    !confirm(
      `CẢNH BÁO: Xóa vĩnh viễn ${ids.length} bình luận đã chọn?\n\nHành động này không thể hoàn tác!`
    )
  ) {
    return;
  }

  // Tạo form và submit
  const form = document.createElement("form");
  form.method = "POST";
  form.action = actionUrl + "admin/comments/bulk-delete";

  // Thêm CSRF token
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  const csrfInput = document.createElement("input");
  csrfInput.type = "hidden";
  csrfInput.name = "csrf_token";
  csrfInput.value = csrfToken;
  form.appendChild(csrfInput);

  // Thêm ID bình luận
  ids.forEach((id) => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "comment_ids[]";
    input.value = id;
    form.appendChild(input);
  });

  document.body.appendChild(form);
  form.submit();
}

//Xác nhận xóa bình luận đơn
function confirmDelete(replyCount) {
  if (replyCount > 0) {
    return confirm(
      `CẢNH BÁO: Bình luận này có ${replyCount} câu trả lời.\n\nXóa sẽ xóa tất cả câu trả lời. Bạn có chắc chắn?`
    );
  }
  return confirm("Bạn có chắc muốn xóa bình luận này?");
}

document.addEventListener("click", function (e) {
  if (e.target.classList.contains("comment-modal")) {
    e.target.classList.remove("active");
  }
});

document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    const modals = document.querySelectorAll(".comment-modal.active");
    modals.forEach((modal) => modal.classList.remove("active"));
  }
});

// Phím tắt
document.addEventListener("keydown", function (e) {
  // Ctrl/Cmd + K: Focus vào ô tìm kiếm
  if ((e.ctrlKey || e.metaKey) && e.key === "k") {
    e.preventDefault();
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
      searchInput.focus();
    }
  }

  // Ctrl/Cmd + A: Chọn tất cả (khi không focus vào input)
  if (
    (e.ctrlKey || e.metaKey) &&
    e.key === "a" &&
    !["INPUT", "TEXTAREA"].includes(document.activeElement.tagName)
  ) {
    e.preventDefault();
    const selectAllCheckbox = document.getElementById("selectAll");
    if (selectAllCheckbox) {
      selectAllCheckbox.checked = !selectAllCheckbox.checked;
      selectAllCheckbox.dispatchEvent(new Event("change"));
    }
  }
});

// Tự động làm mới danh sách bình luận mỗi 60 giây
let autoRefreshInterval;

document.addEventListener("DOMContentLoaded", startAutoRefresh);
// Dừng auto-refresh khi rời khỏi trang
window.addEventListener("beforeunload", () => {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
  }
});
