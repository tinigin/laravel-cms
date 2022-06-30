<?php

namespace LaravelCms\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelCms\Alert\Alert as AlertClass;

/**
 * Class Alert.
 *
 * @method static AlertClass info(string $title, string $message = '')
 * @method static AlertClass success(string $title, string $message = '')
 * @method static AlertClass error(string $title, string $message = '')
 * @method static AlertClass warning(string $title, string $message = '')
 * @method static AlertClass check()
 * @method static AlertClass message(string $title, string $message = '', string $level = null)
 */
class Alert extends Facade
{
    /**
     * Initiate a mock expectation on the facade.
     *
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return AlertClass::class;
    }
}
