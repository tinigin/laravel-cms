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

    'notifications' => false,

    /*
    |--------------------------------------------------------------------------
    | Default configuration for attachments.
    |--------------------------------------------------------------------------
    |
    | Strategy properties for the file and storage used.
    |
    */
    'attachment' => [
        'disk' => env('CMS_FILESYSTEM_DISK', 'public'),
        'parent_folder' => env('CMS_FILESYSTEM_PARENT_FOLDER', ''),
        'generator' => \LaravelCms\Attachment\Engines\Generator::class,
        'max' => [
            'width' => env('CMS_FILESYSTEM_MAX_IMAGE_WIDTH', 2500),
            'height' => env('CMS_FILESYSTEM_MAX_IMAGE_HEIGHT', 2500),
        ]
    ],
];
