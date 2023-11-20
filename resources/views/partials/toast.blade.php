@if (session()->has(\LaravelCms\Alert\Toast::SESSION_MESSAGE))
    @php ($level = session(\LaravelCms\Alert\Toast::SESSION_LEVEL))
    <div
        class="toast-message hidden"
        data-icon="{{ $level == 'danger' ? 'error' : $level }}"
        data-title="{{ session(\LaravelCms\Alert\Toast::SESSION_MESSAGE_TITLE) }}"
    >{{ session(\LaravelCms\Alert\Toast::SESSION_MESSAGE) }}</div>
@endif
