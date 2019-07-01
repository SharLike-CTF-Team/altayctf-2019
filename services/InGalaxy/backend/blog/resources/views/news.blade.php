@extends('layouts.main')
@section('title', 'Галолента')

@section('content')
    <div class="row justify-content-center mb-5" id="toAddNews">
        <a href="#addNews">
            <button class="btn button-console button-long">Есть что сказать?</button>
        </a>
    </div>

    @if(!empty($posts))
        <div class="scroll mb-3" style="max-height: 70vh">
            @foreach($posts as $post)
                <div class="row justify-content-center mb-3">
                    <div class="card console w-75">

                        <div class="row pr-5 justify-content-end">
                            {{$post->created_at}}
                        </div>
                        <!-- user start-->
                        <div class="row  justify-content-left">
                            <div class="col-3">
                                <div class="row justify-content-center">
                                    <a href="{{ route('profile',['id'=>$post->id_owner]) }}">
                                        <div class="img-user">
                                            <img src="/{{App\User::find($post->id_owner)->avatar }}">
                                        </div>
                                    </a>
                                </div>
                                <div class="row justify-content-center">
                                    <a href="{{ route('profile',['id'=>$post->id_owner]) }}">
                                        <h5 class="card-header">
                                            @if(!empty($name=App\User::find($post->id_owner)->name))
                                                {{ $name  }} {{ App\User::find($post->id_owner)->surname }}
                                            @else
                                                {{ App\User::find($post->id_owner)->login }}
                                            @endif
                                        </h5></a>
                                </div>
                                <!-- user end-->
                            </div>
                            <div class="col-9">
                                <div class="card-body text-left">
                                    <p class="card-text">{{$post->text}}</p>
                                </div>
                            </div>
                        </div>
                        @if (!empty($post->image))
                            <div class="row">
                                <div class="img-card">
                                    <img src="/{{$post->image}}">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row justify-content-center mb-5">
            <div class="card console w-75">
                <h2>Постов еще нет. Будете первым?</h2>
            </div>
        </div>
    @endif

    @include ('auth.addPost_form')

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
@endsection
