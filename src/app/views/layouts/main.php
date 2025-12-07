<!DOCTYPE html><!---->
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="48x48" href="<?php echo Router::url('/favicon.png'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'BlogIT'; ?></title>


    <link rel="stylesheet" href="<?php echo Router::url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/components.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/toast.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/users/header_footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/users/home.css'); ?>">

    <?php
    $currentPath = $_SERVER['REQUEST_URI'] ?? '';

    if (strpos($currentPath, '/') === 0 && strlen($currentPath) <= 1 || strpos($currentPath, '/page/') !== false):
    ?>
    <?php endif; ?>

    <?php
    if (
        strpos($currentPath, '/login') !== false || strpos($currentPath, '/register') !== false ||
        strpos($currentPath, '/forgot-password') !== false || strpos($currentPath, '/reset-password') !== false
    ):
    ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/users/auth.css'); ?>">
    <?php endif; ?>

    <?php // Post detail page
    if (strpos($currentPath, '/post/') !== false):
    ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/users/post-detail.css'); ?>">
        <link rel="stylesheet" href="<?php echo Router::url('css/users/comments.css'); ?>">
    <?php endif; ?>

    <?php // About page
    if (strpos($currentPath, '/about') !== false):
    ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/users/about.css'); ?>">
    <?php endif; ?>

    <?php // Profile page
    if (strpos($currentPath, '/profile') !== false):
    ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/users/profile.css'); ?>">
    <?php endif; ?>

    <?php // Category page
    if (strpos($currentPath, '/category') !== false):
    ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/users/user-category.css'); ?>">
    <?php endif; ?>

    <?php // Tag page
    if (strpos($currentPath, '/tag') !== false):
    ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/users/tag.css'); ?>">
    <?php endif; ?>

    <?php // All posts & Search pages
    if (strpos($currentPath, '/posts') !== false || strpos($currentPath, '/search') !== false):
    ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/users/post-all-search.css'); ?>">
    <?php endif; ?>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <!-- Critical JS - Load on all pages -->
    <script src="<?php echo Router::url('js/toast.js'); ?>"></script>
    <script src="<?php echo Router::url('/js/main.js'); ?>"></script>

    <?php // Conditional JS loading
    if (strpos($currentPath, '/post/') !== false):
    ?>
        <script src="<?php echo Router::url('/js/post-detail.js'); ?>"></script>
        <script src="<?php echo Router::url('/js/comments.js'); ?>"></script>
    <?php endif; ?>

    <?php if (strpos($currentPath, '/profile') !== false):
    ?>
        <script src="<?php echo Router::url('/js/profile.js'); ?>"></script>
    <?php endif; ?>

    <?php include __DIR__ . '/../partials/toast.php'; ?>
</body>

</html>