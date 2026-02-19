<?php

namespace LaravelCms\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CmsSessionDriver
{
    public function handle($request, Closure $next)
    {
        // Проверяем, начинается ли URI с /cms/
        if ($request->is('cms*')) {
            Config::set('session.cookie', Str::slug(config('app.name'), '_').'_cms_session');
            Config::set('session.path', '/cms');
            Config::set('session.driver', 'database');
        }

        return $next($request);
    }
}