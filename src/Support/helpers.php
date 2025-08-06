<?php

use LaravelCms\Alert\Alert;
use LaravelCms\Support\Color;

if (! function_exists('alert')) {
    /**
     * Helper function to send an alert.
     *
     * @param string|null $title
     * @param string|null $message
     * @param string|null $level
     *
     * @return Alert
     */
    function alert(string $title = '', string $message = '', string $level = ''): Alert
    {
        $notifier = app(Alert::class);

        if ($level !== '') {
            $level = (string) Color::INFO();
        }

        if ($title !== '') {
            return $notifier->message($title, $message, $level);
        }

        return $notifier;
    }
}
