@component($typeForm, get_defined_vars())
    @if ($parent_id)
        <div class="external-listing" data-url="{{ $url }}" data-parent-id="{{ $parent_id }}"></div>
    @else
        <p class="text-center">Управление появится после создания объекта.</p>
    @endif
@endcomponent
