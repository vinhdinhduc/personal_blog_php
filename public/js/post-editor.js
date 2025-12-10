/**
 * Post Editor JavaScript
 * File: public/assets/js/post-editor.js
 */

console.log("=== POST EDITOR JS LOADED ===");

// Khởi tạo Quill Editor
document.addEventListener("DOMContentLoaded", function () {
  const editorElement = document.getElementById("editor");

  if (!editorElement) {
    console.error("Editor element not found!");
    return;
  }

  console.log("Initializing Quill Editor...");

  // Khởi tạo Quill với config đầy đủ
  window.quillEditor = new Quill("#editor", {
    theme: "snow",
    modules: {
      toolbar: [
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        [{ font: [] }],
        [{ size: ["small", false, "large", "huge"] }],
        ["bold", "italic", "underline", "strike"],
        [{ color: [] }, { background: [] }],
        [{ script: "sub" }, { script: "super" }],
        [{ list: "ordered" }, { list: "bullet" }],
        [{ indent: "-1" }, { indent: "+1" }],
        [{ align: [] }],
        ["blockquote", "code-block"],
        ["link", "image", "video"],
        ["clean"],
      ],
    },
    placeholder: "Nhập nội dung bài viết tại đây...",
  });

  console.log("Quill Editor initialized successfully!");

  // Load existing content (for edit mode)
  const hiddenContent = document.getElementById("editorContent");
  if (hiddenContent && hiddenContent.value.trim()) {
    console.log("Loading existing content...");
    window.quillEditor.root.innerHTML = hiddenContent.value;
    console.log("Content loaded successfully!");
  }

  console.log(
    "Initial content:",
    window.quillEditor.root.innerHTML.substring(0, 100)
  );

  //  SYNC CONTENT NGAY KHI QUILL THAY ĐỔI
  window.quillEditor.on("text-change", function () {
    const content = window.quillEditor.root.innerHTML;
    const hiddenField = document.getElementById("editorContent");

    if (hiddenField) {
      hiddenField.value = content;
      console.log("Content synced:", content.substring(0, 100) + "...");
    }
  });
});

// ==============================================
// UTILITY FUNCTIONS (sử dụng window.quillEditor)
// ==============================================

(function () {
  "use strict";

  // ==============================================
  // 2. SYNC QUILL CONTENT WITH FORM
  // ==============================================

  // Wait for Quill to be ready
  function waitForQuill(callback) {
    if (window.quillEditor) {
      callback();
    } else {
      setTimeout(() => waitForQuill(callback), 100);
    }
  }

  waitForQuill(function () {
    const form = document.getElementById("postForm");
    const editorContent = document.getElementById("editorContent");

    if (form && editorContent) {
      form.addEventListener("submit", function (e) {
        // Get HTML content from Quill
        const html = window.quillEditor.root.innerHTML;
        editorContent.value = html;

        // Validation
        if (window.quillEditor.getText().trim().length === 0) {
          e.preventDefault();
          alert("Nội dung bài viết không được để trống!");
          return false;
        }
      });
    }
  });

  // ==============================================
  // 3. WORD & CHARACTER COUNTER
  // ==============================================

  waitForQuill(function () {
    const wordCountElement = document.getElementById("wordCount");
    const charCountElement = document.getElementById("charCount");

    function updateCounter() {
      const text = window.quillEditor.getText().trim();
      const words = text.length > 0 ? text.split(/\s+/).length : 0;
      const chars = text.length;

      if (wordCountElement) {
        wordCountElement.textContent = words.toLocaleString();
      }

      if (charCountElement) {
        charCountElement.textContent = chars.toLocaleString();
      }
    }

    // Update counter on text change
    window.quillEditor.on("text-change", updateCounter);

    // Initial count
    updateCounter();
  });

  // ==============================================
  // 4. SLUG GENERATION
  // ==============================================

  window.generateSlug = function () {
    const titleInput = document.getElementById("postTitle");
    const slugInput = document.getElementById("postSlug");

    if (!titleInput || !slugInput) return;

    const title = titleInput.value;

    // Vietnamese to ASCII conversion map
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
      À: "A",
      Á: "A",
      Ả: "A",
      Ã: "A",
      Ạ: "A",
      Ă: "A",
      Ằ: "A",
      Ắ: "A",
      Ẳ: "A",
      Ẵ: "A",
      Ặ: "A",
      Â: "A",
      Ầ: "A",
      Ấ: "A",
      Ẩ: "A",
      Ẫ: "A",
      Ậ: "A",
      È: "E",
      É: "E",
      Ẻ: "E",
      Ẽ: "E",
      Ẹ: "E",
      Ê: "E",
      Ề: "E",
      Ế: "E",
      Ể: "E",
      Ễ: "E",
      Ệ: "E",
      Ì: "I",
      Í: "I",
      Ỉ: "I",
      Ĩ: "I",
      Ị: "I",
      Ò: "O",
      Ó: "O",
      Ỏ: "O",
      Õ: "O",
      Ọ: "O",
      Ô: "O",
      Ồ: "O",
      Ố: "O",
      Ổ: "O",
      Ỗ: "O",
      Ộ: "O",
      Ơ: "O",
      Ờ: "O",
      Ớ: "O",
      Ở: "O",
      Ỡ: "O",
      Ợ: "O",
      Ù: "U",
      Ú: "U",
      Ủ: "U",
      Ũ: "U",
      Ụ: "U",
      Ư: "U",
      Ừ: "U",
      Ứ: "U",
      Ử: "U",
      Ữ: "U",
      Ự: "U",
      Ỳ: "Y",
      Ý: "Y",
      Ỷ: "Y",
      Ỹ: "Y",
      Ỵ: "Y",
      Đ: "D",
    };

    // Convert Vietnamese to ASCII
    let slug = title
      .split("")
      .map((char) => vietnameseMap[char] || char)
      .join("");

    // Convert to lowercase and replace spaces with hyphens
    slug = slug
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, "") // Remove special characters
      .replace(/\s+/g, "-") // Replace spaces with -
      .replace(/-+/g, "-") // Replace multiple - with single -
      .replace(/^-+|-+$/g, ""); // Trim - from start and end

    slugInput.value = slug;

    // Visual feedback
    slugInput.style.borderColor = "#27ae60";
    setTimeout(() => {
      slugInput.style.borderColor = "";
    }, 1000);
  };

  // Auto-generate slug on title input (debounced)
  const titleInput = document.getElementById("postTitle");
  if (titleInput) {
    let slugTimeout;
    titleInput.addEventListener("input", function () {
      clearTimeout(slugTimeout);
      slugTimeout = setTimeout(() => {
        const slugInput = document.getElementById("postSlug");
        if (!slugInput.value) {
          generateSlug();
        }
      }, 500);
    });
  }

  // ==============================================
  // 5. IMAGE PREVIEW
  // ==============================================

  window.previewImage = function (input) {
    const preview = document.getElementById("thumbnailPreview");

    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    // Validate file type
    if (!file.type.match("image.*")) {
      alert("Vui lòng chọn file ảnh!");
      input.value = "";
      return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert("Kích thước ảnh không được vượt quá 5MB!");
      input.value = "";
      return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
      preview.innerHTML = `
                <img src="${e.target.result}" 
                     alt="Preview" 
                     class="image-upload__preview-img"
                     style="animation: fadeIn 0.3s ease;">
            `;
    };

    reader.readAsDataURL(file);
  };

  // ==============================================
  // 6. CHARACTER COUNTERS
  // ==============================================

  // Excerpt Counter
  const excerptTextarea = document.querySelector('textarea[name="excerpt"]');
  const excerptCounter = document.getElementById("excerptCounter");

  if (excerptTextarea && excerptCounter) {
    function updateExcerptCounter() {
      const length = excerptTextarea.value.length;
      excerptCounter.textContent = length;

      if (length > 200) {
        excerptCounter.classList.add("editor-counter__count--warning");
      } else {
        excerptCounter.classList.remove("editor-counter__count--warning");
      }

      if (length >= 250) {
        excerptCounter.classList.add("editor-counter__count--danger");
      } else {
        excerptCounter.classList.remove("editor-counter__count--danger");
      }
    }

    excerptTextarea.addEventListener("input", updateExcerptCounter);
    updateExcerptCounter();
  }

  // Meta Description Counter
  const metaDescription = document.getElementById("metaDescription");
  const metaDescCounter = document.getElementById("metaDescCounter");

  if (metaDescription && metaDescCounter) {
    function updateMetaCounter() {
      const length = metaDescription.value.length;
      metaDescCounter.textContent = length;

      if (length > 140) {
        metaDescCounter.classList.add("editor-counter__count--warning");
      } else {
        metaDescCounter.classList.remove("editor-counter__count--warning");
      }

      if (length >= 160) {
        metaDescCounter.classList.add("editor-counter__count--danger");
      } else {
        metaDescCounter.classList.remove("editor-counter__count--danger");
      }
    }

    metaDescription.addEventListener("input", updateMetaCounter);
    updateMetaCounter();
  }

  // ==============================================
  // 7. FORM AUTO-SAVE (DRAFT)
  // ==============================================

  let autoSaveTimeout;
  const AUTOSAVE_DELAY = 30000; // 30 seconds

  function autoSaveDraft() {
    if (!form) return;

    const formData = new FormData(form);
    formData.set("status", "draft");
    formData.set("auto_save", "1");

    // Get Quill content
    if (window.quillEditor) {
      formData.set("content", window.quillEditor.root.innerHTML);
    }

    fetch(form.action, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showNotification("Đã tự động lưu bản nháp", "success");
        }
      })
      .catch((error) => {
        console.error("Auto-save error:", error);
      });
  }

  // Trigger auto-save on content change
  waitForQuill(function () {
    window.quillEditor.on("text-change", function () {
      clearTimeout(autoSaveTimeout);
      autoSaveTimeout = setTimeout(autoSaveDraft, AUTOSAVE_DELAY);
    });
  });

  // ==============================================
  // 8. NOTIFICATION SYSTEM
  // ==============================================

  function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification notification--${type}`;
    notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            background: ${
              type === "success"
                ? "#27ae60"
                : type === "error"
                ? "#e74c3c"
                : "#3498db"
            };
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 300px;
        `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.animation = "slideOut 0.3s ease";
      setTimeout(() => {
        document.body.removeChild(notification);
      }, 300);
    }, 3000);
  }

  // ==============================================
  // 9. KEYBOARD SHORTCUTS
  // ==============================================

  document.addEventListener("keydown", function (e) {
    // Ctrl/Cmd + S: Save draft
    if ((e.ctrlKey || e.metaKey) && e.key === "s") {
      e.preventDefault();
      const draftButton = document.querySelector(
        'button[name="status"][value="draft"]'
      );
      if (draftButton) {
        draftButton.click();
      }
    }

    // Ctrl/Cmd + Enter: Publish
    if ((e.ctrlKey || e.metaKey) && e.key === "Enter") {
      e.preventDefault();
      const publishButton = document.querySelector(
        'button[name="status"][value="published"]'
      );
      if (publishButton) {
        publishButton.click();
      }
    }
  });

  // ==============================================
  // 10. UNSAVED CHANGES WARNING
  // ==============================================

  let hasUnsavedChanges = false;

  waitForQuill(function () {
    window.quillEditor.on("text-change", function () {
      hasUnsavedChanges = true;
    });
  });

  form?.addEventListener("submit", function () {
    hasUnsavedChanges = false;
  });

  window.addEventListener("beforeunload", function (e) {
    if (hasUnsavedChanges) {
      e.preventDefault();
      e.returnValue =
        "Bạn có thay đổi chưa được lưu. Bạn có chắc muốn rời khỏi trang?";
      return e.returnValue;
    }
  });

  // ==============================================
  // 11. IMAGE UPLOAD TO EDITOR
  // ==============================================

  function selectImageToInsert() {
    const input = document.createElement("input");
    input.setAttribute("type", "file");
    input.setAttribute("accept", "image/*");
    input.click();

    input.onchange = async () => {
      const file = input.files[0];

      if (!file) return;

      // Show loading
      showNotification("Đang tải ảnh lên...", "info");

      const formData = new FormData();
      formData.append("image", file);

      try {
        const response = await fetch("/admin/upload/image", {
          method: "POST",
          body: formData,
        });

        const data = await response.json();

        if (data.success) {
          const range = quill.getSelection(true);
          quill.insertEmbed(range.index, "image", data.url);
          quill.setSelection(range.index + 1);
          showNotification("Tải ảnh lên thành công!", "success");
        } else {
          showNotification("Lỗi: " + data.message, "error");
        }
      } catch (error) {
        showNotification("Lỗi khi tải ảnh lên", "error");
        console.error("Upload error:", error);
      }
    };
  }

  // Override Quill image button handler
  const toolbar = quill.getModule("toolbar");
  toolbar.addHandler("image", selectImageToInsert);

  // ==============================================
  // 12. RESPONSIVE TOOLBAR
  // ==============================================

  function handleResponsiveToolbar() {
    const toolbar = document.querySelector(".ql-toolbar");
    if (!toolbar) return;

    if (window.innerWidth < 768) {
      toolbar.style.flexWrap = "wrap";
    } else {
      toolbar.style.flexWrap = "nowrap";
    }
  }

  window.addEventListener("resize", handleResponsiveToolbar);
  handleResponsiveToolbar();
})();
