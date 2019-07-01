<?php $__env->startSection('title', 'Диалоги'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-between">
        <div class="col-3 console mt-5" id="message_objects">
            <?php if(count($dialogs)>0): ?>
                <?php $__currentLoopData = $dialogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dialog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="row justify-content-between align-items-center w-100 ml-0 button-console mb-3
                    <?php if (!empty($current_dialog) && $dialog->id === $current_dialog->id) echo "button-active"?> message">
                        <div class="col-6 p-0">
                            <a href="/messages/<?php echo e($dialog->slug); ?>">
                                <?php echo e($users[$dialog->id]["name"]); ?>

                            </a>
                        </div>
                        <div class="col-6 p-0">
                            <a class="nav-link">
                                <div class="img-message">
                                    <img src="/<?php echo e($users[$dialog->id]["avatar"]); ?>">
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="row justify-content-between align-items-center w-100 ml-0 button-console button-active message">
                    <div class="col pt-2">
                        <h3>Нет диалогов</h3>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <div class="col-8 console mt-5">
            <?php if(!empty($messages)): ?>
                <div class="dialog">
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php

                            if(Auth::user()->id === $message->id_owner)
                                $user = Auth::user()->toArray();
                            elseif(!empty($users[$message->id_dialogs]))
                                $user = $users[$message->id_dialogs];
                            else $user = Auth::user()->toArray();
                        ?>
                        <div class="container button-console m-2">
                            <div class="row pr-5 justify-content-end">
                                <?php echo e($message->created_at); ?>

                            </div>
                            <div class="row justify-content-start align-items-center">
                                <div class="col-3">
                                    <a href="<?php echo e(route("profile",["id"=>$user["id"]])); ?>">
                                        <div class="img-message">
                                            <img src="/<?php echo e($user["avatar"]); ?>">
                                        </div>
                                        <p>
                                            <?php if(!empty($user["name"])): ?>
                                                <?php echo e($user["name"]); ?> <?php echo e(isset($user["surname"])?$user["surname"]:""); ?>

                                            <?php else: ?>
                                                <?php echo e($user["login"]); ?>

                                            <?php endif; ?>
                                        </p>
                                    </a>
                                </div>
                                <div class="col text-left">
                                    <?php echo e($message->text); ?>

                                </div>
                            </div>
                            <?php if(!empty($message->image)): ?>
                                <div class="row justify-content-start mt-3">
                                    <div class="img-card">
                                        <img src="/<?php echo e($message->image); ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
            <?php if(!empty($current_dialog)): ?>
                <div class="container">
                    <form id="addNews" class="mt-3" action="<?php echo e(route('addMessage')); ?>" method="POST"
                          enctype="multipart/form-data">
                        <?php if(!empty($users[$current_dialog->id]["id"])): ?>
                            <input type="hidden" value="<?php echo e($users[$current_dialog->id]["id"]); ?>" name="user_id">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                <textarea class="form-control input" id="selfdescription" rows="5"
                                          placeholder="Ваше сообщение" name="text"><?php echo e(old("text")); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <label for="customFile">Загрузите картинку</label>
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="customFile">
                                    <label class="custom-file-label input" for="customFile">Выберите
                                        файл</label>
                                    <small class="text-help"> jpg,png до 10мб</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <br>
                                <button type="submit" class="btn button-console button-long">Отправить</button>
                            </div>
                        </div>
                        <?php if(count($errors) > 0): ?>
                            <div class="text-error">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <strong><?php echo e($error); ?></strong>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
        </div>


    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>