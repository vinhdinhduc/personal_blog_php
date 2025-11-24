/**
 * Post Detail JavaScript
 * File: public/js/post-detail.js
 *
 * Xử lý interactions cho trang chi tiết bài viết
 * - Like button
 * - Share functionality
 * - Comment interactions
 * - Reply functionality
 */

document.addEventListener("DOMContentLoaded", function () {
  initPostDetail();
});

/**
 * Initialize post detail functionality
 */
function initPostDetail() {
  initLikeButton();
  initShareButton();
  initCommentActions();
  initSmoothScroll();
  initCopyCode();
}

/**
 * Like Button
 */
function initLikeButton() {
  const likeBtn = document.getElementById("likeBtn");
  if (!likeBtn) return;

  // Check if already liked (localStorage)
  const postId = getPostId();
  const isLiked = localStorage.getItem(`liked_${postId}`) === "true";

  if (isLiked) {
    likeBtn.classList.add("active");
    likeBtn.querySelector("i").classList.replace("far", "fas");
  }

  likeBtn.addEventListener("click", async function () {
    const icon = this.querySelector("i");
    const isCurrentlyLiked = this.classList.contains("active");

    if (isCurrentlyLiked) {
      // Unlike
      this.classList.remove("active");
      icon.classList.replace("fas", "far");
      localStorage.removeItem(`liked_${postId}`);
    } else {
      // Like
      this.classList.add("active");
      icon.classList.replace("far", "fas");
      localStorage.setItem(`liked_${postId}`, "true");

      // Animation
      icon.style.animation = "heartBeat 0.3s ease";
      setTimeout(() => {
        icon.style.animation = "";
      }, 300);
    }

    // TODO: Send AJAX request to server
    // await sendLikeRequest(postId, !isCurrentlyLiked);
  });
}

/**
 * Share Button
 */
function initShareButton() {
  const shareBtn = document.getElementById("shareBtn");
  if (!shareBtn) return;

  shareBtn.addEventListener("click", async function () {
    const url = window.location.href;
    const title = document.querySelector(".post__title")?.textContent || "";

    // Check if Web Share API is supported
    if (navigator.share) {
      try {
        await navigator.share({
          title: title,
          url: url,
        });
      } catch (error) {
        if (error.name !== "AbortError") {
          copyToClipboard(url);
        }
      }
    } else {
      copyToClipboard(url);
    }
  });

  // Social share buttons
  document.querySelectorAll(".post__share-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(
        document.querySelector(".post__title")?.textContent || ""
      );

      let shareUrl = "";

      if (this.querySelector(".fa-facebook-f")) {
        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
      } else if (this.querySelector(".fa-twitter")) {
        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
      } else if (this.querySelector(".fa-link")) {
        copyToClipboard(window.location.href);
        return;
      }

      if (shareUrl) {
        window.open(shareUrl, "_blank", "width=600,height=400");
      }
    });
  });
}

/**
 * Comment Actions
 */
function initCommentActions() {
  // Reply buttons
  document
    .querySelectorAll('.comment__action-btn[data-action="reply"]')
    .forEach((btn) => {
      btn.addEventListener("click", function () {
        const commentId = this.closest(".comment").dataset.commentId;
        toggleReplyForm(commentId);
      });
    });

  // Cancel reply buttons
  document.querySelectorAll(".comment__reply-form-cancel").forEach((btn) => {
    btn.addEventListener("click", function () {
      const form = this.closest(".comment__reply-form");
      if (form) {
        form.style.display = "none";
        form.querySelector("textarea").value = "";
      }
    });
  });

  // Edit buttons
  document
    .querySelectorAll('.comment__action-btn[data-action="edit"]')
    .forEach((btn) => {
      btn.addEventListener("click", function () {
        const commentId = this.closest(".comment").dataset.commentId;
        editComment(commentId);
      });
    });

  // Delete buttons
  document
    .querySelectorAll('.comment__action-btn[data-action="delete"]')
    .forEach((btn) => {
      btn.addEventListener("click", function () {
        if (confirm("Bạn có chắc muốn xóa bình luận này?")) {
          const commentId = this.closest(".comment").dataset.commentId;
          deleteComment(commentId);
        }
      });
    });
}

/**
 * Toggle reply form
 */
function toggleReplyForm(commentId) {
  const comment = document.querySelector(
    `.comment[data-comment-id="${commentId}"]`
  );
  if (!comment) return;

  let replyForm = comment.querySelector(".comment__reply-form");

  if (replyForm) {
    // Toggle existing form
    replyForm.style.display =
      replyForm.style.display === "none" ? "block" : "none";
  } else {
    // Create new reply form
    replyForm = createReplyForm(commentId);
    comment.appendChild(replyForm);
  }

  // Focus on textarea
  const textarea = replyForm.querySelector("textarea");
  if (textarea) {
    textarea.focus();
  }
}

/**
 * Create reply form HTML
 */
function createReplyForm(parentId) {
  const form = document.createElement("div");
  form.className = "comment__reply-form";
  form.innerHTML = `
        <form method="POST" action="/comment/create">
            <input type="hidden" name="csrf_token" value="${getCsrfToken()}">
            <input type="hidden" name="post_id" value="${getPostId()}">
            <input type="hidden" name="parent_id" value="${parentId}">
            <textarea name="content" rows="3" placeholder="Viết câu trả lời..." required></textarea>
            <div class="comment__reply-form-actions">
                <button type="submit" class="btn btn--primary btn--sm">
                    <i class="fas fa-paper-plane"></i> Gửi
                </button>
                <button type="button" class="btn btn--secondary btn--sm comment__reply-form-cancel">
                    Hủy
                </button>
            </div>
        </form>
    `;

  // Add cancel event listener
  const cancelBtn = form.querySelector(".comment__reply-form-cancel");
  cancelBtn.addEventListener("click", function () {
    form.style.display = "none";
    form.querySelector("textarea").value = "";
  });

  return form;
}

/**
 * Edit comment
 */
async function editComment(commentId) {
  const comment = document.querySelector(
    `.comment[data-comment-id="${commentId}"]`
  );
  if (!comment) return;

  const contentDiv = comment.querySelector(".comment__content");
  const currentContent = contentDiv.textContent.trim();

  // Create edit form
  const editForm = document.createElement("div");
  editForm.className = "comment__edit-form";
  editForm.innerHTML = `
        <textarea class="comment-form__textarea" rows="3">${currentContent}</textarea>
        <div class="comment__reply-form-actions">
            <button class="btn btn--primary btn--sm save-edit">
                <i class="fas fa-save"></i> Lưu
            </button>
            <button class="btn btn--secondary btn--sm cancel-edit">
                Hủy
            </button>
        </div>
    `;

  // Hide original content
  contentDiv.style.display = "none";
  contentDiv.parentNode.insertBefore(editForm, contentDiv.nextSibling);

  // Save button
  editForm
    .querySelector(".save-edit")
    .addEventListener("click", async function () {
      const newContent = editForm.querySelector("textarea").value.trim();

      if (!newContent) {
        alert("Nội dung không được trống");
        return;
      }

      try {
        const response = await fetch(`/comment/${commentId}/update`, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            csrf_token: getCsrfToken(),
            content: newContent,
          }),
        });

        const data = await response.json();

        if (data.success) {
          contentDiv.textContent = newContent;
          contentDiv.style.display = "block";
          editForm.remove();
          showToast("Cập nhật bình luận thành công", "success");
        } else {
          showToast(data.message || "Cập nhật thất bại", "error");
        }
      } catch (error) {
        console.error("Error:", error);
        showToast("Có lỗi xảy ra", "error");
      }
    });

  // Cancel button
  editForm.querySelector(".cancel-edit").addEventListener("click", function () {
    contentDiv.style.display = "block";
    editForm.remove();
  });
}

/**
 * Delete comment
 */
async function deleteComment(commentId) {
  try {
    const response = await fetch(`/comment/${commentId}/delete`, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        csrf_token: getCsrfToken(),
      }),
    });

    const data = await response.json();

    if (data.success) {
      const comment = document.querySelector(
        `.comment[data-comment-id="${commentId}"]`
      );
      if (comment) {
        comment.style.opacity = "0";
        comment.style.transform = "translateY(-10px)";
        setTimeout(() => {
          comment.remove();
        }, 300);
      }
      showToast("Xóa bình luận thành công", "success");
    } else {
      showToast(data.message || "Xóa thất bại", "error");
    }
  } catch (error) {
    console.error("Error:", error);
    showToast("Có lỗi xảy ra", "error");
  }
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href === "#") return;

      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });
}

/**
 * Copy code blocks
 */
function initCopyCode() {
  document.querySelectorAll(".post__content pre").forEach((pre) => {
    const button = document.createElement("button");
    button.className = "code-copy-btn";
    button.innerHTML = '<i class="fas fa-copy"></i> Copy';
    button.style.cssText =
      "position: absolute; top: 8px; right: 8px; padding: 4px 8px; font-size: 12px; background: rgba(255,255,255,0.1); border: none; color: #fff; cursor: pointer; border-radius: 4px;";

    pre.style.position = "relative";
    pre.appendChild(button);

    button.addEventListener("click", function () {
      const code = pre.querySelector("code")?.textContent || pre.textContent;
      copyToClipboard(code);

      button.innerHTML = '<i class="fas fa-check"></i> Copied!';
      setTimeout(() => {
        button.innerHTML = '<i class="fas fa-copy"></i> Copy';
      }, 2000);
    });
  });
}

/**
 * Utility: Copy to clipboard
 */
function copyToClipboard(text) {
  if (navigator.clipboard) {
    navigator.clipboard
      .writeText(text)
      .then(() => {
        showToast("Đã copy vào clipboard", "success");
      })
      .catch((err) => {
        console.error("Copy failed:", err);
      });
  } else {
    // Fallback
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.style.position = "fixed";
    textarea.style.opacity = "0";
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);
    showToast("Đã copy vào clipboard", "success");
  }
}

/**
 * Utility: Show toast notification
 */
function showToast(message, type = "info") {
  // Use existing toast system if available
  if (typeof Toast !== "undefined") {
    Toast[type](message);
    return;
  }

  // Fallback simple toast
  const toast = document.createElement("div");
  toast.className = `toast toast--${type}`;
  toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: ${
          type === "success"
            ? "#10b981"
            : type === "error"
            ? "#ef4444"
            : "#3b82f6"
        };
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "slideOut 0.3s ease";
    setTimeout(() => {
      toast.remove();
    }, 300);
  }, 3000);
}

/**
 * Utility: Get CSRF token
 */
function getCsrfToken() {
  return document.querySelector('input[name="csrf_token"]')?.value || "";
}

/**
 * Utility: Get post ID
 */
function getPostId() {
  return (
    document.querySelector('input[name="post_id"]')?.value ||
    window.location.pathname.split("/").pop()
  );
}

/**
 * Add CSS animations
 */
const style = document.createElement("style");
style.textContent = `
    @keyframes heartBeat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.3); }
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .code-copy-btn:hover {
        background: rgba(255,255,255,0.2) !important;
    }
`;
document.head.appendChild(style);
