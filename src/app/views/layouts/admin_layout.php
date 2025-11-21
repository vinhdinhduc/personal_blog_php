<?php ?>

<!---->
<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - Admin Dashboard</title>
    <link rel="stylesheet" href="<?php echo Router::url('css/toast.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Router::url('css/admin.css'); ?>">

    <link rel="stylesheet" href="<?php echo Router::url('css/user_form.css'); ?>">
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
    <script src="<?php echo Router::url('js/toast.js'); ?>"></script>
    <script src="<?php echo Router::url('js/admin.js'); ?>"></script>
    <?php include __DIR__ . '/../partials/toast.php'; ?>


    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>






</body>

</html>