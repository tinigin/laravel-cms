@component($typeForm, get_defined_vars())
    @if ($readonly)
        {!! $value ? (strip_tags($value) == $value ? nl2br($value) : $value) : '—' !!}
    @else
        <textarea {{ $attributes }}>{{ $value ?? '' }}</textarea>
    @endif
@endcomponent
