<!DOCTYPE html><!---->
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'MyBlog'; ?></title>
    <link rel="stylesheet" href="<?php echo Router::url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/components.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/auth.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/header_footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/home.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/toast.css'); ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    <script src="<?php echo Router::url('js/toast.js'); ?>"></script>
    <script src="<?php echo Router::url('/js/main.js'); ?>"></script>

    <?php include __DIR__ . '/../partials/toast.php'; ?>
</body>

</html>