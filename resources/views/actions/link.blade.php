@component($typeForm, get_defined_vars())
    <a {{ $attributes }}>
        {{ $name ?? '' }}
    </a>
@endcomponent
