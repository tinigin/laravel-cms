@component($typeForm, get_defined_vars())
    <div class="properties" data-url="{{ $url }}" data-name="{{ $name }}" data-readonly="{{ $readonly ? 'true' : 'false'}}">
        @if ($props)
            <div class="template" style="display: none;">
                <select class="form-control" data-live-search="true" data-width="100%" size="10" data-actions-box="true">
                    @foreach($props as $item)
                        <option value="{{ $item->getKey() }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div id="properties-container">
            @if ($value)
                @foreach ($value as $item)
                    <div class="row align-items-start mb-3 w-100" data-row="true" data-type="{{ $item['type'] }}" data-name="{{ $item['name'] }}" data-id="{{ $item['id'] }}" style="align-content: stretch;">
                        <div class="col-2 pt-2 field-label">
                            {{ $item['name'] }}
                        </div>
                        <div class="col property-values" style="flex-grow: 1">
                            @if ($item['type'] == 'string')
                                @if (isset($item['options']))
                                    <select
                                        class="form-control selectpicker"
                                        required
                                        data-live-search="true"
                                        data-width="100%"
                                        size="10"
                                        data-actions-box="true"
                                        multiple
                                        name="{{ $name }}[{{ $item['id'] }}][]"
                                        {{ $readonly ? 'disabled="true"' : '' }}
                                    >
                                        @foreach($item['options'] as $val)
                                            <option value="{{$val}}"
                                                @if (is_array($item['value']) && in_array($val, $item['value'])) selected
                                                @elseif ($val == $item['value']) selected
                                                @endif
                                            >{{ $val }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    @php ($values = is_array($item['value']) ? $item['value'] : [$item['value']])

                                    @foreach ($values as $v)
                                        <div class="d-flex align-items-center @if (!$loop->first) mt-2 @endif">
                                            <div class="" style="flex-grow: 1">
                                                <input
                                                    class="form-control"
                                                    required
                                                    type="text"
                                                    {{ $readonly ? 'readonly="true"' : '' }}
                                                    name="{{ $name }}[{{ $item['id'] }}][]"
                                                    value="{{ $v }}"
                                                />
                                            </div>
                                            @if (!$readonly)
                                                <div class="pl-2 text-nowrap text-right">
                                                    <span class="mr-2 fa fa-minus text-danger" data-remove-input="true" style="cursor: pointer"></span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach

                                    @if (!$readonly)
                                        <span class="fa fa-plus text-success mt-3" data-add-input="true" style="cursor: pointer"></span>
                                    @endif
                                @endif

                            @elseif($item['type'] == 'boolean')
                                <select
                                    class="form-control selectpicker"
                                    required
                                    data-live-search="true"
                                    data-width="100%"
                                    size="10"
                                    data-actions-box="true"
                                    multiple
                                    name="{{ $name }}[{{ $item['id'] }}]"
                                    {{ $readonly ? 'disabled="true"' : '' }}
                                >
                                    @foreach([true => 'Да', false => 'Нет'] as $key => $title)
                                        <option value="{{ $key }}"
                                            @if (is_array($item['value']) && in_array($key, $item['value'])) selected
                                            @elseif ($key == $item['value']) selected
                                            @endif
                                        >{{ $title }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    class="form-control"
                                    required
                                    type="text"
                                    {{ $readonly ? 'readonly="true"' : '' }}
                                    name="{{ $name }}[{{ $item['id'] }}]"
                                    value="{{ is_array($item['value']) ? join('; ', $item['value']) : $item['value'] }}"
                                />
                            @endif
                        </div>
                        @if (!$readonly)
                            <div class="pl-2 text-right pt-2 property-actions">
                                <span class="fa fa-trash text-danger" title="Двойное нажатие" data-remove-property="true" style="cursor: pointer"></span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        @if (!$readonly)
            <div class="row mt-3">
                <div class="col-12">
                    <span id="add-property" class="fa fa-plus text-success" style="cursor: pointer"></span>
                </div>
            </div>
        @endif
    </div>
@endcomponent
