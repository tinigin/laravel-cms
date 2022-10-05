@extends('cms::layouts.simple')

@section('content')
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">{{ __('Authorization') }}</p>

                <form method="post" action="{{ route('cms.login', [], false) }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="{{ __('Email') }}"
                            required
                            autocomplete="email"
                            autofocus
                        >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input
                            type="password"
                            name="password"
                            class="form-control @if ($errors->hasAny('email', 'password')) is-invalid @endif"
                            placeholder="{{ __('Password') }}"
                            required autocomplete="current-password"
                        >
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        @if ($errors)
                            @foreach ($errors->all() as $message)
                                <span class="invalid-feedback text-center" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label for="remember">
                                    {{ __('Remember me') }}
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                {{ __('Log in') }}
                            </button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                @if (config('services.yandex.client_id'))
                    <div class="social-auth-links text-center mb-3">
                        <p>- ИЛИ -</p>
                        <a href="{{ route('cms.login.oauth.yandex', [], false) }}" class="btn btn-block btn-danger">
                            <i class="fab fa-yandex mr-2"></i> {{ __("Sign in with Yandex") }}
                        </a>
                    </div>
                @endif

                <p class="mb-0 mt-1">
                    @if (Route::has('cms.password'))
                        <a href="{{ route('cms.password', [], false) }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
@endsection
