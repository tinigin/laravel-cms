<?php

namespace LaravelCms\Support;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ImageHelper {

    protected $manager;
    protected $image;

    public function __construct($filename)
    {
        $this->manager = new ImageManager(new Driver());
        $this->image = $this->manager->read($filename);
    }

    public function resizeToWidth($width)
    {
        $this->image->scale(width: $width);
    }

    public function resizeToHeight($height)
    {
        $this->image->scale(height: $height);
    }

    public function resizeToBestFit($width, $height)
    {
        $this->image->cover(width: $width, height: $height);
    }

    public function crop(int $width, int $height, int $offset_x = 0, int $offset_y = 0, string $position = 'top-left')
    {
        $this->image->crop(
            width: $width,
            height: $height,
            offset_x: $offset_x,
            offset_y: $offset_y,
            position: $position
        );
    }

    public function contain($width, $height, $background = 'ffffff', string $position = 'center')
    {
        $this->image->contain(width: $width, height: $height, background: $background, position: $position);
    }

    public function trim(int $tolerance = 0)
    {
        $this->image->trim($tolerance);
    }

    public function save(string $filename, int $quality = 100)
    {
        $this->image->save($filename, quality: $quality);
    }

    public function watermark($path)
    {
        if ($path === true || $path == 'true')
            $path = config('cms.watermark');

        if (!is_file($path))
            return false;

        $manager = new ImageManager(new Driver());
        $watermarkSource = $manager->read($path)->scale($this->image->width(), $this->image->height());
        $this->image->place($watermarkSource, position: 'center');
    }
}
