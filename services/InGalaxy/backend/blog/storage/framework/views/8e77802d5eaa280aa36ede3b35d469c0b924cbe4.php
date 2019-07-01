<?php $__env->startSection('title', $user->login); ?>

<?php $__env->startSection('content'); ?>
    <div class="col-12 console mb-5">
        <div class="row justify-content-end mb-5">
            <div class="col-4">
                <a href="<?php echo e(route ('profile',['id'=>$user->id])); ?>">
                    <div class="img-friends">
                        <img src="/<?php echo e($user->avatar); ?>">
                    </div>
                </a>
            </div>
            <div class="col-8 pt-5">
                <div class="row justify-content-between text-left">
                    <div class="col profile-info">
                        <p>
                            <?php if(!empty($user->name) || !empty($user->surname)): ?>
                                <?php echo e($user->name); ?> <?php echo e($user->surname); ?> aka <b><?php echo e($user->login); ?></b>
                            <?php else: ?>
                                <b><?php echo e($user->login); ?></b>
                            <?php endif; ?>
                        </p>

                        <?php if(!empty($user->gender) || !empty($user->birthday)): ?>
                            <p>
                                <?php if(!empty($user->gender)): ?>
                                    <?php echo e($user->gender); ?>

                                <?php endif; ?>
                                <?php if(!empty($user->birthday)): ?>
                                    <?php echo e($user->birthday); ?> <?php echo e(App\User::num2word($user->birthday)); ?>

                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <?php if(!empty($user->homeplace)): ?>
                            <p>
                                Дом: <?php echo e($user->homeplace); ?>

                            </p>
                        <?php endif; ?>
                        <?php if(!empty($user->selfdescription)): ?>
                            <p><?php echo e($user->selfdescription); ?></p>
                        <?php endif; ?>
                        <p>Друзей: <?php echo e($friends_count); ?></p>

                        <?php if(count($errors) > 0): ?>
                            <div class="text-error">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <strong><?php echo e($error); ?></strong>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-3">
                <form action="<?php echo e(route('addMessage')); ?>" method="POST">
                    <input type="hidden" value="<?php echo e($user->id); ?>" name="user_id">
                    <input class="btn button-console" type="submit" value="Написать сообщение">
                </form>
            </div>
            <?php
                use App\User;
            ?>
            <?php if(Auth::user()->id!==$user->id): ?>
                <div class="col-3">
                    <?php
                        $owner = User::findOrFail(Auth::user()->id);
                        $recipient = User::findOrFail($user->id);
                        if($owner->checkFriendship($recipient)):
                    ?>
                    <form action="<?php echo e(route('removeFriend')); ?>" method="POST">
                        <input type="hidden" value="<?php echo e($user->id); ?>" name="user_id">
                        <input class="btn button-console" type="submit" value="Удалить из друзей">
                    </form>
                    <?php
                        else:
                    ?>
                    <form action="<?php echo e(route('addFriend')); ?>" method="POST">
                        <input type="hidden" value="<?php echo e($user->id); ?>" name="user_id">
                        <input class="btn button-console" type="submit" value="Добавить в друзья">
                    </form>
                    <?php
                        endif;
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $__env->make('auth.addPostInWall_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php if(!empty($posts)): ?>
        <div class="scroll mb-3" style="max-height: 70vh">
            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="row justify-content-center mb-5">
                    <div class="card console w-75">

                        <div class="row pr-5 justify-content-end">
                            <?php echo e($post->created_at); ?>

                        </div>
                        <!-- user start-->
                        <div class="row  justify-content-left">
                            <div class="col-3">
                                <div class="row justify-content-center">
                                    <a href="<?php echo e(route('profile',['id'=>$post->id_owner])); ?>">
                                        <div class="img-user">
                                            <img src="/<?php echo e(App\User::find($post->id_owner)->avatar); ?>">
                                        </div>
                                    </a>
                                </div>
                                <div class="row justify-content-center">
                                    <a href="<?php echo e(route('profile',['id'=>$post->id_owner])); ?>">
                                        <h5 class="card-header">
                                            <?php if(!empty($name=App\User::find($post->id_owner)->name)): ?>
                                                <?php echo e($name); ?> <?php echo e(App\User::find($post->id_owner)->surname); ?>

                                            <?php else: ?>
                                                <?php echo e(App\User::find($post->id_owner)->login); ?>

                                            <?php endif; ?>
                                        </h5></a>
                                </div>
                                <!-- user end-->
                            </div>
                            <div class="col-9">
                                <div class="card-body text-left">
                                    <p class="card-text"><?php echo e($post->text); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($post->image)): ?>
                            <div class="row">
                                <div class="img-card">
                                    <img src="/<?php echo e($post->image); ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>