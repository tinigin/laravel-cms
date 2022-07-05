@component($typeForm, get_defined_vars())
    <div class="custom-file">
        <input {{ $attributes }}>
        <label class="custom-file-label" for="{{ $attributes['id'] }}">{{ __('Choose File') }}</label>
    </div>

    @if ($value->count())
        <table class="table table-responsive-md table-striped mt-2">
            <thead>
                <tr>
                    <th class="border-top-0">{{ __("File") }}</th>
                    @if (isset($settings['thumbnails']))
                        @foreach ($settings['thumbnails'] as $data)
                            <th class="border-top-0">{{ $data['w'] . '×' . $data['h'] }}</th>
                        @endforeach
                    @endif
                    <th class="border-top-0"></th>
                </th>
            </thead>
            <tbody>
                @foreach ($value as $file)
                    <tr>
                        <td>
                            <a
                                href="{{ $file->url() }}"
                                data-lightbox="{{$attributes['id'] }}"
                                @if ($file->isImage()) data-lightbox="{{$attributes['id'] }}" @endif
                                title="{{ $file->getFilename() }}"
                                target="_blank"
                            >
                                {{ $file->getFilename() }}
                            </a>
                        </td>
                        @if (isset($settings['thumbnails']))
                            @foreach ($settings['thumbnails'] as $data)
                                <td>
                                    @if ($file->hasThumbnail($data['w'] . 'x' . $data['h']))
                                        <a
                                            href="{{ $file->thumbnailUrl($data['w'] . 'x' . $data['h']) }}"
                                            data-lightbox="{{$attributes['id'] }}"
                                            title="{{ $file->getThumbnailFilename($data['w'] . 'x' . $data['h']) }}"
                                        >
                                            {{ $file->getThumbnailFilename($data['w'] . 'x' . $data['h']) }}
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
                                </td>
                            @endforeach
                        @endif
                        <td class="text-right py-0 align-middle">
                            <a
                                href="#"
                                data-remove="{{ $file->getKey() }}"
                                data-url="/cms/ajax/remove-file"
                                class="btn btn-sm btn-danger"
                                title="{{ $file->getFilename() }}"
                            >
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endcomponent
