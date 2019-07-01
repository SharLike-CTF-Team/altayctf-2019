<div class="container mb-5">
    <div class="row justify-content-center console">
        <div class="col-8">
            <h2>Написать на стене</h2>
            <form id="addNews" class="mt-3" action="<?php echo e(route ('profile',['id'=>$user->id])); ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <textarea class="form-control input" id="text" rows="5" name="text"><?php echo e(old('text')); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-6">
                        <label for="customFile">Загрузите картинку</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="file">
                            <label class="custom-file-label input" for="customFile">Выберите файл</label>
                            <small class="text-help"> jpg,png</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <br>
                        <button type="submit" class="btn button-console button-long">Написать</button>
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
    </div>
</div>