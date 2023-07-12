@component($typeForm, get_defined_vars())
    @include('cms::partials.tree', [
        'type' => $attributes['multiple'] ? 'multiple' : 'single',
        'name' => $attributes['name'],
        'value' => $value,
        'tree' => $tree,
        'url' => '',
        'readonly' => $readonly
    ])
@endcomponent
