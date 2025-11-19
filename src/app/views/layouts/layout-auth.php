<!DOCTYPE html><!---->
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>


    <link rel="stylesheet" href="<?php echo Router::url('css/components.css'); ?>">
    <link rel="stylesheet" href="<?php echo Router::url('css/auth.css'); ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <?php if (Session::has('success')): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars((string) Session::flash('success', "Ok"), ENT_QUOTES); ?>
        </div>
    <?php endif; ?>

    <?php if (Session::has('error')): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars((string) Session::flash('error', ""), ENT_QUOTES); ?>
        </div>
    <?php endif; ?>


    <?php echo $content ?? ''; ?>
</body>

</html>