<?php

namespace LaravelCms\Tests\Console;

use LaravelCms\Tests\TestConsoleCase;

class ArtisanTest extends TestConsoleCase
{
    /**
     * debug: php vendor/bin/phpunit  --filter= ArtisanTest tests\\Console\\ArtisanTest --debug.
     *
     * @var
     */

    public function testArtisanCmsInstall(): void
    {
        $this->artisan('cms:install')
//            ->expectsQuestion('What is your name?', 'testConsoleCreateUser')
//            ->expectsQuestion('What is your email?', 'testConsoleCreateUser@console.loc')
//            ->expectsQuestion('What is the password?', 'testConsoleCreateUser')
            ->expectsOutput("Completed!");
    }
}
