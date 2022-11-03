<?php

return [
    'url_prefix' => 'cms',
    'namespace' => "\\App\\Http\\Controllers\\Cms",
    'blade_functions' => (bool) env('APP_BLADE_FUNCTIONS', false),

    /*
    |--------------------------------------------------------------------------
    | Default configuration for attachments.
    |--------------------------------------------------------------------------
    |
    | Strategy properties for the file and storage used.
    |
    */
    'attachment' => [
        'disk' => 'public',
        'parent_folder' => env('CMS_FILESYSTEM_PARENT_FOLDER', ''),
        'generator' => \LaravelCms\Attachment\Engines\Generator::class,
    ],
];
