<div class="container-fluid bg-welcome align-items-center" id="forms">
    <div class="container console">
        <div class="row">
            <div class="col-12">
                <h2> Регистрация </h2>
                <form id="register" class="mt-3" method="POST" action="<?php echo e(route('register')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-4">
                            <h4> Обязательно </h4>
                            <div class="form-group">
                                <label for="login">Логин</label>
                                <input type="text" class="form-control" id="login" name="login" placeholder="Ваш логин"
                                       value="<?php echo e(old('login')); ?>" required>
                                <small class="text-help"> Он же ник, который увидят другие пользователи</small>
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Ваш пароль" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Повторите пароль</label>
                                <input type="password" class="form-control" id="repeat-password"
                                       placeholder="Ваш пароль" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4> Уточняем </h4>
                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ваше имя"
                                       value="<?php echo e(old('name')); ?>">
                            </div>
                            <div class="form-group">
                                <label for="surname">Фамилия</label>
                                <input type="text" class="form-control" id="surname" name="surname"
                                       placeholder="Ваша фамилия" value="<?php echo e(old('surname')); ?>">
                            </div>
                            <div class="form-group">
                                <label for="race">Раса</label>
                                <input type="text" class="form-control" id="race" name="race" placeholder="Ваша раса"
                                       value="<?php echo e(old('race')); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gender">Пол</label>
                                <input type="text" class="form-control" id="gender" name="gender" placeholder="Ваш пол"
                                       value="<?php echo e(old('gender')); ?>">
                                <small class="text-help"> Если имеется</small>
                            </div>
                            <div class="form-group">
                                <label for="birthday">Дата рождения</label>
                                <input type="date" class="form-control input" id="birthday" name="birthday"
                                       placeholder="Дата рождения" value="<?php echo e(old('birthday')); ?>">
                            </div>
                            <div class="form-group">
                                <label for="homeplace">Место проживания</label>
                                <input type="text" class="form-control input" id="homeplace" name="homeplace"
                                       placeholder="Планета\Система" value="<?php echo e(old('homeplace')); ?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <h4> Аватар </h4>
                            <label for="customFile">Загрузите картинку</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="file">
                                <label class="custom-file-label input" for="customFile">Выберите файл</label>
                                <small class="text-help">jpg,png</small>
                            </div>

                            <h4 class="mt-5"> О себе </h4>
                            <label for="selfdescription">Расскажите ваши увлечения, хобби.<br> Что-нибудь, о чем вы
                                хотите рассказать другим</label>
                            <div class="custom-file">
                                <textarea class="form-control input" id="selfdescription" name="selfdescription" rows="5" value="<?php echo e(old('selfdescription')); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-5">
                        <button type="submit" class="btn button-console button-long">Регистрация</button>
                    </div>
                </form>
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