<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-8  console">
            <h2> Поделиться новостью </h2>
            <form id="addNews" class="mt-3" action="<?php echo e(route('profile',['id'=>Auth::user()->id])); ?>" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <textarea class="form-control input" id="text" name="text" rows="5"><?php echo e(old('text')); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-6">
                        <label for="customFile">Загрузите картинку</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile">
                            <label class="custom-file-label input" for="customFile">Выберите файл</label>
                            <small class="text-help"> jpg,png</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <br>
                        <button type="submit" class="btn button-console button-long">Поделиться</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>