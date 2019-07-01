@extends('layouts.main')
@section('title', 'Диалоги')

@section('content')
    <div class="row justify-content-between">
        <div class="col-3 console mt-5" id="message_objects">
            @if(count($dialogs)>0)
                @foreach($dialogs as $dialog)
                    <div class="row justify-content-between align-items-center w-100 ml-0 button-console mb-3
                    <?php if (!empty($current_dialog) && $dialog->id === $current_dialog->id) echo "button-active"?> message">
                        <div class="col-6 p-0">
                            <a href="/messages/{{ $dialog->slug }}">
                                {{ $users[$dialog->id]["name"] }}
                            </a>
                        </div>
                        <div class="col-6 p-0">
                            <a class="nav-link">
                                <div class="img-message">
                                    <img src="/{{ $users[$dialog->id]["avatar"] }}">
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="row justify-content-between align-items-center w-100 ml-0 button-console button-active message">
                    <div class="col pt-2">
                        <h3>Нет диалогов</h3>
                    </div>
                </div>
            @endif
        </div>


        <div class="col-8 console mt-5">
            @if(!empty($messages))
                <div class="dialog">
                    @foreach($messages as $message)
                        @php

                            if(Auth::user()->id === $message->id_owner)
                                $user = Auth::user()->toArray();
                            elseif(!empty($users[$message->id_dialogs]))
                                $user = $users[$message->id_dialogs];
                            else $user = Auth::user()->toArray();
                        @endphp
                        <div class="container button-console m-2">
                            <div class="row pr-5 justify-content-end">
                                {{$message->created_at}}
                            </div>
                            <div class="row justify-content-start align-items-center">
                                <div class="col-3">
                                    <a href="{{ route("profile",["id"=>$user["id"]]) }}">
                                        <div class="img-message">
                                            <img src="/{{ $user["avatar"] }}">
                                        </div>
                                        <p>
                                            @if(!empty($user["name"]))
                                                {{ $user["name"]  }} {{ isset($user["surname"])?$user["surname"]:"" }}
                                            @else
                                                {{ $user["login"] }}
                                            @endif
                                        </p>
                                    </a>
                                </div>
                                <div class="col text-left">
                                    {{$message->text}}
                                </div>
                            </div>
                            @if (!empty($message->image))
                                <div class="row justify-content-start mt-3">
                                    <div class="img-card">
                                        <img src="/{{ $message->image }}">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            @if(!empty($current_dialog))
                <div class="container">
                    <form id="addNews" class="mt-3" action="{{ route('addMessage') }}" method="POST"
                          enctype="multipart/form-data">
                        @if(!empty($users[$current_dialog->id]["id"]))
                            <input type="hidden" value="{{ $users[$current_dialog->id]["id"]  }}" name="user_id">
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                <textarea class="form-control input" id="selfdescription" rows="5"
                                          placeholder="Ваше сообщение" name="text">{{ old("text") }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <label for="customFile">Загрузите картинку</label>
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="customFile">
                                    <label class="custom-file-label input" for="customFile">Выберите
                                        файл</label>
                                    <small class="text-help"> jpg,png до 10мб</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <br>
                                <button type="submit" class="btn button-console button-long">Отправить</button>
                            </div>
                        </div>
                        @if (count($errors) > 0)
                            <div class="text-error">
                                @foreach ($errors->all() as $error)
                                    <strong>{{ $error }}</strong>
                                @endforeach
                            </div>
                        @endif
                    </form>
                </div>
            @endif
        </div>


    </div>
@endsection
