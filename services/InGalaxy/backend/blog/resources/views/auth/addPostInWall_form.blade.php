<div class="container mb-5">
    <div class="row justify-content-center console">
        <div class="col-8">
            <h2>Написать на стене</h2>
            <form id="addNews" class="mt-3" action="{{ route ('profile',['id'=>$user->id]) }}" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <textarea class="form-control input" id="text" rows="5" name="text">{{ old('text') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-6">
                        <label for="customFile">Загрузите картинку</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="file">
                            <label class="custom-file-label input" for="customFile">Выберите файл</label>
                            <small class="text-help"> jpg,png</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <br>
                        <button type="submit" class="btn button-console button-long">Написать</button>
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
    </div>
</div>