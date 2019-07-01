<?php $__env->startSection('title', 'Друзья'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row mb-2 justify-content-center">
        <div class="col-2">
            <a href="#">
                <div id="btn_friends" class="button-console text-center button-active p-1">
                    Друзья
                </div>
            </a>
        </div>
        <div class="col-2">
            <a href="#">
                <div id="btn_requests" class="button-console text-center p-1">
                    Заявки
                </div>
            </a>
        </div>
    </div>
    <div id="friends" class="scroll">
        <?php if(!(empty($friends))): ?>
            <?php $__currentLoopData = $friends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $friend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 console mb-3">
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <a href="<?php echo e(route('profile',['id'=>$friend->id])); ?>">
                                <div class="img-friends">
                                    <img src="<?php echo e($friend->avatar); ?>">
                                </div>
                            </a>
                        </div>
                        <div class="col-8">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <a href="<?php echo e(route('profile',['id'=>$friend->id])); ?>">
                                        <h3>
                                            <?php if(!empty($friend->name)): ?>
                                                <?php echo e($friend->name); ?> <?php echo e($friend->surname); ?>

                                            <?php else: ?>
                                                <?php echo e($friend->login); ?>

                                            <?php endif; ?>
                                        </h3>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <?php if(!empty($friend->race)): ?>
                                        <?php echo e($friend->race); ?>

                                    <?php endif; ?>
                                    <?php if(!empty($friend->birthday)): ?>
                                        <?php echo e($friend->birthday); ?> <?php echo e(App\User::num2word($friend->birthday)); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if(!empty($friend->selfdescription)): ?>
                                <div class="row justify-content-between text-left">
                                    <p><?php echo e($friend->selfdescription); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-3">
                            <form action="<?php echo e(route('addMessage')); ?>" method="POST">
                                <input type="hidden" value="<?php echo e($friend->id); ?>" name="user_id">
                                <input class="btn button-console" type="submit" value="Написать сообщение">
                            </form>
                        </div>
                        <div class="col-3">
                            <form action="<?php echo e(route('removeFriend')); ?>" method="POST">
                                <input type="hidden" value="<?php echo e($friend->id); ?>" name="user_id">
                                <input class="btn button-console" type="submit" value="Удалить из друзей">
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-12 console mb-3">
                <h4>У вас еще нет друзей :(</h4>
            </div>
        <?php endif; ?>
    </div>
    <div id="requests" style="display: none" class="scroll h-25">
        <?php if(!(empty($requests))): ?>
            <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $onerequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 console mb-3">
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <a href="<?php echo e(route('profile',['id'=>$onerequest->id])); ?>">
                                <div class="img-friends">
                                    <img src="<?php echo e($onerequest->avatar); ?>">
                                </div>
                            </a>
                        </div>
                        <div class="col-8">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <h3>
                                        <?php if(!empty($onerequest->name)): ?>
                                            <?php echo e($onerequest->name); ?> <?php echo e($onerequest->surname); ?>

                                        <?php else: ?>
                                            <?php echo e($onerequest->login); ?>

                                        <?php endif; ?>
                                    </h3>
                                </div>
                                <div class="col-4">
                                    <?php if(!empty($onerequest->race)): ?>
                                        <?php echo e($onerequest->race); ?>

                                    <?php endif; ?>
                                    <?php if(!empty($onerequest->birthday)): ?>
                                        <?php echo e($onerequest->birthday); ?> <?php echo e(App\User::num2word($onerequest->birthday)); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if(!empty($onerequest->selfdescription)): ?>
                                <div class="row justify-content-between text-left">
                                    <p><?php echo e($onerequest->selfdescription); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-3">
                            <form action="<?php echo e(route('addFriend')); ?>" method="POST">
                                <input type="hidden" value="<?php echo e($onerequest->id); ?>" name="user_id">
                                <input class="btn button-console" type="submit" value="Добавить в друзья">
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-12 console mb-3">
                <h4>Нет новых заявок</h4>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>