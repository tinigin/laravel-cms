<?php

namespace LaravelCms;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use LaravelCms\Console\AdminCommand;
use LaravelCms\Console\ControllerCommand;
use LaravelCms\Console\InitSectionsCommand;
use LaravelCms\Console\InstallCommand;
use LaravelCms\Console\InstallLaravelCms;
use Illuminate\Support\Facades\Config;
use LaravelCms\Console\ModelCommand;
use LaravelCms\Console\PublishCommand;
use LaravelCms\Http\Middleware\Authenticate;
use LaravelCms\Http\Middleware\LogoutIfTimelimit;
use LaravelCms\Http\Middleware\RedirectIfAuthenticated;
use LaravelCms\View\Components\GridTable;

class LaravelCmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'cms');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms');
        $this->loadViewComponentsAs('cms', [
            GridTable::class,
        ]);
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'cms');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/cms.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('cms.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../config/settings.php' => config_path('settings.php'),
            ], 'config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/cms'),
            ], 'views');

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('assets/cms'),
            ], 'assets');

            // Publishing the translation files.
            $this->publishes([
                __DIR__.'/../resources/lang/ru' => lang_path('ru'),
            ], 'lang');

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('cms.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                InstallCommand::class,
                AdminCommand::class,
                InitSectionsCommand::class,
                PublishCommand::class,
                ControllerCommand::class,
                ModelCommand::class
            ]);
        }

        Blade::directive('spaceless', function () {
            return "<?php ob_start() ?>";
        });

        Blade::directive('endspaceless', function () {
            return "<?php echo preg_replace('/>\\s+</', '><', ob_get_clean()); ?>";
        });

        Blade::directive('generateId', function ($length = 10) {
            return "<?php $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; echo substr(str_shuffle(str_repeat($x, ceil($length / strlen($x)))), 1, $length); ?>";
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
//        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'cms');

        // Will use the SessionGuard driver with the moon provider
        Config::set('auth.guards.cms', [
            'driver' => 'session',
            'provider' => 'cms',
        ]);

        // Will use the EloquentUserProvider driver with the Admin model
        Config::set('auth.providers.cms', [
            'driver' => 'eloquent',
            'model' => class_exists(\App\Models\Cms\User::class) ? \App\Models\Cms\User::class : \LaravelCms\Models\Cms\User::class
        ]);

        Config::set('auth.passwords.cms', [
            'provider' => 'cms',
            'table' => 'cms_password_resets',
            'expire' => 60,
            'throttle' => 60,
        ]);

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('cms.auth', Authenticate::class);
        $router->aliasMiddleware('cms.guest', RedirectIfAuthenticated::class);
        $router->aliasMiddleware('cms.logout_if_timelimit', LogoutIfTimelimit::class);
    }
}
