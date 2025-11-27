<?php


?>

<header class="header" id="header">
    <div class="header-container">
        <a href="<?php echo Router::url('/'); ?>" class="logo">
            <img src="<?php echo Router::url('uploads/logo.png'); ?>"
                alt="BlogIT"
                class="logo__image" />

            <span class="logo__text" style="display:none;">BlogIT</span>
        </a>

        <nav>
            <ul class="nav-menu" id="navMenu">
                <li>
                    <a href="<?php echo Router::url('/'); ?>" class="<?php echo ($_SERVER['REQUEST_URI'] == '/personal-blog/' || $_SERVER['REQUEST_URI'] == '/personal-blog') ? 'active' : ''; ?>">
                        Trang chủ
                    </a>
                </li>
                <li>
                    <a href="<?php echo Router::url('/category'); ?>">
                        Danh mục
                    </a>
                </li>
                <li>
                    <a href="<?php echo Router::url('/about'); ?>">
                        Về chúng tôi
                    </a>
                </li>

                <?php if (Session::isLoggedIn()): ?>


                    <li>
                        <a href="<?php echo Router::url('/profile'); ?>">
                            Hồ sơ
                        </a>
                    </li>


                    <?php if (Session::isAdmin()): ?>
                        <li>
                            <a href="<?php echo Router::url('/admin'); ?>" class="admin-link">
                                <i class="fas fa-dashboard"></i> Quản trị
                            </a>
                        </li>
                    <?php endif; ?>


                    <li class="user-menu">
                        <div class="user-avatar" title="<?php
                                                        $userData = Session::getUserData();
                                                        $userName = $userData['name'] ?? $userData['email'] ?? 'User';
                                                        echo htmlspecialchars($userName);
                                                        ?>">
                            <?php
                            // Hiển thị chữ cái đầu của tên hoặc email
                            $initial = mb_substr($userName, 0, 1);
                            echo htmlspecialchars(mb_strtoupper($initial));
                            ?>
                        </div>
                        <a href="<?php echo Router::url('/logout'); ?>">
                            Đăng xuất
                        </a>
                    </li>

                <?php else: ?>
                    <!-- Guest menu -->
                    <li>
                        <a href="<?php echo Router::url('/login'); ?>" class="btn btn-outline">
                            Đăng nhập
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo Router::url('/register'); ?>" class="btn btn-primary">
                            Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>


        <button class="mobile-toggle" onclick="toggleMenu()" aria-label="Toggle menu">
            <span id="menuIcon">☰</span>
        </button>
    </div>
</header>

<script>
    // Toggle mobile menu
    function toggleMenu() {
        const navMenu = document.getElementById('navMenu');
        const menuIcon = document.getElementById('menuIcon');

        navMenu.classList.toggle('active');

        // Change icon
        if (navMenu.classList.contains('active')) {
            menuIcon.textContent = '✕';
        } else {
            menuIcon.textContent = '☰';
        }
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const navMenu = document.getElementById('navMenu');
        const mobileToggle = document.querySelector('.mobile-toggle');
        const header = document.getElementById('header');

        if (!header.contains(event.target)) {
            navMenu.classList.remove('active');
            document.getElementById('menuIcon').textContent = '☰';
        }
    });

    // Header scroll effect
    let lastScroll = 0;
    const header = document.getElementById('header');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            header.classList.add('header--scrolled');
        } else {
            header.classList.remove('header--scrolled');
        }

        lastScroll = currentScroll;
    });

    // Active navigation link based on current page
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-menu a');

    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;

        // Exact match hoặc starts with (cho sub-pages)
        if (linkPath === currentPath) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
</script>