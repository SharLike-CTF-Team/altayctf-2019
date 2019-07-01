<?php $__env->startSection('title', 'Галолента'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row justify-content-center mb-5" id="toAddNews">
        <a href="#addNews">
            <button class="btn button-console button-long">Есть что сказать?</button>
        </a>
    </div>

    <?php if(!empty($posts)): ?>
        <div class="scroll mb-3" style="max-height: 70vh">
            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="row justify-content-center mb-3">
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
    <?php else: ?>
        <div class="row justify-content-center mb-5">
            <div class="card console w-75">
                <h2>Постов еще нет. Будете первым?</h2>
            </div>
        </div>
    <?php endif; ?>

    <?php echo $__env->make('auth.addPost_form', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <script>
        // кнопка
        $(document).ready(function () {
            $("#toAddNews").on("click", "a", function (event) {
                //отменяем стандартную обработку нажатия по ссылке
                event.preventDefault();

                //забираем идентификатор бока с атрибута href
                var id = $(this).attr('href'),
                    //узнаем высоту от начала страницы до блока на который ссылается якорь
                    top = $(id).offset().top;

                $('body,html').animate({scrollTop: top}, timeToScroll);
                $('#addNews').find('textarea').focus();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>