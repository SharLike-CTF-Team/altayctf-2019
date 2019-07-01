
<?php $__env->startSection('title', 'Регистрация'); ?>

<?php echo $__env->make('auth.register_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('layouts.index', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>