<?php
$needPostEdit = $needPostEdit ?? false;
$needComments = $needComments ?? false;
$needCategory = $needCategory ?? false;
$needUsers = $needUsers ?? false;
$needTags = $needTags ?? false;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - Admin Dashboard</title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo Router::url('/favicon.png'); ?>">

    <!-- Common CSS -->
    <link rel="stylesheet" href="<?php echo Router::url('css/toast.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Router::url('css/admin/admin.css'); ?>">

    <!-- Page-specific CSS -->
    <?php if ($needPostEdit): ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/admin/post-editor.css'); ?>">
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <?php endif; ?>

    <?php if ($needComments): ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/admin/comments.css'); ?>">
    <?php endif; ?>

    <?php if ($needCategory): ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/admin/category.css'); ?>">
    <?php endif; ?>

    <?php if ($needUsers): ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/admin/user_form.css'); ?>">
    <?php endif; ?>
    <?php if ($needTags): ?>
        <link rel="stylesheet" href="<?php echo Router::url('css/admin/tag.css'); ?>">
    <?php endif; ?>

    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>

<body>
    <div class="admin-wrapper">
        <?php require_once __DIR__ . '/admin_sidebar.php'; ?>
        <div class="admin-main">
            <?php require_once __DIR__ . '/admin_header.php'; ?>
            <main class="admin-content">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <!-- Common JS -->
    <script src="<?php echo Router::url('js/toast.js'); ?>"></script>
    <script src="<?php echo Router::url('js/admin.js'); ?>"></script>

    <!-- Page-specific JS -->
    <?php if ($needPostEdit): ?>
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script src="<?php echo Router::url('js/post-editor.js'); ?>"></script>
    <?php endif; ?>

    <?php if ($needComments): ?>
        <script src="<?php echo Router::url('js/admin-comment.js'); ?>"></script>
    <?php endif; ?>

    <?php if ($needCategory): ?>
        <script src="<?php echo Router::url('js/admin-category.js'); ?>"></script>
    <?php endif; ?>
    <?php if ($needTags): ?>
        <script src="<?php echo Router::url('js/admin-tags.js'); ?>"></script>
    <?php endif; ?>
    <?php include __DIR__ . '/../partials/toast.php'; ?>

    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>
</body>

</html>