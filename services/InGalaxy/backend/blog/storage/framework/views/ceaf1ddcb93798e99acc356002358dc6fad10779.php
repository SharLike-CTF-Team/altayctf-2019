<div class="col-8 mx-auto">
    <h4> Есть аккаунт</h4>
    <form id="signin" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="login">Логин</label>
            <input name="login" type="text" class="form-control" id="login" placeholder="Ваш логин" value="<?php echo e(old('login')); ?>">
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Ваш пароль">
        </div>
        <button type="submit" class="btn button-console">Войти</button>
    </form>
    <?php if($errors->has('login')): ?>
        <div class="text-error">
            <p><?php echo e($errors->first('login')); ?></p>
        </div>
    <?php endif; ?>
</div>
