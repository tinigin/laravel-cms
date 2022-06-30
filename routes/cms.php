<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// CMS routes
Route::name('cms.')->group(function() {
    Route::prefix(config('cms.url_prefix'))
        ->middleware(['web', 'cms.auth:cms'])
        ->group(function() {
            // Auth
            Route::get('login', [\LaravelCms\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
                ->name('login')
                ->withoutMiddleware('cms.auth:cms');
            Route::post('login', [\LaravelCms\Http\Controllers\Auth\LoginController::class, 'login'])
                ->name('login.submit')
                ->withoutMiddleware('cms.auth:cms');
            Route::get('login/oauth', [\LaravelCms\Http\Controllers\Auth\LoginController::class, 'oauth'])
                ->name('login.oauth')
                ->withoutMiddleware('cms.auth:cms');
            Route::get('login/oauth/yandex', [\LaravelCms\Http\Controllers\Auth\LoginController::class, 'yandex'])
                ->name('login.oauth.yandex')
                ->withoutMiddleware('cms.auth:cms');
            Route::get('password', [\LaravelCms\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])
                ->name('password')
                ->withoutMiddleware('cms.auth:cms');
            Route::post('password', [\LaravelCms\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
                ->name('password.email')
                ->withoutMiddleware('cms.auth:cms');
            Route::get('password/reset/{token}', [\LaravelCms\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])
                ->name('password.reset')
                ->withoutMiddleware('cms.auth:cms');
            Route::post('password/reset', [\LaravelCms\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
                ->name('password.update')
                ->withoutMiddleware('cms.auth:cms');
            Route::get('logout', [\LaravelCms\Http\Controllers\Auth\LoginController::class, 'logout'])
                ->name('logout');

            Route::get('/', [\LaravelCms\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

            Route::group([], function(\Illuminate\Routing\Router $router) {
                $resolver = function ($controller, $action, $id = null) {
                    $namespace = config('cms.namespace');
                    $controller = Str::studly($controller);

                    // Generate App\Http\Controllers\Cms\{name}Controller
                    $controllerName = sprintf('%s\\%sController', $namespace, $controller);
                    if (!class_exists($controllerName)) {
                        // @todo Change namespace
                        $namespace = 'LaravelCms\\Http\\Controllers';
                        $controllerName = sprintf('%s\\%sController', $namespace, $controller);
                    }

                    if (!class_exists($controllerName))
                        throw new NotFoundHttpException;

                    $controller = app()->make($controllerName);
                    return $controller->callAction($action, array($id));
                };

                $router->match(['get', 'post'], '{controller}', function ($controller) use ($resolver) {
                    return $resolver($controller, 'index');
                })->name('module.index');

                $router->get('{controller}/create', function ($controller) use ($resolver) {
                    return $resolver($controller, 'create');
                })->name('module.create');

                $router->post('{controller}/store', function ($controller) use ($resolver) {
                    return $resolver($controller, 'store');
                })->name('module.store');

                $router->get('{controller}/{objectId}', function ($controller, $objectId) use ($resolver) {
                    return $resolver($controller, 'edit', $objectId);
                })->name('module.edit');

                $router->match(['put', 'patch'],'{controller}/{objectId}', function ($controller, $objectId) use ($resolver) {
                    return $resolver($controller, 'update', $objectId);
                })->name('module.update');

                $router->get('{controller}/destroy/{objectId}', function ($controller, $objectId) use ($resolver) {
                    return $resolver($controller, 'destroy', $objectId);
                })->name('module.destroy');

                // Ajax
                $router->post('ajax/remove-file', [\LaravelCms\Http\Controllers\AjaxController::class, 'removeFile'])->name('ajax.remove.file');
            });

            Route::fallback(function () {
                return view('cms::errors.404');
            });
        });
});
