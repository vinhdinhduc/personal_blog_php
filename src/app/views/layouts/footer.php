<?php


?>

<footer class="footer">
    <div class="footer__container">
        <!-- Footer Content -->
        <div class="footer__content">
            <!-- Brand Section -->
            <div class="footer__brand">
                <div class="footer__logo">BlogIT</div>
                <p class="footer__description">
                    Chia sẻ kiến thức, trải nghiệm và những câu chuyện thú vị về công nghệ,
                    lập trình và cuộc sống. Cùng nhau xây dựng cộng đồng học tập và phát triển.
                </p>
                <div class="footer__social">
                    <a href="#" class="footer__social-link" aria-label="Facebook" title="Facebook">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                        </svg>
                    </a>
                    <a href="#" class="footer__social-link" aria-label="Twitter" title="Twitter">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" />
                        </svg>
                    </a>
                    <a href="#" class="footer__social-link" aria-label="GitHub" title="GitHub">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 00-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0020 4.77 5.07 5.07 0 0019.91 1S18.73.65 16 2.48a13.38 13.38 0 00-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 005 4.77a5.44 5.44 0 00-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 009 18.13V22" />
                        </svg>
                    </a>
                    <a href="#" class="footer__social-link" aria-label="LinkedIn" title="LinkedIn">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z" />
                            <circle cx="4" cy="4" r="2" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer__section">
                <h3 class="footer__title">Liên kết nhanh</h3>
                <div class="footer__links">
                    <a href="<?php echo Router::url('/'); ?>" class="footer__link">Trang chủ</a>
                    <a href="<?php echo Router::url('/category'); ?>" class="footer__link">Danh mục</a>
                    <a href="<?php echo Router::url('/about'); ?>" class="footer__link">Về chúng tôi</a>
                    <a href="<?php echo Router::url('/contact'); ?>" class="footer__link">Liên hệ</a>
                </div>
            </div>

            <!-- Categories -->
            <div class="footer__section">
                <h3 class="footer__title">Danh mục</h3>
                <div class="footer__links">
                    <a href="<?php echo Router::url('/category/technology'); ?>" class="footer__link">Công nghệ</a>
                    <a href="<?php echo Router::url('/category/programming'); ?>" class="footer__link">Lập trình</a>
                    <a href="<?php echo Router::url('/category/design'); ?>" class="footer__link">Thiết kế</a>
                    <a href="<?php echo Router::url('/category/lifestyle'); ?>" class="footer__link">Cuộc sống</a>
                </div>
            </div>

            <!-- Resources -->
            <div class="footer__section">
                <h3 class="footer__title">Tài nguyên</h3>
                <div class="footer__links">
                    <a href="<?php echo Router::url('/blog'); ?>" class="footer__link">Blog</a>
                    <a href="<?php echo Router::url('/tutorials'); ?>" class="footer__link">Hướng dẫn</a>
                    <a href="<?php echo Router::url('/faq'); ?>" class="footer__link">FAQ</a>
                    <a href="<?php echo Router::url('/support'); ?>" class="footer__link">Hỗ trợ</a>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; <?php echo date('Y'); ?> BlogIT. Phát triển bởi BlogIT Group 4 K63CNTT-A.
            </p>
            <div class="footer__links-bottom">
                <a href="<?php echo Router::url('/privacy'); ?>" class="footer__link-bottom">Chính sách bảo mật</a>
                <a href="<?php echo Router::url('/terms'); ?>" class="footer__link-bottom">Điều khoản sử dụng</a>
                <a href="<?php echo Router::url('/cookies'); ?>" class="footer__link-bottom">Cookie</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Smooth scroll to top
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Show "Back to top" button when scrolling
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '↑';
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.onclick = scrollToTop;
    document.body.appendChild(backToTopBtn);

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
</script>

<style>
    /* Back to Top Button */
    .back-to-top {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 999;
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
    }

    @media (max-width: 768px) {
        .back-to-top {
            bottom: 1rem;
            right: 1rem;
            width: 45px;
            height: 45px;
            font-size: 1.3rem;
        }
    }
</style>