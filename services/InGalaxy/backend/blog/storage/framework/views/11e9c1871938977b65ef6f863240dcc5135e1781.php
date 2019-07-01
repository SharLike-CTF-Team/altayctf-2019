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
<body class="bg-welcome">
<header class="fixed-top">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-2 console p-0">
                <div class="row h-100 justify-content-center align-items-center">
                    <h2><a href="<?php echo e(route('news')); ?>">InGalaxy</a></h2>
                </div>
            </div>
            <div class="col-6 console p-0">
                <nav class="navbar navbar-expand-lg p-0">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center align-items-center"
                         id="navbarNavDropdown">
                        <ul class="navbar-nav justify-content-around w-100">
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('profile',['id'=>Auth::user()->id])); ?>">
                                    <button type="submit" class="btn button-console">Профиль</button>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('users')); ?>">
                                    <button type="submit" class="btn button-console">Пользователи</button>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('friends')); ?>">
                                    <button type="submit" class="btn button-console">Друзья</button>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('news')); ?>">
                                    <button type="submit" class="btn button-console">Галолента</button>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('messages')); ?>">
                                    <button type="submit" class="btn button-console">Сообщения</button>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
            <div class="col-2 console p-0">
                <div class="row justify-content-between align-items-center h-100 w-100 ml-0">
                    <div class="col-6 p-0">
                        <a href="<?php echo e(route('profile',['id'=>Auth::user()->id])); ?>">
                            <?php if(!empty(Auth::user()->name)): ?>
                                <?php echo e(Auth::user()->name); ?> <?php echo e(Auth::user()->surname); ?>

                            <?php else: ?>
                                <?php echo e(Auth::user()->login); ?>

                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="col-6 p-0">
                        <a class="nav-link dropdown-toggle" data-toggle="collapse" href="#dropdown_top" role="button"
                           aria-expanded="false" aria-controls="dropdown_top">
                            <img src="/<?php echo e(Auth::user()->avatar); ?>" class="rounded z-depth-4 button-console" width="30"
                                 height="30">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-end align-items-center">
            <div class="col-2 collapse console" id="dropdown_top">
                <a class="btn button-console w-100 mb-3" href="<?php echo e(route('account')); ?>">
                    Редактировать</a>
                <a class="btn button-console w-100" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    Выход</a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                </form>
            </div>
        </div>
    </div>
</header>
<?php if(Route::current()->getName() == "messages"): ?>
    <div class="container-fluid main-content">
<?php else: ?>
    <div class="container main-content">
<?php endif; ?>
    <?php echo $__env->yieldContent('content'); ?>

    <div id="top" class="button-console">
        Наверх
    </div>
</div>
</body>
</html>
