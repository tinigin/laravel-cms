@component($typeForm, get_defined_vars())
    <div data-controller="select">
        <select {{ $attributes }} data-live-search="true" data-width="100%" size="10" data-actions-box="true" {{$readonly ? 'disabled="true"' : ''}}>
            @foreach($options as $key => $option)
                <option value="{{$key}}"
                    @isset($value)
                        @if (is_array($value) && in_array($key, $value)) selected
                        @elseif (isset($value[$key]) && $value[$key] == $option) selected
                        @elseif ($key == $value) selected
                        @endif
                    @endisset
                >{{$option}}</option>
            @endforeach
        </select>
    </div>
@endcomponent
