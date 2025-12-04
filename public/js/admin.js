document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.querySelector(".header__toggle");
  const sidebar = document.querySelector(".admin-sidebar");

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");

      // Save state to localStorage
      const isCollapsed = sidebar.classList.contains("collapsed");
      localStorage.setItem("sidebarCollapsed", isCollapsed);
    });

    // Load saved state
    const savedState = localStorage.getItem("sidebarCollapsed");
    if (savedState === "true") {
      sidebar.classList.add("collapsed");
    }
  }

  if (window.innerWidth <= 768) {
    if (toggleBtn && sidebar) {
      toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("show");
      });

      document.addEventListener("click", function (e) {
        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
          sidebar.classList.remove("show");
        }
      });
    }
  }

  const currentPath = window.location.pathname;
  const menuLinks = document.querySelectorAll(".sidebar__menu-link");

  // Clear all active first
  menuLinks.forEach((link) => link.classList.remove("active"));

  // Find exact match
  let found = false;
  menuLinks.forEach((link) => {
    const linkPath = new URL(link.href).pathname;
    if (currentPath === linkPath) {
      link.classList.add("active");
      found = true;
    }
  });

  // If no exact match, find best parent (but exclude /admin for everything)
  if (!found) {
    let bestMatch = null;
    let longestMatchLength = 0;

    menuLinks.forEach((link) => {
      const linkPath = new URL(link.href).pathname;

      // Only match specific admin sections, not the general /admin
      if (linkPath !== "/admin" && currentPath.startsWith(linkPath + "/")) {
        if (linkPath.length > longestMatchLength) {
          longestMatchLength = linkPath.length;
          bestMatch = link;
        }
      }
    });

    if (bestMatch) {
      bestMatch.classList.add("active");
    } else {
      if (currentPath === "/admin" || currentPath.startsWith("/admin/")) {
        const dashboardLink = document.querySelector('a[href="/admin"]');
        if (dashboardLink) {
          dashboardLink.classList.add("active");
        }
      }
    }
  }

  // ============================================
  // CONFIRM DELETE
  // ============================================
  const deleteButtons = document.querySelectorAll(
    '.btn-delete, [data-action="delete"]'
  );

  deleteButtons.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      if (!confirm("Bạn có chắc chắn muốn xóa?")) {
        e.preventDefault();
        return false;
      }
    });
  });

  // ============================================
  // AUTO HIDE ALERTS
  // ============================================
  const alerts = document.querySelectorAll(".alert");

  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => {
        alert.remove();
      }, 300);
    }, 5000);
  });

  // ============================================
  // SEARCH FUNCTIONALITY
  // ============================================
  const searchInput = document.querySelector(".header__search-input");

  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        const searchTerm = this.value.trim();
        if (searchTerm) {
          console.log("Searching for:", searchTerm);
          // Implement your search logic here
        }
      }
    });
  }

  // ============================================
  // TOOLTIP INITIALIZATION (if using)
  // ============================================
  const tooltipElements = document.querySelectorAll("[data-tooltip]");

  tooltipElements.forEach((el) => {
    el.addEventListener("mouseenter", function () {
      const tooltipText = this.getAttribute("data-tooltip");
      const tooltip = document.createElement("div");
      tooltip.className = "tooltip";
      tooltip.textContent = tooltipText;
      document.body.appendChild(tooltip);

      const rect = this.getBoundingClientRect();
      tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + "px";
      tooltip.style.left =
        rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px";
    });

    el.addEventListener("mouseleave", function () {
      const tooltip = document.querySelector(".tooltip");
      if (tooltip) tooltip.remove();
    });
  });

  // ============================================
  // TABLE ROW ACTIONS
  // ============================================
  const tableRows = document.querySelectorAll(".table tbody tr");

  tableRows.forEach((row) => {
    row.addEventListener("click", function (e) {
      // Don't trigger if clicking on a button or link
      if (
        e.target.tagName === "A" ||
        e.target.tagName === "BUTTON" ||
        e.target.closest("a") ||
        e.target.closest("button")
      ) {
        return;
      }

      // Add selected class
      tableRows.forEach((r) => r.classList.remove("selected"));
      this.classList.add("selected");
    });
  });

  // ============================================
  // FORM VALIDATION
  // ============================================
  const forms = document.querySelectorAll("form[data-validate]");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      let isValid = true;
      const requiredFields = form.querySelectorAll("[required]");

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          isValid = false;
          field.classList.add("error");

          // Show error message
          let errorMsg = field.nextElementSibling;
          if (!errorMsg || !errorMsg.classList.contains("error-message")) {
            errorMsg = document.createElement("span");
            errorMsg.className = "error-message";
            errorMsg.textContent = "Trường này là bắt buộc";
            errorMsg.style.color = "var(--danger-color)";
            errorMsg.style.fontSize = "12px";
            errorMsg.style.marginTop = "5px";
            errorMsg.style.display = "block";
            field.parentNode.insertBefore(errorMsg, field.nextSibling);
          }
        } else {
          field.classList.remove("error");
          const errorMsg = field.nextElementSibling;
          if (errorMsg && errorMsg.classList.contains("error-message")) {
            errorMsg.remove();
          }
        }
      });

      if (!isValid) {
        e.preventDefault();
        alert("Vui lòng điền đầy đủ các trường bắt buộc!");
      }
    });

    // Remove error on input
    form.querySelectorAll("[required]").forEach((field) => {
      field.addEventListener("input", function () {
        this.classList.remove("error");
        const errorMsg = this.nextElementSibling;
        if (errorMsg && errorMsg.classList.contains("error-message")) {
          errorMsg.remove();
        }
      });
    });
  });

  // ============================================
  // NOTIFICATION BADGE ANIMATION
  // ============================================
  const notificationBadges = document.querySelectorAll(
    ".header__notification-badge"
  );

  notificationBadges.forEach((badge) => {
    if (badge.textContent !== "0") {
      badge.style.animation = "pulse 2s infinite";
    }
  });

  // ============================================
  // SMOOTH SCROLL
  // ============================================
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");
      if (targetId !== "#") {
        const target = document.querySelector(targetId);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    });
  });

  // ============================================
  // IMAGE PREVIEW FOR FILE UPLOADS
  // ============================================
  const imageInputs = document.querySelectorAll(
    'input[type="file"][accept*="image"]'
  );

  imageInputs.forEach((input) => {
    input.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          let preview = input.parentNode.querySelector(".image-preview");
          if (!preview) {
            preview = document.createElement("img");
            preview.className = "image-preview";
            preview.style.maxWidth = "200px";
            preview.style.marginTop = "10px";
            preview.style.borderRadius = "5px";
            input.parentNode.appendChild(preview);
          }
          preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  });

  // ============================================
  // RESPONSIVE TABLE
  // ============================================
  function makeTablesResponsive() {
    const tables = document.querySelectorAll(".table");

    if (window.innerWidth <= 768) {
      tables.forEach((table) => {
        if (!table.parentElement.classList.contains("table-responsive")) {
          const wrapper = document.createElement("div");
          wrapper.className = "table-responsive";
          wrapper.style.overflowX = "auto";
          table.parentNode.insertBefore(wrapper, table);
          wrapper.appendChild(table);
        }
      });
    }
  }

  makeTablesResponsive();
  window.addEventListener("resize", makeTablesResponsive);

  // ============================================
  // Sidebar Toggle (Additional)
  // ============================================
  const sidebarToggle = document.getElementById("sidebarToggle");

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
    });
  }

  // Auto close alerts after 5 seconds
  const flashAlerts = document.querySelectorAll('[id^="flash-message-"]');
  flashAlerts.forEach((alert) => {
    setTimeout(() => {
      if (alert && alert.parentNode) {
        alert.style.animation = "slideOut 0.3s ease-out";
        setTimeout(() => alert.remove(), 300);
      }
    }, 5000);
  });
});

// Close alert function
function closeAlert(alertId) {
  const alert = document.getElementById(alertId);
  if (alert) {
    alert.style.animation = "slideOut 0.3s ease-out";
    setTimeout(() => alert.remove(), 300);
  }
}

// ============================================
// CSS ANIMATION KEYFRAMES (Add to CSS if needed)
// ============================================
const style = document.createElement("style");
style.textContent = `
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .table tbody tr.selected {
        background: rgba(78, 115, 223, 0.1);
    }
    
    .form-control.error {
        border-color: var(--danger-color);
    }
`;
document.head.appendChild(style);
