@component($typeForm, get_defined_vars())
   @empty(!$value)
        <div {{ $attributes }}>
            {!! $value !!}
        </div>
   @endempty
@endcomponent
