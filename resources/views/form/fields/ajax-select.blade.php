@component($typeForm, get_defined_vars())
    <div class="ajax-select" {{$readonly ? "data-readonly=\"true\"" : ''}}>
        <select name="{{ $attributes['name'] }}" class="hidden" {{$attributes['multiple'] ? 'multiple' : ''}}>
            @if ($value && is_array($value))
                @foreach($value as $id => $name)
                    <option value="{{$id}}" selected>{{$name}}</option>
                @endforeach
            @endif
        </select>

        @if (!$readonly)
            <input type="text" class="{{ $attributes['class'] }}" placeholder="Название или артикул" id="{{ $attributes['name'] }}_input" data-url="{{ $attributes['url'] }}" autocomplete="off">
        @endif

        <div class="selected-items"></div>
    </div>
@endcomponent
