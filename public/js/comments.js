/**
 * Comment System JavaScript
 * Xử lý AJAX cho comments, replies, và moderation
 */

document.addEventListener("DOMContentLoaded", function () {
  initCommentSystem();
});

function initCommentSystem() {
  // Submit comment form (AJAX)
  const commentForms = document.querySelectorAll(".comment-form");
  commentForms.forEach((form) => {
    form.addEventListener("submit", handleCommentSubmit);
  });

  // Reply buttons
  const replyButtons = document.querySelectorAll(".reply-btn");
  replyButtons.forEach((btn) => {
    btn.addEventListener("click", handleReplyClick);
  });

  // Cancel reply buttons
  const cancelReplyButtons = document.querySelectorAll(".cancel-reply-btn");
  cancelReplyButtons.forEach((btn) => {
    btn.addEventListener("click", handleCancelReply);
  });

  // Edit comment buttons
  const editButtons = document.querySelectorAll(".edit-comment-btn");
  editButtons.forEach((btn) => {
    btn.addEventListener("click", handleEditComment);
  });

  // Delete comment buttons
  const deleteButtons = document.querySelectorAll(".delete-comment-btn");
  deleteButtons.forEach((btn) => {
    btn.addEventListener("click", handleDeleteComment);
  });

  // Approve comment buttons
  const approveButtons = document.querySelectorAll(".approve-comment-btn");
  approveButtons.forEach((btn) => {
    btn.addEventListener("click", handleApproveComment);
  });
}

/**
 * Handle comment form submit (AJAX)
 */
async function handleCommentSubmit(e) {
  e.preventDefault();

  const form = e.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  const formData = new FormData(form);

  // Disable submit button
  submitBtn.disabled = true;
  submitBtn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-1"></span> Đang gửi...';

  try {
    const response = await fetch("/comment/create", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      showAlert("success", data.message);

      // Reset form
      form.reset();

      // Hide reply form if it's a reply
      const parentId = formData.get("parent_id");
      if (parentId) {
        const replyForm = document.getElementById(`reply-form-${parentId}`);
        if (replyForm) {
          replyForm.style.display = "none";
        }
      }

      // Reload page to show new comment (hoặc append comment nếu không cần approve)
      if (!data.needs_approval) {
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        // Comment cần approve - chỉ reload sau 2s
        setTimeout(() => {
          location.reload();
        }, 2000);
      }
    } else {
      showAlert("danger", data.message);
    }
  } catch (error) {
    console.error("Error:", error);
    showAlert("danger", "Có lỗi xảy ra. Vui lòng thử lại.");
  } finally {
    // Re-enable submit button
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-comment"></i> Gửi bình luận';
  }
}

/**
 * Handle reply button click
 */
function handleReplyClick(e) {
  e.preventDefault();
  const commentId = e.currentTarget.dataset.commentId;
  const replyForm = document.getElementById(`reply-form-${commentId}`);

  if (replyForm) {
    // Hide all other reply forms
    document.querySelectorAll(".reply-form").forEach((form) => {
      form.style.display = "none";
    });

    // Show this reply form
    replyForm.style.display = "block";

    // Focus on textarea
    const textarea = replyForm.querySelector("textarea");
    if (textarea) {
      textarea.focus();
    }
  }
}

/**
 * Handle cancel reply
 */
function handleCancelReply(e) {
  e.preventDefault();
  const replyForm = e.currentTarget.closest(".reply-form");
  if (replyForm) {
    replyForm.style.display = "none";
    replyForm.querySelector("textarea").value = "";
  }
}

/**
 * Handle edit comment
 */
async function handleEditComment(e) {
  e.preventDefault();
  const commentId = e.currentTarget.dataset.commentId;
  const commentDiv = document.querySelector(`#comment-${commentId}`);
  const contentDiv = commentDiv.querySelector(".comment-content");
  const currentContent = contentDiv.textContent.trim();

  // Create edit form
  const editForm = document.createElement("div");
  editForm.className = "edit-comment-form";
  editForm.innerHTML = `
        <textarea class="form-control mb-2" rows="3">${currentContent}</textarea>
        <button class="btn btn-sm btn-primary save-edit-btn">Lưu</button>
        <button class="btn btn-sm btn-secondary cancel-edit-btn">Hủy</button>
    `;

  // Replace content with edit form
  contentDiv.style.display = "none";
  contentDiv.parentNode.insertBefore(editForm, contentDiv.nextSibling);

  // Handle save
  editForm
    .querySelector(".save-edit-btn")
    .addEventListener("click", async () => {
      const newContent = editForm.querySelector("textarea").value.trim();

      if (!newContent) {
        showAlert("danger", "Nội dung không được trống");
        return;
      }

      try {
        const formData = new FormData();
        formData.append("content", newContent);
        formData.append(
          "csrf_token",
          document.querySelector('input[name="csrf_token"]').value
        );

        const response = await fetch(`/comment/${commentId}/update`, {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          showAlert("success", data.message);
          contentDiv.innerHTML = nl2br(escapeHtml(newContent));
          contentDiv.style.display = "block";
          editForm.remove();
        } else {
          showAlert("danger", data.message);
        }
      } catch (error) {
        console.error("Error:", error);
        showAlert("danger", "Có lỗi xảy ra");
      }
    });

  // Handle cancel
  editForm.querySelector(".cancel-edit-btn").addEventListener("click", () => {
    contentDiv.style.display = "block";
    editForm.remove();
  });
}

/**
 * Handle delete comment
 */
async function handleDeleteComment(e) {
  e.preventDefault();
  const commentId = e.currentTarget.dataset.commentId;

  if (!confirm("Bạn có chắc muốn xóa bình luận này?")) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append(
      "csrf_token",
      document.querySelector('input[name="csrf_token"]').value
    );

    const response = await fetch(`/comment/${commentId}/delete`, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      showAlert("success", data.message);

      // Remove comment from DOM
      const commentDiv = document.querySelector(`#comment-${commentId}`);
      if (commentDiv) {
        commentDiv.style.opacity = "0";
        setTimeout(() => {
          commentDiv.remove();
        }, 300);
      }
    } else {
      if (data.confirm_required) {
        if (confirm(data.message + " Tiếp tục?")) {
          // User confirmed, delete anyway (implement if needed)
          showAlert("info", "Chức năng xóa cascade chưa implement");
        }
      } else {
        showAlert("danger", data.message);
      }
    }
  } catch (error) {
    console.error("Error:", error);
    showAlert("danger", "Có lỗi xảy ra");
  }
}

/**
 * Handle approve comment
 */
async function handleApproveComment(e) {
  e.preventDefault();
  const commentId = e.currentTarget.dataset.commentId;

  try {
    const formData = new FormData();
    formData.append(
      "csrf_token",
      document.querySelector('input[name="csrf_token"]').value
    );

    const response = await fetch(`/comment/${commentId}/approve`, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      showAlert("success", data.message);

      // Remove pending badge
      const commentDiv = document.querySelector(`#comment-${commentId}`);
      const pendingBadge = commentDiv.querySelector(".badge.bg-warning");
      if (pendingBadge) {
        pendingBadge.remove();
      }
      commentDiv.classList.remove("comment-pending");

      // Remove approve button
      e.currentTarget.remove();
    } else {
      showAlert("danger", data.message);
    }
  } catch (error) {
    console.error("Error:", error);
    showAlert("danger", "Có lỗi xảy ra");
  }
}

/**
 * Show alert message
 */
function showAlert(type, message) {
  const alertDiv = document.createElement("div");
  alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
  alertDiv.style.cssText =
    "top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

  document.body.appendChild(alertDiv);

  // Auto remove after 5 seconds
  setTimeout(() => {
    alertDiv.remove();
  }, 5000);
}

/**
 * Helper: Escape HTML
 */
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

/**
 * Helper: Convert newlines to <br>
 */
function nl2br(text) {
  return text.replace(/\n/g, "<br>");
}
