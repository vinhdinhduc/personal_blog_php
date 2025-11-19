<?php

/**
 * Admin Layout Template
 * File: app/views/layouts/admin.php
 * 
 * Layout chính cho trang admin
 * Bao gồm: Sidebar, Topbar, Content Area
 * 
 * Variables cần truyền vào:
 * - $pageTitle: Tiêu đề trang
 * - $content: Nội dung trang (được render từ view)
 * - $activeMenu: Menu item đang active (dashboard, posts, users, etc.)
 */

// Lấy thông tin user từ session
$currentUser = [
    'name' => $_SESSION['user_data']['name'] ?? 'Admin User',
    'email' => $_SESSION['user_data']['email'] ?? 'admin@blog.com',
    'avatar' => substr($_SESSION['user_data']['name'] ?? 'AD', 0, 2)
];

// Active menu item
$activeMenu = $activeMenu ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Dashboard'); ?> - Blog Admin</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo Router::url('public/css/admin.css'); ?>">

    <!-- Page Specific CSS (if any) -->
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>

    <!-- ========================================
         SIDEBAR NAVIGATION
    ======================================== -->
    <?php include __DIR__ . '/../admin/partials/sidebar.php'; ?>

    <!-- ========================================
         TOPBAR HEADER
    ======================================== -->
    <?php include __DIR__ . '/../admin/partials/topbar.php'; ?>

    <!-- ========================================
         MAIN CONTENT AREA
    ======================================== -->
    <div class="main-wrapper">
        <main class="main-content" id="main-content">

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Page Content -->
            <?php echo $content; ?>

        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Admin JS -->
    <script src="<?php echo Router::url('public/js/admin.js'); ?>"></script>

    <!-- Page Specific JS (if any) -->
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>