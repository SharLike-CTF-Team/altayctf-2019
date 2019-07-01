@extends('layouts.main')
@section('title', 'Пользователи')

@section('content')
    <div class="scroll">
        @if(!(empty($users)))
            @foreach($users as $user)
                <div class="col-12 console mb-3">
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <a href="{{ route('profile',['id'=>$user->id]) }}">
                                <div class="img-friends">
                                    <img src="/{{$user->avatar}}">
                                </div>
                            </a>
                        </div>
                        <div class="col-8">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <a href="{{ route('profile',['id'=>$user->id]) }}">
                                        <h3>
                                            @if(!empty($user->name))
                                                {{ $user->name  }} {{ $user->surname }}
                                            @else
                                                {{ $user->login }}
                                            @endif
                                        </h3>
                                    </a>
                                </div>
                                <div class="col-4">
                                    @if(!empty($user->race))
                                        {{ $user->race  }}
                                    @endif
                                    @if(!empty($user->birthday))
                                        {{ $user->birthday }} {{ App\User::num2word($user->birthday) }}
                                    @endif
                                </div>
                            </div>
                            @if(!empty($user->selfdescription))
                                <div class="row justify-content-between text-left">
                                    <p>{{ $user->selfdescription }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
