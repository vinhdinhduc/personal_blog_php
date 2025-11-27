/**
 * ADMIN-COMMENTS.JS - Admin Comments Management
 */

// Select all functionality
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

  // Update select all when individual checkboxes change
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

/**
 * Get selected comment IDs
 */
function getSelectedCommentIds() {
  const checkboxes = document.querySelectorAll(".comment-checkbox:checked");
  return Array.from(checkboxes).map((cb) => cb.value);
}

/**
 * Bulk approve comments
 */
function bulkApprove() {
  const ids = getSelectedCommentIds();

  if (ids.length === 0) {
    alert("Vui lòng chọn ít nhất một bình luận!");
    return;
  }

  if (!confirm(`Phê duyệt ${ids.length} bình luận đã chọn?`)) {
    return;
  }

  // Create form and submit
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "/admin/comments/bulk-approve";

  // Add CSRF token
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  const csrfInput = document.createElement("input");
  csrfInput.type = "hidden";
  csrfInput.name = "csrf_token";
  csrfInput.value = csrfToken;
  form.appendChild(csrfInput);

  // Add comment IDs
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

/**
 * Bulk delete comments
 */
function bulkDelete() {
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

  // Create form and submit
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "/admin/comments/bulk-delete";

  // Add CSRF token
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;
  const csrfInput = document.createElement("input");
  csrfInput.type = "hidden";
  csrfInput.name = "csrf_token";
  csrfInput.value = csrfToken;
  form.appendChild(csrfInput);

  // Add comment IDs
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

/**
 * Confirm delete single comment
 */
function confirmDelete(replyCount) {
  if (replyCount > 0) {
    return confirm(
      `CẢNH BÁO: Bình luận này có ${replyCount} câu trả lời.\n\nXóa sẽ xóa tất cả câu trả lời. Bạn có chắc chắn?`
    );
  }
  return confirm("Bạn có chắc muốn xóa bình luận này?");
}

/**
 * Edit comment
 */
function editComment(commentId, content) {
  const modal = document.getElementById("editCommentModal");
  const form = document.getElementById("editCommentForm");

  document.getElementById("edit_comment_id").value = commentId;
  document.getElementById("edit_comment_content").value = content;

  modal.classList.add("active");

  // Handle form submission
  form.onsubmit = async function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    const commentId = formData.get("comment_id");

    try {
      const response = await fetch(`/admin/comments/edit/${commentId}`, {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        // Update comment content in the list
        const commentItem = document.querySelector(
          `[data-comment-id="${commentId}"]`
        );
        if (commentItem) {
          const contentDiv = commentItem.querySelector(
            ".comment-item__content"
          );
          if (contentDiv) {
            contentDiv.innerHTML = result.content.replace(/\n/g, "<br>");
          }
        }

        alert(result.message || "Cập nhật thành công!");
        closeEditModal();
      } else {
        alert(result.message || "Có lỗi xảy ra!");
      }
    } catch (error) {
      console.error("Error:", error);
      alert("Có lỗi xảy ra khi cập nhật bình luận!");
    }
  };
}

/**
 * Close edit modal
 */
function closeEditModal() {
  const modal = document.getElementById("editCommentModal");
  modal.classList.remove("active");
  document.getElementById("editCommentForm").reset();
}

/**
 * View comment details
 */
async function viewComment(commentId) {
  const modal = document.getElementById("viewCommentModal");
  const content = document.getElementById("viewCommentContent");

  // Show loading
  content.innerHTML =
    '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #4e73df;"></i><p>Đang tải...</p></div>';
  modal.classList.add("active");

  try {
    const response = await fetch(`/admin/comments/view/${commentId}`);
    const result = await response.json();

    if (result.success) {
      const comment = result.comment;
      const post = result.post;
      const replies = result.replies || [];

      let html = `
                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: #2c3e50;">
                        <i class="fas fa-user"></i> Thông tin người bình luận
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; background: #f8f9fc; padding: 15px; border-radius: 8px;">
                        <div>
                            <strong>Tên:</strong> ${
                              comment.user_name || "Anonymous"
                            }
                        </div>
                        <div>
                            <strong>Email:</strong> ${
                              comment.user_email || "N/A"
                            }
                        </div>
                        <div>
                            <strong>Ngày tạo:</strong> ${new Date(
                              comment.created_at
                            ).toLocaleString("vi-VN")}
                        </div>
                        <div>
                            <strong>Trạng thái:</strong> 
                            ${
                              comment.is_approved
                                ? '<span class="comment-badge comment-badge--approved"><i class="fas fa-check-circle"></i> Đã duyệt</span>'
                                : '<span class="comment-badge comment-badge--pending"><i class="fas fa-clock"></i> Chờ duyệt</span>'
                            }
                        </div>
                    </div>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: #2c3e50;">
                        <i class="fas fa-comment"></i> Nội dung bình luận
                    </h4>
                    <div style="background: #f8f9fc; padding: 20px; border-radius: 8px; line-height: 1.8;">
                        ${comment.content.replace(/\n/g, "<br>")}
                    </div>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 15px; color: #2c3e50;">
                        <i class="fas fa-newspaper"></i> Bài viết
                    </h4>
                    <div style="background: #f8f9fc; padding: 15px; border-radius: 8px;">
                        <a href="/post/${
                          post.slug
                        }" target="_blank" style="color: #4e73df; text-decoration: none; font-weight: 600;">
                            ${post.title}
                        </a>
                    </div>
                </div>
            `;

      if (replies.length > 0) {
        html += `
                    <div>
                        <h4 style="margin-bottom: 15px; color: #2c3e50;">
                            <i class="fas fa-reply"></i> Câu trả lời (${replies.length})
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                `;

        replies.forEach((reply) => {
          html += `
                        <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; border-left: 4px solid #36b9cc;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <img src="https://www.gravatar.com/avatar/${md5(
                                  reply.user_email
                                )}?s=40&d=mp" 
                                     style="width: 40px; height: 40px; border-radius: 50%;">
                                <div>
                                    <strong>${
                                      reply.user_name || "Anonymous"
                                    }</strong>
                                    <div style="font-size: 12px; color: #858796;">
                                        ${new Date(
                                          reply.created_at
                                        ).toLocaleString("vi-VN")}
                                    </div>
                                </div>
                            </div>
                            <div style="line-height: 1.6;">
                                ${reply.content.replace(/\n/g, "<br>")}
                            </div>
                        </div>
                    `;
        });

        html += `
                        </div>
                    </div>
                `;
      }

      content.innerHTML = html;
    } else {
      content.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #e74a3b;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px;"></i>
                    <p>${
                      result.message || "Không thể tải thông tin bình luận!"
                    }</p>
                </div>
            `;
    }
  } catch (error) {
    console.error("Error:", error);
    content.innerHTML = `
            <div style="text-align: center; padding: 40px; color: #e74a3b;">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px;"></i>
                <p>Có lỗi xảy ra khi tải thông tin bình luận!</p>
            </div>
        `;
  }
}

/**
 * Close view modal
 */
function closeViewModal() {
  const modal = document.getElementById("viewCommentModal");
  modal.classList.remove("active");
}

/**
 * MD5 hash function for Gravatar
 */
function md5(string) {
  // Simple MD5 implementation for Gravatar
  // In production, use a proper MD5 library
  return string; // Simplified for demo
}

/**
 * Close modals on outside click
 */
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("comment-modal")) {
    e.target.classList.remove("active");
  }
});

/**
 * Close modals on Escape key
 */
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    const modals = document.querySelectorAll(".comment-modal.active");
    modals.forEach((modal) => modal.classList.remove("active"));
  }
});

/**
 * Keyboard shortcuts
 */
document.addEventListener("keydown", function (e) {
  // Ctrl/Cmd + K: Focus search
  if ((e.ctrlKey || e.metaKey) && e.key === "k") {
    e.preventDefault();
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
      searchInput.focus();
    }
  }

  // Ctrl/Cmd + A: Select all (when focus is not in input)
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

/**
 * Auto-refresh for pending comments (every 30 seconds)
 */
let autoRefreshInterval;

function startAutoRefresh() {
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get("status");

  // Only auto-refresh on pending page
  if (status === "pending") {
    autoRefreshInterval = setInterval(() => {
      // Check for new pending comments
      fetch("/admin/comments/check-pending")
        .then((response) => response.json())
        .then((data) => {
          if (data.has_new) {
            // Show notification
            const notification = document.createElement("div");
            notification.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: #f6c23e;
                            color: #fff;
                            padding: 15px 20px;
                            border-radius: 8px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            z-index: 10000;
                            animation: slideIn 0.3s ease;
                        `;
            notification.innerHTML = `
                            <strong><i class="fas fa-bell"></i> Có bình luận mới!</strong>
                            <br>
                            <button onclick="location.reload()" style="margin-top: 10px; padding: 5px 15px; background: #fff; color: #f6c23e; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">
                                Tải lại trang
                            </button>
                        `;
            document.body.appendChild(notification);

            // Auto-remove after 10 seconds
            setTimeout(() => notification.remove(), 10000);
          }
        })
        .catch((error) => console.error("Auto-refresh error:", error));
    }, 30000); // 30 seconds
  }
}

// Start auto-refresh on page load
document.addEventListener("DOMContentLoaded", startAutoRefresh);

// Clear interval when leaving page
window.addEventListener("beforeunload", () => {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
  }
});
