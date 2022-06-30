<button
    @empty(!$confirm)
        data-action="button#confirm"
        data-button-confirm="{{ $confirm }}"
    @endempty
    {{ $attributes }}>
    {{ $label ?? '' }}
</button>
