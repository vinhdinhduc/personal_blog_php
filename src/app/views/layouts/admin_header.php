<?php
$userData = Session::getUserData();
$firstName = $userData['first_name'] ?? '';
$lastName = $userData['last_name'] ?? '';
$fullName = trim($firstName . ' ' . $lastName) ?: 'Admin';
$email = $userData['email'] ?? '';
$avatar = $userData['avatar'] ?? '';

// Default avatar nếu không có
$defaultAvatar = Router::url('uploads/default.png');
$avatarUrl = !empty($avatar) ? htmlspecialchars($avatar) : $defaultAvatar;

// Lấy số lượng thông báo từ session
$pendingCommentsCount = Session::getPendingCommentsCount();
$unreadMessagesCount = Session::getUnreadMessagesCount();
?>
<header class="admin-header">
    <div class="header__left">
        <button class="header__toggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="header__search">
            <i class="fas fa-search header__search-icon"></i>
            <input type="text" class="header__search-input" placeholder="Tìm kiếm...">
        </div>
    </div>

    <div class="header__right">
        <!-- Notifications -->
        <?php if ($pendingCommentsCount > 0): ?>
            <div class="header__notification" title="Bình luận chờ duyệt">
                <a href="<?php echo Router::url('/admin/comments?status=pending'); ?>">
                    <i class="fas fa-bell header__notification-icon"></i>
                    <span class="header__notification-badge"><?php echo $pendingCommentsCount; ?></span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Messages -->
        <?php if ($unreadMessagesCount > 0): ?>
            <div class="header__notification" title="Tin nhắn mới">
                <i class="fas fa-envelope header__notification-icon"></i>
                <span class="header__notification-badge"><?php echo $unreadMessagesCount; ?></span>
            </div>
        <?php endif; ?>

        <!-- User Menu -->
        <div class="header__user dropdown">
            <div class="dropdown-toggle" data-toggle="dropdown" id="userDropdown">
                <img src="<?php echo $avatarUrl; ?>"
                    alt="<?php echo htmlspecialchars($fullName); ?>"
                    class="header__user-avatar"
                    onerror="this.onerror=null; this.src='<?php echo $defaultAvatar; ?>';">
                <span class="header__user-name"><?php echo htmlspecialchars($fullName); ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="userDropdownMenu">
                <div class="dropdown-header">
                    <strong><?php echo htmlspecialchars($fullName); ?></strong>
                    <small><?php echo htmlspecialchars($email); ?></small>
                </div>
                <a href="<?php echo Router::url('/admin/profile'); ?>" class="dropdown-item">
                    <i class="fas fa-user"></i> Hồ sơ
                </a>
                <a href="<?php echo Router::url('/admin/settings'); ?>" class="dropdown-item">
                    <i class="fas fa-cog"></i> Cài đặt
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo Router::url('/'); ?>" class="dropdown-item" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Xem website
                </a>
                <a href="<?php echo Router::url('/logout'); ?>" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>
</header>