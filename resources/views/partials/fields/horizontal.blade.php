<div class="form-group row form-group-row">
    @isset($title)
        <label for="{{$id}}" class="col-sm-2 text-wrap mt-2 form-label">
            {{$title}}

            @if(isset($attributes['required']) && $attributes['required'])
                <sup class="text-danger">*</sup>
            @endif
        </label>
    @endisset

    <div class="col-sm-10">
        {{$slot}}

        @if($errors->has($oldName))
            <div class="invalid-feedback d-block">
                {{$errors->first($oldName)}}
            </div>
        @elseif(isset($help))
            <small class="form-text text-muted">{!!$help!!}</small>
        @endif
    </div>
</div>

@isset($hr)
    <div class="line line-dashed border-bottom my-3"></div>
@endisset
