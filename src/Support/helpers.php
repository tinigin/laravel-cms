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
    function alert(string $title = null, string $message = '', string $level = null): Alert
    {
        $notifier = app(Alert::class);

        if ($level !== null) {
            $level = (string) Color::INFO();
        }

        if ($title !== null) {
            return $notifier->message($title, $message, $level);
        }

        return $notifier;
    }
}
