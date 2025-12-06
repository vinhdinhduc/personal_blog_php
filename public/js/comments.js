/**
 * Comment System JavaScript - FIXED & IMPROVED
 */

document.addEventListener("DOMContentLoaded", function () {
  initCommentSystem();
});

function initCommentSystem() {
  // Submit comment forms
  document.querySelectorAll(".comment-form").forEach((form) => {
    form.addEventListener("submit", handleCommentSubmit);
  });

  // Reply buttons
  document.querySelectorAll(".reply-btn").forEach((btn) => {
    btn.addEventListener("click", handleReplyClick);
  });

  // Cancel reply buttons
  document.querySelectorAll(".cancel-reply-btn").forEach((btn) => {
    btn.addEventListener("click", handleCancelReply);
  });

  // Edit buttons
  document.querySelectorAll(".edit-comment-btn").forEach((btn) => {
    btn.addEventListener("click", handleEditComment);
  });

  // Delete buttons
  document.querySelectorAll(".delete-comment-btn").forEach((btn) => {
    btn.addEventListener("click", handleDeleteComment);
  });

  // Approve buttons
  document.querySelectorAll(".approve-comment-btn").forEach((btn) => {
    btn.addEventListener("click", handleApproveComment);
  });
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
      if (form.id !== `reply-form-${commentId}`) {
        form.style.display = "none";
      }
    });

    // Toggle this reply form
    if (replyForm.style.display === "none") {
      replyForm.style.display = "block";
      const textarea = replyForm.querySelector("textarea");
      if (textarea) {
        textarea.focus();
      }
    } else {
      replyForm.style.display = "none";
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
    const textarea = replyForm.querySelector("textarea");
    if (textarea) {
      textarea.value = "";
    }
  }
}

/**
 * Handle edit comment
 */
function handleEditComment(e) {
  e.preventDefault();
  const commentId = e.currentTarget.dataset.commentId;
  const commentDiv = document.getElementById(`comment-${commentId}`);
  const contentDiv = commentDiv.querySelector(".comment-content");
  const currentContent = contentDiv.textContent.trim();

  // Check if already editing
  if (commentDiv.querySelector(".edit-comment-form")) {
    return;
  }

  // Hide actions while editing
  const actions = commentDiv.querySelector(".comment-actions");
  actions.style.display = "none";

  // Create edit form
  const editForm = document.createElement("div");
  editForm.className = "edit-comment-form";
  editForm.innerHTML = `
    <textarea class="form-control" rows="3">${escapeHtml(
      currentContent
    )}</textarea>
    <button class="btn btn--primary btn--sm save-edit-btn">
      <i class="fas fa-check"></i> Lưu
    </button>
    <button class="btn btn--secondary btn--sm cancel-edit-btn">
      <i class="fas fa-times"></i> Hủy
    </button>
  `;

  // Insert after content
  contentDiv.style.display = "none";
  contentDiv.parentNode.insertBefore(editForm, contentDiv.nextSibling);

  // Handle save
  editForm
    .querySelector(".save-edit-btn")
    .addEventListener("click", async () => {
      const newContent = editForm.querySelector("textarea").value.trim();

      if (!newContent) {
        showAlert("error", "Nội dung không được trống");
        return;
      }

      const saveBtn = editForm.querySelector(".save-edit-btn");
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

      try {
        const formData = new FormData();
        formData.append("content", newContent);
        formData.append(
          "csrf_token",
          document.querySelector('input[name="csrf_token"]').value
        );

        const response = await fetch(`/comment/${commentId}/update`, {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          showAlert("success", data.message);
          contentDiv.innerHTML = nl2br(escapeHtml(newContent));
          contentDiv.style.display = "block";
          actions.style.display = "flex";
          editForm.remove();
        } else {
          showAlert("error", data.message);
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<i class="fas fa-check"></i> Lưu';
        }
      } catch (error) {
        console.error("Error:", error);
        showAlert("error", "Có lỗi xảy ra");
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-check"></i> Lưu';
      }
    });

  // Handle cancel
  editForm.querySelector(".cancel-edit-btn").addEventListener("click", () => {
    contentDiv.style.display = "block";
    actions.style.display = "flex";
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
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      showAlert("success", data.message);

      // Fade out and remove
      const commentDiv = document.getElementById(`comment-${commentId}`);
      if (commentDiv) {
        commentDiv.style.opacity = "0";
        commentDiv.style.transform = "translateY(-10px)";
        commentDiv.style.transition = "all 0.3s ease";

        setTimeout(() => {
          commentDiv.remove();
        }, 300);
      }
    } else {
      if (data.confirm_required) {
        if (confirm(data.message + " Tiếp tục?")) {
          // Implement cascade delete if needed
          showAlert("info", "Chức năng xóa cascade chưa implement");
        }
      } else {
        showAlert("error", data.message);
      }
    }
  } catch (error) {
    console.error("Error:", error);
    showAlert("error", "Có lỗi xảy ra");
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
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      showAlert("success", data.message);

      const commentDiv = document.getElementById(`comment-${commentId}`);

      // Remove pending badge
      const pendingBadge = commentDiv.querySelector(".comment-badge--pending");
      if (pendingBadge) {
        pendingBadge.remove();
      }

      // Remove pending class
      commentDiv.classList.remove("comment-pending");

      // Remove approve button
      e.currentTarget.remove();
    } else {
      showAlert("error", data.message);
    }
  } catch (error) {
    console.error("Error:", error);
    showAlert("error", "Có lỗi xảy ra");
  }
}

/**
 * Show alert message
 */
function showAlert(type, message) {
  // Remove existing alerts
  document
    .querySelectorAll(".comment-alert")
    .forEach((alert) => alert.remove());

  const alertDiv = document.createElement("div");
  alertDiv.className = `comment-alert comment-alert--${type}`;
  alertDiv.innerHTML = `
    <div class="comment-alert__icon">
      ${
        type === "success"
          ? '<i class="fas fa-check-circle"></i>'
          : '<i class="fas fa-exclamation-circle"></i>'
      }
    </div>
    <div class="comment-alert__message">${message}</div>
    <button class="comment-alert__close">
      <i class="fas fa-times"></i>
    </button>
  `;

  document.body.appendChild(alertDiv);

  // Close button
  alertDiv
    .querySelector(".comment-alert__close")
    .addEventListener("click", () => {
      alertDiv.classList.add("comment-alert--hide");
      setTimeout(() => alertDiv.remove(), 300);
    });

  // Auto remove
  setTimeout(() => {
    if (alertDiv.parentElement) {
      alertDiv.classList.add("comment-alert--hide");
      setTimeout(() => alertDiv.remove(), 300);
    }
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

// Alert styles
const alertStyles = document.createElement("style");
alertStyles.textContent = `
  .comment-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    animation: slideInRight 0.3s ease;
    max-width: 400px;
  }

  .comment-alert--success {
    border-left: 4px solid #16a34a;
  }

  .comment-alert--error {
    border-left: 4px solid #dc2626;
  }

  .comment-alert__icon {
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  .comment-alert--success .comment-alert__icon {
    color: #16a34a;
  }

  .comment-alert--error .comment-alert__icon {
    color: #dc2626;
  }

  .comment-alert__message {
    flex: 1;
    font-size: 0.9375rem;
    color: #374151;
  }

  .comment-alert__close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.15s;
  }

  .comment-alert__close:hover {
    color: #374151;
  }

  .comment-alert--hide {
    animation: slideOutRight 0.3s ease forwards;
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
    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }

  @media (max-width: 768px) {
    .comment-alert {
      left: 10px;
      right: 10px;
      max-width: none;
    }
  }
`;
document.head.appendChild(alertStyles);
