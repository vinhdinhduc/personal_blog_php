<aside class="admin-sidebar">
    <!-- Brand/Logo -->
    <div class="sidebar__brand">
        <a href=<?php echo Router::url('/admin/dashboard'); ?> class="sidebar__brand-logo">
            <i class="fas fa-cog"></i>
            <span class="sidebar__brand-text">Admin Panel</span>
        </a>
    </div>

    <!-- Navigation Menu -->
    <nav>
        <ul class="sidebar__menu">
            <!-- Dashboard -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/dashboard'); ?> class="sidebar__menu-link">
                    <i class="fas fa-tachometer-alt sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Dashboard</span>
                </a>
            </li>

            <!-- Posts Management -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/posts'); ?> class="sidebar__menu-link">
                    <i class="fas fa-newspaper sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Bài viết</span>
                </a>
            </li>

            <!-- Add New Post -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/posts/add'); ?> class="sidebar__menu-link">
                    <i class="fas fa-plus-circle sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Thêm bài viết</span>
                </a>
            </li>

            <!-- Users Management -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/users'); ?> class="sidebar__menu-link">
                    <i class="fas fa-users sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Người dùng</span>
                </a>
            </li>

            <!-- Comments Management -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/comments'); ?> class="sidebar__menu-link">
                    <i class="fas fa-comments sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Bình luận</span>
                </a>
            </li>

            <!-- Categories -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/categories'); ?> class="sidebar__menu-link">
                    <i class="fas fa-folder sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Danh mục</span>
                </a>
            </li>

            <!-- Media -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/media'); ?> class="sidebar__menu-link">
                    <i class="fas fa-images sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Thư viện</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="sidebar__menu-item">
                <a href=<?php echo Router::url('/admin/settings'); ?> class="sidebar__menu-link">
                    <i class="fas fa-cog sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Cài đặt</span>
                </a>
            </li>

            <!-- Divider -->
            <li class="sidebar__menu-item" style="height: 1px; background: rgba(255,255,255,0.1); margin: 15px 20px;"></li>

            <!-- View Site -->
            <li class="sidebar__menu-item">
                <a href="/" target="_blank" class="sidebar__menu-link">
                    <i class="fas fa-external-link-alt sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Xem website</span>
                </a>
            </li>

            <!-- Logout -->
            <li class="sidebar__menu-item">
                <a href="/admin/logout" class="sidebar__menu-link">
                    <i class="fas fa-sign-out-alt sidebar__menu-icon"></i>
                    <span class="sidebar__menu-text">Đăng xuất</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>