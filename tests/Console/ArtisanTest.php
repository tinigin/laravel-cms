<?php

namespace LaravelCms\Tests\Console;

use LaravelCms\Tests\TestConsoleCase;

class ArtisanTest extends TestConsoleCase
{
    /**
     * debug: php vendor/bin/phpunit  --filter= ArtisanTest tests\\Feature\\ArtisanTest --debug.
     *
     * @var
     */
    public function testArtisanCmsAdmin(): void
    {
        $this->artisan('cms:admin')
            ->expectsQuestion('What is your name?', 'testConsoleCreateUser')
            ->expectsQuestion('What is your email?', 'testConsoleCreateUser@console.loc')
            ->expectsQuestion('What is the password?', 'testConsoleCreateUser')
            ->expectsOutputToContain('User created successfully.');

        $this->artisan('cms:admin')
            ->expectsQuestion('What is your name?', 'testConsoleCreateUser')
            ->expectsQuestion('What is your email?', 'testConsoleCreateUser@console.loc')
            ->expectsQuestion('What is the password?', 'testConsoleCreateUser')
            ->expectsOutputToContain('User exist');

        $this->artisan('cms:admin', ['--id' => 1])
            ->expectsOutputToContain('User sections restored');
    }

    public function testArtisanCmsInstall(): void
    {
        $this->artisan('cms:install')
            ->expectsOutputToContain("Installation completed!");
    }

    public function testArtisanCmsDbRecords(): void
    {
        $this->artisan('cms:init-sections')
            ->expectsOutputToContain("Sections records were added");
    }

    public function testArtisanCmsAssets(): void
    {
        $this->artisan('cms:publish')
            ->assertExitCode(0);
    }
}
