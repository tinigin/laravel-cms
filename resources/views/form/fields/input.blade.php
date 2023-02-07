@component($typeForm, get_defined_vars())
    @if ($attributes['type'] == 'datetime' || $attributes['type'] == 'date')
        <div data-input-mask="{{$mask ?? ''}}">
            <div class="input-group date @if ($attributes['type'] == 'datetime') datetime-picker @else datetime-picker @endif" id="{{ $attributes['name'] }}_datetime" data-target-input="nearest">
                <div class="input-group-prepend" data-target="#{{ $attributes['name'] }}_datetime" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="far fa-calendar-alt"></i></div>
                </div>
                <input {{ $attributes }} />
            </div>
        </div>
    @else
        <div data-input-mask="{{$mask ?? ''}}">
            <input {{ $attributes }} />
        </div>
    @endif

    @empty(!$datalist)
        <datalist id="datalist-{{$name}}">
            @foreach($datalist as $item)
                <option value="{{ $item }}">
            @endforeach
        </datalist>
    @endempty
@endcomponent
