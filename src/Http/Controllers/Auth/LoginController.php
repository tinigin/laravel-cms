<?php

namespace LaravelCms\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelCms\Models\Cms\User;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class LoginController extends \Illuminate\Routing\Controller
{
    use AuthenticatesUsers, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('cms.guest:cms')->except('logout');
    }

    public function showLoginForm()
    {
        return view('cms::login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Attempt to log the user in
        if (Auth::guard('cms')->attemptWhen([
            'email' => $request->email,
            'password' => $request->password,
            'status_id' => User::ACTIVE,
        ], function (User $user) {
            return $user->isTimeAllowed();
        }, $request->has('remember'))) {
            $request->session()->regenerate();

            // if successful, then redirect to their intended location
            return redirect()->intended(route('cms.dashboard', absolute: false));
        }

        $user = User::query()->where('email', $request->email)->first();
        if ($user && !$user->isTimeAllowed())
            return redirect()
                ->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => trans('cms::auth.time_limit', [
                    'from' => $user->time_from ?: '00:00', 'till' => $user->time_till ?: '23:59'
                ])]);

        // if unsuccessful, then redirect back to the login with the form data
        return redirect()
            ->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => trans('cms::auth.failed')]);
    }

    public function logout()
    {
        Auth::guard('cms')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect(route('cms.login', absolute: false));
    }

    public function yandex()
    {
        return Socialite::driver('yandex')->redirect();
    }

    public function oauth()
    {
        if (request()->get('code')) {
            $answer = Socialite::driver('yandex')->getAccessTokenResponse(request()->get('code'));
            if (array_key_exists('access_token', $answer) && $answer['access_token']) {
                $user = Socialite::driver('yandex')->userFromToken($answer['access_token']);
                if ($user->email) {
                    $user = User::where('email', $user->email)->first();
                    if ($user) {
                        if (!$user->isTimeAllowed())
                            return redirect()
                                ->back()
                                ->withInput($request->only('email', 'remember'))
                                ->withErrors(['email' => trans('cms::auth.time_limit', [
                                    'from' => $user->time_from ?: '00:00', 'till' => $user->time_till ?: '23:59'
                                ])]);

                        Auth::guard('cms')->loginUsingId($user->getKey(), true);
                        return redirect()->intended(route('cms.dashboard'));
                    }
                }
            }
        }

        return redirect()->to(route('cms.login'));
    }
}
