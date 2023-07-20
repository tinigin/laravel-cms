@component($typeForm, get_defined_vars())
    @if($attributes['class'] == 'form-check-input')
        <div class="form-check form-switch">
            <input {{ $attributes }} role="switch" id="{{$id}}" value="1" @if($value) checked @endif>
            <label class="form-check-label" for="{{$id}}">{{$placeholder ?? ''}}</label>
        </div>
    @else
        <div class="custom-control custom-switch">
			<input {{ $attributes }} id="{{$id}}" value="1" @if($value) checked @endif>
			<label class="custom-control-label" for="{{$id}}">{{$placeholder ?? ''}}</label>
		</div>
    @endisset
@endcomponent
