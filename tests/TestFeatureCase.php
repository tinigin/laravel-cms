<?php

namespace LaravelCms\Tests;

use Orchestra\Testbench\TestCase;
use LaravelCms\Models\Cms\User;

/**
 * Class TestUnitCase.
 */
abstract class TestFeatureCase extends TestCase
{
    use Environment;

    /**
     * @var User
     */
    private $user;

    /**
     * @return User
     */
    protected function createAdminUser(): User
    {
        if ($this->user === null) {
            $this->user = User::factory()->create();
        }

        return $this->user;
    }

    /**
     * Set the URL of the previous request.
     *
     * @param string $url
     *
     * @return $this
     */
    public function from(string $url)
    {
        $this->app['session']->setPreviousUrl($url);

        return $this;
    }
}