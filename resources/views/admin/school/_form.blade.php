<div class="form-group">
    <label for="tag" class="col-md-3 control-label">学校名称</label>
    <div class="col-md-5">
        @if(isset($data))
            <input type="text" class="form-control" name="name" id="tag" value="{{ $data['name'] }}" autofocus>
        @else
            <input type="text" class="form-control" name="name" id="tag" value="" autofocus>
        @endif
    </div>
</div>