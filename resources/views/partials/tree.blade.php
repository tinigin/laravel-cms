@if (!isset($level))
    @php ($level = 1)
@endif

@if ($level == 1)
    <div class="tree" id="tree" data-url="{{ $url }}" data-type="{{ $type }}">
@endif

@if (empty($tree) && $level == 1)
    <p class="mt-2 ml-3">Нет</p>
@else
    <ul @if ($level > 3) style="display: none" @endif>
        @foreach ($tree as $item)
            <li data-id="{{ $item['id'] }}" id="item_{{ $item['id'] }}" class="pr-3">
                <div>
                    <span class="tree-icon @if ($level > 2 && isset($item['children'])) icon-plus toggle-collapse @elseif ($level <= 2 && isset($item['children'])) icon-minus toggle-collapse @else icon-empty @endif"></span>

                    @if ($type == 'sortable')
                        <a href="{{ route('cms.module.edit', ['objectId' => $item['id'], 'controller' => $controller], false) }}">{{ $item['name'] }}</a>

                    @elseif ($type == 'multiple')
                        @php ($inputId = Str::ulid())
                        <input type="checkbox" name="{{ $name }}" value="{{ $item['id'] }}" id="{{ $inputId }}" @if (in_array($item['id'], $value)) checked @endif></input>
                        <label for="{{ $inputId }}">{{ $item['name'] }}</label>

                    @elseif ($type == 'single')
                        @php ($inputId = Str::ulid())
                        <input type="radio" name="{{ $name }}" value="{{ $item['id'] }}" id="{{ $inputId }}" @if (in_array($item['id'], $value) || (!$item['id'] && !$value)) checked @endif></input>
                        <label for="{{ $inputId }}">{{ $item['name'] }}</label>

                    @endif
                </div>

                @if (isset($item['children']))
                    @include ('cms::partials.tree', ['tree' => $item['children'], 'level' => $level + 1, 'type' => $type, 'name' => $name, 'value' => $value])
                @endif
            </li>
        @endforeach
    </ul>
@endif

@if ($level == 1)
    </div>
@endif
