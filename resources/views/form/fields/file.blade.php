@component($typeForm, get_defined_vars())
    @if (!$readonly)
        <div class="custom-file mb-3">
            <input {{ $attributes }}>
            <label
                class="custom-file-label"
                for="{{ $attributes['id'] }}"
            >
                @if ($attributes['multiple'])
                    {{ __('Choose Files') }}
                @else
                    {{ __('Choose File') }}
                @endif
            </label>
        </div>
    @endif

    @if ($value && $value->count())
        <div class="files-list{{ isset($settings['sortable']) && $settings['sortable'] && !$readonly ? ' sortable-list' : '' }}">
            @foreach ($value as $file)
                @php ($isImage = $file->isImage())
                <div class="card border-secondary file-card" data-id="{{ $file->getKey() }}">
                    <div class="card-body file-card-body">
                        <span class="file-preview"{{ $isImage ? ' style=line-height:0' : '' }}>
                            <a
                                href="{{ $file->url() }}?{{ \Illuminate\Support\Str::random() }}"
                                @if ($file->isImage())
                                    data-lightbox="{{$attributes['id'] }}"
                                @endif
                                title="{{ $file->getFilename() }}"
                                target="_blank"
                            >
                                @if ($file->isImage())
                                    <img src="{{ $file->url() }}?{{ \Illuminate\Support\Str::random() }}" class="img-thumbnail" alt="" />
                                @else
                                    {{ $file->getFilename() }}
                                @endif
                            </a>
                        </span>

                        @if (isset($settings['thumbnails']))
                            <div class="thumbnails">
                                @foreach ($settings['thumbnails'] as $data)
                                    <span>
                                        @if ($file->hasThumbnail($data['w'] . 'x' . $data['h']))
                                            <a
                                                href="{{ $file->thumbnailUrl($data['w'] . 'x' . $data['h']) }}?{{ \Illuminate\Support\Str::random() }}"
                                                data-lightbox="{{$attributes['id'] }}"
                                                title="{{ $file->getThumbnailFilename($data['w'] . 'x' . $data['h']) }}"
                                                data-lightbox="{{$attributes['id'] }}"
                                                data-image-tippy="true"
                                                data-tippy-content="<img src='{{ $file->thumbnailUrl($data['w'] . 'x' . $data['h']) }}?{{ \Illuminate\Support\Str::random() }}' style='max-width: 200px; max-height: 200px;' />"
                                            >
                                                {{ $data['w'] . 'x' . $data['h'] }}
                                            </a>
                                        @endif
                                        @if (isset($settings['resize']) && $settings['resize'] && !$readonly)
                                            <a
                                                href=""
                                                class="crop-image text-muted ml-2"
                                                data-crop="true"
                                                data-url="{{ $file->url() }}?{{ \Illuminate\Support\Str::random() }}"
                                                data-thumbnail="{{ $data['w'] . 'x' . $data['h'] }}"
                                                data-width="{{ $data['w'] }}"
                                                data-height="{{ $data['h'] }}"
                                                data-mode="{{ $data['mode'] }}"
                                                {{ isset($data['watermark']) ? 'data-watermark=' . ($data['watermark'] === true ? 'true' : $data['watermark']) . '' : '' }}
                                                data-id="{{ $file->getKey() }}"
                                            >
                                                <i class="fas fa-crop"></i>
                                            </a>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="file-card-buttons btn-group">
                            @if (isset($settings['fields']) && $settings['fields'])
                                <a
                                    href="#"
                                    class="btn btn-sm btn-default"
                                    data-toggle="card-footer"
                                >
                                    <i class="fas fa-bars"></i>
                                </a>
                            @endif

                            @if (!$readonly)
                                <a
                                    href="#"
                                    data-remove="{{ $file->getKey() }}"
                                    data-url="/cms/ajax/remove-file"
                                    class="btn btn-sm btn-danger"
                                    title="{{ $file->getFilename() }}"
                                >
                                    <i class="fas fa-trash"></i>
                                </a>
                            @endif
                        </div>

                        @if (!$readonly && $attributes['multiple'])
                            <div class="multiple-select">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        id="file-to-select-{{ $file->getKey() }}"
                                        class="custom-control-input multiple-select"
                                        type="checkbox"
                                        name="files-to-select[]"
                                        value="{{ $file->getKey() }}"
                                    >
                                        <label for="file-to-select-{{ $file->getKey() }}" class="custom-control-label"></label>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if (isset($settings['fields']) && $settings['fields'])
                        <div class="card-footer file-card-footer hidden">
                            @foreach ($settings['fields'] as $field => $fieldTitle)
                                <div class="form-group">
                                    @switch($field)
                                        @case('title')
                                            <input
                                                type="text"
                                                data-name="{{ $field }}"
                                                class="form-control"
                                                name="{{ $attributes['id'] }}_{{ $field }}"
                                                placeholder="{{ $fieldTitle }}"
                                                value="{{ $file->getAdditionalByKey($field) }}"
                                                {{ $readonly ? 'readonly' : '' }}
                                            />
                                            @break

                                        @case('description')
                                            <textarea
                                                rows="5"
                                                class="form-control"
                                                data-name="{{ $field }}"
                                                name="{{ $attributes['id'] }}_{{ $field }}"
                                                placeholder="{{ $fieldTitle }}"
                                                {{ $readonly ? 'readonly' : '' }}
                                            >{{ $file->getAdditionalByKey($field) }}</textarea>
                                            @break
                                    @endswitch
                                </div>
                            @endforeach
                            @if (!$readonly)
                                <div class="form-group">
                                    <a
                                        href="#"
                                        data-file-action="save"
                                        data-id="{{ $attributes['id'] }}"
                                        class="btn btn-default float-right"
                                    >{{ __('Save') }}</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach

            @if (!$readonly && $attributes['multiple'])
                <p class="m-0 text-right">
                    <a
                        href="#"
                        data-url="/cms/ajax/remove-file"
                        class="btn btn-danger disabled"
                        data-remove=""
                        id="delete-multiple-files"
                    >Удалить выбранные</a>
                </p>
            @endif
        </div>
    @elseif ($readonly)
        <div class="files-list">
            —
        </div>
    @endif
@endcomponent
