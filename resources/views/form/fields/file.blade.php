@component($typeForm, get_defined_vars())
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

    @if ($value && $value->count())
        <div class="files-list{{ isset($settings['sortable']) && $settings['sortable'] ? ' sortable-list' : '' }}">
            @foreach ($value as $file)
                <div class="card border-secondary file-card">
                    <div class="card-body file-card-body">
                        <span class="w-33 d-inline-block overflow-hidden">
                            <a
                                href="{{ $file->url() }}"
                                @if ($file->isImage())
                                    data-lightbox="{{$attributes['id'] }}"
                                    data-tippy="true"
                                    data-tippy-content="<img src='{{ $file->url() }}' style='max-width: 200px; max-height: 200px;' />"
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
                                            href="{{ $file->thumbnailUrl($data['w'] . 'x' . $data['h']) }}"
                                            data-lightbox="{{$attributes['id'] }}"
                                            title="{{ $file->getThumbnailFilename($data['w'] . 'x' . $data['h']) }}"
                                            data-lightbox="{{$attributes['id'] }}"
                                            data-tippy="true"
                                            data-tippy-content="<img src='{{ $file->thumbnailUrl($data['w'] . 'x' . $data['h']) }}' style='max-width: 200px; max-height: 200px;' />"
                                        >
                                            {{ $data['w'] . 'x' . $data['h'] }}
                                        </a>
                                    @endif
                                    @if (isset($settings['resize']) && $settings['resize'])
                                        <a
                                            href=""
                                            class="crop-image text-muted ml-2"
                                            data-crop="true"
                                            data-url="{{ $file->url() }}"
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

                            <a
                                href="#"
                                data-remove="{{ $file->getKey() }}"
                                data-url="/cms/ajax/remove-file"
                                class="btn btn-sm btn-danger"
                                title="{{ $file->getFilename() }}"
                            >
                                <i class="fas fa-trash"></i>
                            </a>
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
                                                class="form-control"
                                                name="file_field_{{ $attributes['id'] }}_{{ $field }}"
                                                placeholder="{{ $fieldTitle }}"
                                            />
                                            @break

                                        @case('description')
                                            <textarea
                                                rows="5"
                                                class="form-control"
                                                name="file_field_{{ $attributes['id'] }}_{{ $field }}"
                                                placeholder="{{ $fieldTitle }}"
                                            ></textarea>
                                            @break
                                    @endswitch
                                </div>
                            @endforeach
                            <div class="form-group">
                                <a
                                    href="#"
                                    data-action="save"
                                    data-id="{{ $attributes['id'] }}"
                                    class="btn btn-default float-right"
                                >{{ __('Save') }}</a>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endcomponent
