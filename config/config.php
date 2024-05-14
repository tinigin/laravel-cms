<?php

return [
    'url_prefix' => 'cms',
    'namespace' => "\\App\\Http\\Controllers\\Cms",
    'png_image_transparent' => env('CMS_PNG_TRANSPARENT', false),
    'images_default_background_r' => env('CMS_IMAGES_BG_R', 255),
    'images_default_background_g' => env('CMS_IMAGES_BG_G', 255),
    'images_default_background_b' => env('CMS_IMAGES_BG_B', 255),
    'images_default_background_a' => env('CMS_IMAGES_BG_A', 0),
    'watermark' => false,

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
