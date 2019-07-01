@extends('layouts.main')
@section('title', $user->login)

@section('content')
    <div class="col-12 console mb-5">
        <div class="row justify-content-end mb-5">
            <div class="col-4">
                <a href="{{ route ('profile',['id'=>$user->id]) }}">
                    <div class="img-friends">
                        <img src="/{{ $user->avatar }}">
                    </div>
                </a>
            </div>
            <div class="col-8 pt-5">
                <div class="row justify-content-between text-left">
                    <div class="col profile-info">
                        <p>
                            @if(!empty($user->name) || !empty($user->surname))
                                {{ $user->name }} {{ $user->surname }} aka <b>{{ $user->login }}</b>
                            @else
                                <b>{{ $user->login }}</b>
                            @endif
                        </p>

                        @if(!empty($user->gender) || !empty($user->birthday))
                            <p>
                                @if(!empty($user->gender))
                                    {{ $user->gender }}
                                @endif
                                @if(!empty($user->birthday))
                                    {{ $user->birthday }} {{ App\User::num2word($user->birthday) }}
                                @endif
                            </p>
                        @endif
                        @if(!empty($user->homeplace))
                            <p>
                                Дом: {{$user->homeplace}}
                            </p>
                        @endif
                        @if(!empty($user->selfdescription))
                            <p>{{ $user->selfdescription }}</p>
                        @endif
                        <p>Друзей: {{ $friends_count }}</p>

                        @if (count($errors) > 0)
                            <div class="text-error">
                                @foreach ($errors->all() as $error)
                                    <strong>{{ $error }}</strong>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-3">
                <form action="{{ route('addMessage') }}" method="POST">
                    <input type="hidden" value="{{ $user->id  }}" name="user_id">
                    <input class="btn button-console" type="submit" value="Написать сообщение">
                </form>
            </div>
            @php
                use App\User;
            @endphp
            @if(Auth::user()->id!==$user->id)
                <div class="col-3">
                    @php
                        $owner = User::findOrFail(Auth::user()->id);
                        $recipient = User::findOrFail($user->id);
                        if($owner->checkFriendship($recipient)):
                    @endphp
                    <form action="{{ route('removeFriend') }}" method="POST">
                        <input type="hidden" value="{{ $user->id  }}" name="user_id">
                        <input class="btn button-console" type="submit" value="Удалить из друзей">
                    </form>
                    @php
                        else:
                    @endphp
                    <form action="{{ route('addFriend') }}" method="POST">
                        <input type="hidden" value="{{ $user->id  }}" name="user_id">
                        <input class="btn button-console" type="submit" value="Добавить в друзья">
                    </form>
                    @php
                        endif;
                    @endphp
                </div>
            @endif
        </div>
    </div>

    @include ('auth.addPostInWall_form')

    @if(!empty($posts))
        <div class="scroll mb-3" style="max-height: 70vh">
            @foreach($posts as $post)
                <div class="row justify-content-center mb-5">
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
    @endif
@endsection
