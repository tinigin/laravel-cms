<?php

namespace LaravelCms\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use LaravelCms\Database\Seeders\LaravelCmsSeeder;
use LaravelCms\Facades\Alert;
use LaravelCms\LaravelCmsServiceProvider;

/**
 * Trait Environment.
 */
trait Environment
{
    /**
     * Setup the test environment.
     * Run test: php vendor/bin/phpunit --coverage-html ./logs/coverage ./tests
     * Run 1 test:  php vendor/bin/phpunit  --filter= UserTest tests\\Unit\\Platform\\UserTest --debug.
     */
    protected function setUp(): void
    {
        parent::setUp();

        /* Install application */
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(realpath('./database/migrations'));
        $this->artisan('cms:install')
//            ->expectsQuestion('What is your name?', 'testConsoleCreateUser')
//            ->expectsQuestion('What is your email?', 'testConsoleCreateUser@console.loc')
//            ->expectsQuestion('What is the password?', 'testConsoleCreateUser')
        ;

        Factory::guessFactoryNamesUsing(function ($factory) {
            $factoryBasename = class_basename($factory);

            return "LaravelCms\Database\Factories\\$factoryBasename".'Factory';
        });

        $this->artisan('db:seed', [
            '--class' => LaravelCmsSeeder::class,
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $config = config();

        $config->set('app.debug', true);

        // set up database configuration
        $config->set('database.default', 'testing');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelCmsServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Alert'       => Alert::class,
        ];
    }
}
