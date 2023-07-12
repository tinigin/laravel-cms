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
                <div class="card border-secondary file-card" data-id="{{ $file->getKey() }}">
                    <div class="card-body file-card-body">
                        <span class="w-33 d-inline-block overflow-hidden">
                            <a
                                href="{{ $file->url() }}?{{ \Illuminate\Support\Str::random() }}"
                                @if ($file->isImage())
                                    data-lightbox="{{$attributes['id'] }}"
                                    data-image-tippy="true"
                                    data-tippy-content="<img src='{{ $file->url() }}?{{ \Illuminate\Support\Str::random() }}' style='max-width: 200px; max-height: 200px;' />"
                                @endif
                                title="{{ $file->getFilename() }}"
                                target="_blank"
                            >
                                {{ $file->getFilename() }}
                            </a>
                        </span>

                        @if (isset($settings['thumbnails']))
                            @foreach ($settings['thumbnails'] as $data)
                                <span class="w-33 d-inline-block overflow-hidden">
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
                                            data-id="{{ $file->getKey() }}"
                                        >
                                            <i class="fas fa-crop"></i>
                                        </a>
                                    @endif
                                </span>
                            @endforeach
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
        </div>
    @elseif ($readonly)
        <div class="files-list">
            —
        </div>
    @endif
@endcomponent
