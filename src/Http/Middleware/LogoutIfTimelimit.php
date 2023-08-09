<?php

namespace LaravelCms\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutIfTimelimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $user = Auth::user();
        
        if ($user && $user->isTimeAllowed())
            return $next($request);

        else {
            Auth::guard('cms')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect(route('cms.login', absolute: false));
        }
    }
}
