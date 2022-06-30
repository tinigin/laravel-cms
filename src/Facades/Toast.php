<?php

namespace LaravelCms\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelCms\Alert\Toast as ToastClass;

/**
 * Class Toast.
 *
 * @method static ToastClass info(string $title, string $message = '')
 * @method static ToastClass success(string $title, string $message = '')
 * @method static ToastClass error(string $title, string $message = '')
 * @method static ToastClass warning(string $title, string $message = '')
 * @method static ToastClass check()
 *
 * @mixin ToastClass
 */
class Toast extends Facade
{
    /**
     * Initiate a mock expectation on the facade.
     *
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return ToastClass::class;
    }
}
