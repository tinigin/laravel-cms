@component($typeForm, get_defined_vars())
    @isset($sendTrueOrFalse)
        <input hidden name="{{$attributes['name']}}" value="{{$attributes['novalue']}}">
        <div class="form-check form-switch">
            <input value="{{$attributes['yesvalue']}}"
                   {{ $attributes }}
                   @if(isset($attributes['value']) && $attributes['value']) checked @endif
                   id="{{$id}}"
            >
            <label class="form-check-label" for="{{$id}}">{{$placeholder ?? ''}}</label>
        </div>
    @else
        <div class="custom-control custom-switch">
			<input {{ $attributes }} id="{{$id}}" value="1" @if($value) checked @endif>
			<label class="custom-control-label" for="{{$id}}">{{$placeholder ?? ''}}</label>
		</div>
    @endisset
@endcomponent
