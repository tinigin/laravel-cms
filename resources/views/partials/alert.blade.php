@if (session()->has(\LaravelCms\Alert\Alert::SESSION_MESSAGE))
    <div class="alert alert-{{ session(\LaravelCms\Alert\Alert::SESSION_LEVEL) }} alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <!--h5><i class="icon fas fa-ban"></i> Alert!</h5-->
        {!! session(\LaravelCms\Alert\Alert::SESSION_MESSAGE) !!}
    </div>
@endif

@empty(!$errors->count())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-ban"></i> {{ __('Error') }}</h5>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
