<?php

namespace LaravelCms\Tests\Unit;

use LaravelCms\Support\Color;
use LaravelCms\Facades\Alert;
use LaravelCms\Facades\Toast;
use LaravelCms\Tests\TestUnitCase;

/**
 * Class AlertTest.
 */
class AlertTest extends TestUnitCase
{
    public function testHtmlAlert():void
    {
        Alert::info('<h1>Hello Word</h1>');

        self::assertEquals('<h1>Hello Word</h1>', session('flash_notification.title'));
    }

    public function testHelperAlert(): void
    {
        alert('test');

        self::assertEquals('test', session('flash_notification.title'));
        self::assertEquals('info', session('flash_notification.level'));

        self::assertInstanceOf(\LaravelCms\Alert\Alert::class, alert(''));
    }

    /**
     * @dataProvider getLevels
     *
     * @param $level
     * @param $css
     */
    public function testShouldFlashLevelsAlert(string $level, string $css): void
    {
        Alert::$level('test');

        self::assertEquals('test', session('flash_notification.title'));
        self::assertEquals($css, session('flash_notification.level'));
    }

    /**
     * @dataProvider getLevels
     *
     * @param $level
     * @param $css
     */
    public function testShouldFlashLevelsToast(string $level, string $css): void
    {
        Toast::$level('test');

        self::assertEquals('test', session('toast_notification.title'));
        self::assertEquals($css, session('toast_notification.level'));
    }

    public function testShouldToastValue(): void
    {
        Toast::info('Hello Alexandr!')
            ->autoHide(false)
            ->delay(3000);

        self::assertEquals('Hello Alexandr!', session('toast_notification.title'));
        self::assertEquals('false', session('toast_notification.auto_hide'));
        self::assertEquals('3000', session('toast_notification.delay'));
    }

    public function testShouldCheckAlert(): void
    {
        self::assertFalse(Alert::check());

        Alert::info('check alert');

        self::assertTrue(Alert::check());
    }

    /**
     * Array of keys and css classes.
     *
     * @return array
     */
    public static function getLevels(): array
    {
        return [
            [
                'info',
                'info',
            ],
            [
                'success',
                'success',
            ],
            [
                'error',
                'danger',
            ],
            [
                'warning',
                'warning',
            ],
        ];
    }
}
