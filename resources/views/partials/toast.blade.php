@if (session()->has(\LaravelCms\Alert\Toast::SESSION_MESSAGE))
    <div
        class="toast-message hidden"
        data-icon="{{ session(\LaravelCms\Alert\Toast::SESSION_LEVEL) }}"
        data-title="{{ session(\LaravelCms\Alert\Toast::SESSION_MESSAGE_TITLE) }}"
    >{{ session(\LaravelCms\Alert\Toast::SESSION_MESSAGE) }}</div>
@endif
