@extends('layouts.main')
@section('title', "Ваш профиль")

@section('content')
    <form class="console" id="info" method="post" enctype="multipart/form-data" action="{{ route("account") }}">
        <h3>Профиль</h3>
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-4">
                    <div class="img-friends">
                        <img src="/{{ $user->avatar }}">
                    </div>
                    <label for="customFile">Загрузите картинку</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="file">
                        <label class="custom-file-label input" for="customFile">Выберите файл</label>
                        <small class="text-help"> jpg,png до 10мб</small>
                    </div>
                    <label for="selfdescription" class="mt-3">О себе</label>
                    <div class="custom-file">
                        <textarea class="form-control input" id="selfdescription" rows="5" name="selfdescription">{{ $user->selfdescription }}</textarea>
                    </div>
                    @if (session('message'))
                        <div class="text-success">
                            <strong> {{ session('message') }}</strong>
                        </div>
                    @endif
                    @if (count($errors) > 0)
                        <div class="text-error">
                            @foreach ($errors->all() as $error)
                                <strong>{{ $error }}</strong>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="login">Логин</label>
                        <input type="text" class="form-control" id="login" placeholder="Ваш логин" readonly
                               value="{{ $user->login }}" name="login">
                        <small class="text-help">Вы не можете изменить свой логин</small>
                    </div>
                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input type="text" class="form-control" id="name" placeholder="Ваше имя" name="name" value="{{ $user->name }}">
                    </div>
                    <div class="form-group">
                        <label for="surname">Фамилия</label>
                        <input type="text" class="form-control" id="surname" name="surname" placeholder="Ваша фамилия" value="{{ $user->surname }}">
                    </div>
                    <div class="form-group">
                        <label for="race">Раса</label>
                        <input type="text" class="form-control" id="race" name="race" placeholder="Ваша раса" value="{{ $user->race }}">
                    </div>
                    <div class="form-group">
                        <label for="gender">Пол</label>
                        <input type="text" class="form-control" id="gender" name="gender" placeholder="Ваш пол" value="{{ $user->gender }}">
                    </div>
                    <div class="form-group">
                        <label for="birthday">Дата рождения</label>
                        <input type="date" class="form-control input" id="birthday" name="birthday" placeholder="Дата рождения" value="{{ $user->birthday }}">
                    </div>
                    <div class="form-group">
                        <label for="homeplace">Место проживания</label>
                        <input type="text" class="form-control input" id="homeplace" name="homeplace" placeholder="Планета\Система" value="{{ $user->homeplace }}">
                    </div>
                    <button form="info" type="submit" class="btn button-console button-long mt-5">Сохранить</button>
                </div>
            </div>
        </div>
    </form>
    <form class="console mt-5" id="passwd" method="post" action="{{ route("changePasswd") }}">
        <div class="container">
            <div class="row justify-content-around">
                <div class="col-4">
                    <h3>Сменить пароль</h3>

                    <div class="form-group">
                        <label for="oldpassword">Старый пароль</label>
                        <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Старый пароль">
                    </div>
                    <div class="form-group">
                        <label for="password">Новый пароль</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Новый пароль">
                    </div>
                    <div class="form-group">
                        <label for="password">Повторите пароль</label>
                        <input type="password" class="form-control" id="repeat-password" name="password_confirmation"
                               placeholder="Ваш пароль">
                    </div>
                    <div class="row justify-content-center mt-5">
                        <button form="passwd" type="submit" class="btn button-console button-long">Сменить</button>
                    </div>
                    <input type="hidden" name="login" value="{{ $user->login }}">
                </div>
            </div>
        </div>
    </form>
@endsection
