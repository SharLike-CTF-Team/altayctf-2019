<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>">

    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <script src="<?php echo e(asset('js/scripts.js')); ?>"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title> InGalaxy - <?php echo $__env->yieldContent('title'); ?> </title>
</head>
<body>
<?php echo $__env->yieldContent('content'); ?>
</body>
</html>
