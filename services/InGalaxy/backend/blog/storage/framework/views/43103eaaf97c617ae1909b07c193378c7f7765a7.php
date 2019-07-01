<?php $__env->startSection('title', 'Пользователи'); ?>

<?php $__env->startSection('content'); ?>
    <div class="scroll">
        <?php if(!(empty($users))): ?>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 console mb-3">
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <a href="<?php echo e(route('profile',['id'=>$user->id])); ?>">
                                <div class="img-friends">
                                    <img src="/<?php echo e($user->avatar); ?>">
                                </div>
                            </a>
                        </div>
                        <div class="col-8">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <a href="<?php echo e(route('profile',['id'=>$user->id])); ?>">
                                        <h3>
                                            <?php if(!empty($user->name)): ?>
                                                <?php echo e($user->name); ?> <?php echo e($user->surname); ?>

                                            <?php else: ?>
                                                <?php echo e($user->login); ?>

                                            <?php endif; ?>
                                        </h3>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <?php if(!empty($user->race)): ?>
                                        <?php echo e($user->race); ?>

                                    <?php endif; ?>
                                    <?php if(!empty($user->birthday)): ?>
                                        <?php echo e($user->birthday); ?> <?php echo e(App\User::num2word($user->birthday)); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if(!empty($user->selfdescription)): ?>
                                <div class="row justify-content-between text-left">
                                    <p><?php echo e($user->selfdescription); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>