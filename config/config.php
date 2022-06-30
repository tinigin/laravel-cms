<?php

return [
    'url_prefix' => 'cms',
    'namespace' => "\\App\\Http\\Controllers\\Cms",

    /*
    |--------------------------------------------------------------------------
    | Default configuration for attachments.
    |--------------------------------------------------------------------------
    |
    | Strategy properties for the file and storage used.
    |
    */
    'attachment' => [
        'disk'      => 'public',
        'generator' => \App\Core\Attachment\Engines\Generator::class,
    ],
];
