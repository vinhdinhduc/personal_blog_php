// Mobile Menu Toggle
document.addEventListener("DOMContentLoaded", function () {
  const mobileToggle = document.getElementById("mobileToggle");
  const navMenu = document.getElementById("navMenu");

  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener("click", function () {
      navMenu.classList.toggle("active");
      this.classList.toggle("active");

      // Animate hamburger icon
      const spans = this.querySelectorAll("span");
      if (this.classList.contains("active")) {
        spans[0].style.transform = "rotate(45deg) translate(5px, 5px)";
        spans[1].style.opacity = "0";
        spans[2].style.transform = "rotate(-45deg) translate(7px, -6px)";
      } else {
        spans[0].style.transform = "none";
        spans[1].style.opacity = "1";
        spans[2].style.transform = "none";
      }
    });

    // Close menu when clicking outside
    document.addEventListener("click", function (event) {
      if (
        !mobileToggle.contains(event.target) &&
        !navMenu.contains(event.target)
      ) {
        navMenu.classList.remove("active");
        mobileToggle.classList.remove("active");

        const spans = mobileToggle.querySelectorAll("span");
        spans[0].style.transform = "none";
        spans[1].style.opacity = "1";
        spans[2].style.transform = "none";
      }
    });
  }
});

// Smooth Scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    const href = this.getAttribute("href");
    if (href !== "#" && document.querySelector(href)) {
      e.preventDefault();
      const target = document.querySelector(href);
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});

// Scroll to Top Button
function createScrollToTop() {
  const scrollBtn = document.createElement("button");
  //   scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
  scrollBtn.className = "scroll-to-top";
  scrollBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    `;

  document.body.appendChild(scrollBtn);

  // Show/hide button based on scroll position
  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      scrollBtn.style.opacity = "1";
      scrollBtn.style.visibility = "visible";
    } else {
      scrollBtn.style.opacity = "0";
      scrollBtn.style.visibility = "hidden";
    }
  });

  // Scroll to top on click
  scrollBtn.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

  // Hover effect
  scrollBtn.addEventListener("mouseenter", function () {
    this.style.transform = "translateY(-5px)";
  });

  scrollBtn.addEventListener("mouseleave", function () {
    this.style.transform = "translateY(0)";
  });
}

createScrollToTop();

// Newsletter Form Submission
const newsletterForm = document.querySelector(".newsletter-form");
if (newsletterForm) {
  newsletterForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;

    // Simulate API call
    const button = this.querySelector("button");
    const originalText = button.textContent;
    button.textContent = "Đang xử lý...";
    button.disabled = true;

    setTimeout(() => {
      alert(`Cảm ơn bạn đã đăng ký! Email: ${email}`);
      this.reset();
      button.textContent = originalText;
      button.disabled = false;
    }, 1000);
  });
}

// Search Form Enhancement
const searchForm = document.querySelector(".search-form");
if (searchForm) {
  const searchInput = searchForm.querySelector('input[type="text"]');

  // Add loading state on form submit
  searchForm.addEventListener("submit", function () {
    const button = this.querySelector("button");
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  });

  // Auto-focus on search input when clicked anywhere in the search widget
  const searchWidget = document.querySelector(".search-widget");
  if (searchWidget) {
    searchWidget.addEventListener("click", function (e) {
      if (e.target !== searchInput) {
        searchInput.focus();
      }
    });
  }
}

// Lazy Loading Images
function lazyLoadImages() {
  const images = document.querySelectorAll("img[data-src]");

  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.removeAttribute("data-src");
        observer.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));
}

// Check if IntersectionObserver is supported
if ("IntersectionObserver" in window) {
  lazyLoadImages();
}

// Animate elements on scroll
function animateOnScroll() {
  const elements = document.querySelectorAll(".post-card, .widget");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "0";
          entry.target.style.transform = "translateY(20px)";

          setTimeout(() => {
            entry.target.style.transition =
              "opacity 0.6s ease, transform 0.6s ease";
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
          }, 100);

          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.1,
    }
  );

  elements.forEach((el) => observer.observe(el));
}

// Run animation if supported
if ("IntersectionObserver" in window) {
  animateOnScroll();
}

// Add active class to current page in navigation
function setActiveNavLink() {
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll(".nav-menu a");

  navLinks.forEach((link) => {
    const linkPath = new URL(link.href).pathname;
    if (linkPath === currentPath) {
      link.classList.add("active");
    }
  });
}

setActiveNavLink();

// Reading time calculator
function calculateReadingTime() {
  const posts = document.querySelectorAll(".post-card");

  posts.forEach((post) => {
    const content = post.querySelector(".post-excerpt");
    if (content) {
      const text = content.textContent;
      const wordsPerMinute = 200;
      const words = text.trim().split(/\s+/).length;
      const readingTime = Math.ceil(words / wordsPerMinute);

      // Add reading time badge
      const badge = document.createElement("span");
      badge.className = "reading-time";
      badge.innerHTML = `<i class="far fa-clock"></i> ${readingTime} phút đọc`;
      badge.style.cssText = `
                font-size: 0.875rem;
                color: var(--text-light);
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
            `;

      const postMeta = post.querySelector(".post-meta .post-stats");
      if (postMeta && !postMeta.querySelector(".reading-time")) {
        postMeta.appendChild(badge);
      }
    }
  });
}

calculateReadingTime();

// Handle post card click (mobile friendly)
function handlePostCardClick() {
  if (window.innerWidth <= 768) {
    document.querySelectorAll(".post-card").forEach((card) => {
      card.addEventListener("click", function (e) {
        // Only trigger if not clicking on a link
        if (e.target.tagName !== "A") {
          const link = this.querySelector(".post-title a");
          if (link) {
            window.location.href = link.href;
          }
        }
      });
    });
  }
}

handlePostCardClick();

// Copy link functionality
function addCopyLinkButtons() {
  document.querySelectorAll(".post-card").forEach((card) => {
    const postLink = card.querySelector(".post-title a");
    if (postLink) {
      const copyBtn = document.createElement("button");
      copyBtn.innerHTML = '<i class="fas fa-link"></i>';
      copyBtn.className = "copy-link-btn";
      copyBtn.title = "Sao chép liên kết";
      copyBtn.style.cssText = `
                position: absolute;
                top: 1rem;
                right: 1rem;
                width: 35px;
                height: 35px;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: all 0.3s ease;
                z-index: 10;
            `;

      card.querySelector(".post-image").style.position = "relative";
      card.querySelector(".post-image").appendChild(copyBtn);

      card.addEventListener("mouseenter", () => {
        copyBtn.style.opacity = "1";
      });

      card.addEventListener("mouseleave", () => {
        copyBtn.style.opacity = "0";
      });

      copyBtn.addEventListener("click", async (e) => {
        e.preventDefault();
        e.stopPropagation();

        try {
          await navigator.clipboard.writeText(postLink.href);
          copyBtn.innerHTML = '<i class="fas fa-check"></i>';
          copyBtn.style.background = "#10B981";
          copyBtn.style.color = "white";

          setTimeout(() => {
            copyBtn.innerHTML = '<i class="fas fa-link"></i>';
            copyBtn.style.background = "rgba(255, 255, 255, 0.9)";
            copyBtn.style.color = "";
          }, 2000);
        } catch (err) {
          console.error("Failed to copy:", err);
        }
      });
    }
  });
}

addCopyLinkButtons();
