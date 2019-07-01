@extends ('layouts.index')
@section('title', 'Вход')

@section('content')
<div class="container-fluid fullscreen bg-welcome align-items-center d-flex" id="welcome">
    <div class="container console h-50">
        <div class="row align-items-center mt-5 ml-5">
            <div class="col-6">
                <h3 class="hero-heading">Время покорения космоса уже прошло<br> Время быть на связи вечно </h3>
            </div>
            <div class="col-6">
                <h1 class="text-center">InGalaxy</h1>
            </div>
        </div>
        <div class="row justify-content-center mt-5 mr-3">
            <a href="#signin" id="toForms">
                <button type="submit" class="btn button-console button-long">Начать</button>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid fullscreen bg-welcome align-items-center d-flex" id="forms">
    <div class="container console">
        <div class="row">
            <div class="col-6">
                @include('auth.login_form')
            </div>
            <div class="col-6">
                <div class="col-8 h-100 mx-auto my-auto">
                    <h4> Нет аккаунта?</h4>
                    <div class="row h-75 justify-content-center align-items-center">
                        <div class="col">
                            <p><a href="{{ route('register') }}" class="btn button-console">Зарегистрироваться</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // скролл
    $(window).bind('mousewheel', function(event) {
        event.preventDefault();

        if (event.originalEvent.wheelDelta >= 0) {
            var top = $("#welcome").offset().top;
            $('body,html').animate({scrollTop: top}, timeToScroll);
        }
        else {
            var top = $("#forms").offset().top;
            $('body,html').animate({scrollTop: top}, timeToScroll);
        }

        $('#signin').find('#login').focus();
    });

    // кнопка
    $(document).ready(function(){
        $("#welcome").on("click","a", function (event) {
            //отменяем стандартную обработку нажатия по ссылке
            event.preventDefault();

            //забираем идентификатор бока с атрибута href
            var id  = $(this).attr('href'),
                //узнаем высоту от начала страницы до блока на который ссылается якорь
                top = $(id).offset().top;

            $('body,html').animate({scrollTop: top}, timeToScroll);
            $('#signin').find('#login').focus();
        });
    });
</script>
@endsection