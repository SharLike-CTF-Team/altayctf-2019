@extends('layouts.main')
@section('title', 'Друзья')

@section('content')
    <div class="row mb-2 justify-content-center">
        <div class="col-2">
            <a href="#">
                <div id="btn_friends" class="button-console text-center button-active p-1">
                    Друзья
                </div>
            </a>
        </div>
        <div class="col-2">
            <a href="#">
                <div id="btn_requests" class="button-console text-center p-1">
                    Заявки
                </div>
            </a>
        </div>
    </div>
    <div id="friends" class="scroll">
        @if(!(empty($friends)))
            @foreach($friends as $friend)
                <div class="col-12 console mb-3">
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <a href="{{ route('profile',['id'=>$friend->id]) }}">
                                <div class="img-friends">
                                    <img src="{{$friend->avatar}}">
                                </div>
                            </a>
                        </div>
                        <div class="col-8">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <a href="{{ route('profile',['id'=>$friend->id]) }}">
                                        <h3>
                                            @if(!empty($friend->name))
                                                {{ $friend->name  }} {{ $friend->surname }}
                                            @else
                                                {{ $friend->login }}
                                            @endif
                                        </h3>
                                    </a>
                                </div>
                                <div class="col-4">
                                    @if(!empty($friend->race))
                                        {{ $friend->race  }}
                                    @endif
                                    @if(!empty($friend->birthday))
                                        {{ $friend->birthday }} {{ App\User::num2word($friend->birthday) }}
                                    @endif
                                </div>
                            </div>
                            @if(!empty($friend->selfdescription))
                                <div class="row justify-content-between text-left">
                                    <p>{{ $friend->selfdescription }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-3">
                            <form action="{{ route('addMessage') }}" method="POST">
                                <input type="hidden" value="{{ $friend->id  }}" name="user_id">
                                <input class="btn button-console" type="submit" value="Написать сообщение">
                            </form>
                        </div>
                        <div class="col-3">
                            <form action="{{ route('removeFriend') }}" method="POST">
                                <input type="hidden" value="{{ $friend->id  }}" name="user_id">
                                <input class="btn button-console" type="submit" value="Удалить из друзей">
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 console mb-3">
                <h4>У вас еще нет друзей :(</h4>
            </div>
        @endif
    </div>
    <div id="requests" style="display: none" class="scroll h-25">
        @if(!(empty($requests)))
            @foreach($requests as $onerequest)
                <div class="col-12 console mb-3">
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <a href="{{ route('profile',['id'=>$onerequest->id]) }}">
                                <div class="img-friends">
                                    <img src="{{$onerequest->avatar}}">
                                </div>
                            </a>
                        </div>
                        <div class="col-8">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <h3>
                                        @if(!empty($onerequest->name))
                                            {{ $onerequest->name  }} {{ $onerequest->surname }}
                                        @else
                                            {{ $onerequest->login }}
                                        @endif
                                    </h3>
                                </div>
                                <div class="col-4">
                                    @if(!empty($onerequest->race))
                                        {{ $onerequest->race  }}
                                    @endif
                                    @if(!empty($onerequest->birthday))
                                        {{ $onerequest->birthday }} {{ App\User::num2word($onerequest->birthday) }}
                                    @endif
                                </div>
                            </div>
                            @if(!empty($onerequest->selfdescription))
                                <div class="row justify-content-between text-left">
                                    <p>{{ $onerequest->selfdescription }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-3">
                            <form action="{{ route('addFriend') }}" method="POST">
                                <input type="hidden" value="{{ $onerequest->id  }}" name="user_id">
                                <input class="btn button-console" type="submit" value="Добавить в друзья">
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 console mb-3">
                <h4>Нет новых заявок</h4>
            </div>
        @endif
    </div>
@endsection
