@component($typeForm, get_defined_vars())
    <div class="custom-file">
        <input {{ $attributes }}>
        <label class="custom-file-label" for="{{ $attributes['id'] }}">{{ __('Choose File') }}</label>
    </div>

    @if ($value)
        @foreach ($value as $file)
            <a
                href="{{ $file->url() }}"
                target="_blank"
                class="thumbnail-link"
                @if ($file->isImage()) data-lightbox="{{$attributes['id'] }}" @endif
                title="{{ $file->name }}"
            >
                <span
                    class="delete bg-danger"
                    data-remove="{{ $file->getKey() }}"
                    data-url="/cms/ajax/remove-file"
                    title="Удалить"
                ><i class="fas fa-trash"></i></span>

                @if ($file->isImage())
                    <img
                        src="{{ $file->url() }}"
                        class="img-thumbnail mt-2 mr-2"
                    />
                @else
                    <div class="file-thumbnail img-thumbnail mt-2 mr-2">
                        <i class="fas fa-file text-muted"></i>
                    </div>
                @endif
            </a>
        @endforeach
    @endif
@endcomponent
