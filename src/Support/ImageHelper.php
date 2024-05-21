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

    public function scale($width, $height)
    {
        $this->image->scale(width: $width, height: $height);
    }

    public function cover($width, $height, string $position = 'center')
    {
        $this->image->cover(width: $width, height: $height, position: $position);
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

    public function trimWithBorder(int $border = 50, int $tolerance = 30, int $step = 5) {
        $originalWidth = $this->image->width();
        $originalHeight = $this->image->height();

        // detect color
        $corners = [
            [0, 0],
            [$originalWidth - 1, 0],
            [0, $originalHeight - 1],
            [$originalWidth - 1, $originalHeight - 1],
        ];

        $red = 0;
        $green = 0;
        $blue = 0;
        $alpha = 0;

        foreach ($corners as $corner) {
            $color = $this->image->pickColor($corner[0], $corner[1]);

            $red += $color->red()->toInt();
            $green += $color->green()->toInt();
            $blue += $color->blue()->toInt();
            $alpha+= $color->alpha()->toInt();
        }

        $red /= 4;
        $green /= 4;
        $blue /= 4;
        $alpha /= 4;

        // centered
        $cut = [
            't' => 0,
            'r' => 0,
            'b' => 0,
            'l' => 0,
        ];

        // top
        $rows = $originalHeight;
        for ($i = 0; $i < $originalHeight; $i++) {
            $break = false;

            for ($x = 0; $x < $originalWidth; $x += $step) {
                $c = $this->image->pickColor($x, $i);

                $r = $c->red()->toInt();
                $g = $c->green()->toInt();
                $b = $c->blue()->toInt();
                $a = $c->alpha()->toInt();

                $distance = sqrt(
                    pow($r - $red, 2) +
                    pow($b - $blue, 2) +
                    pow($g - $green, 2) +
                    pow($a - $alpha, 2)
                );

                if (
                    $distance > $tolerance
                ) {
                    $break = true;
                    break;
                }
            }

            if ($break)
                break;

            $cut['t'] += 1;
        }

        // bottom
        for ($i = 0; $i < $originalHeight; $i++) {
            $break = false;

            for ($x = 0; $x < $originalWidth; $x += $step) {
                $c = $this->image->pickColor($x, ($originalHeight - $i - 1));

                $r = $c->red()->toInt();
                $g = $c->green()->toInt();
                $b = $c->blue()->toInt();
                $a = $c->alpha()->toInt();


                $distance = sqrt(
                    pow($r - $red, 2) +
                    pow($b - $blue, 2) +
                    pow($g - $green, 2) +
                    pow($a - $alpha, 2)
                );

                if (
                    $distance > $tolerance
                ) {
                    $break = true;
                    break;
                }
            }

            if ($break)
                break;

            $cut['b'] += 1;
        }

        // left
        for ($i = 0; $i < $originalWidth; $i++) {
            $break = false;

            for ($y = 0; $y < $originalHeight; $y += $step) {
                $c = $this->image->pickColor($i, $y);

                $r = $c->red()->toInt();
                $g = $c->green()->toInt();
                $b = $c->blue()->toInt();
                $a = $c->alpha()->toInt();

                $distance = sqrt(
                    pow($r - $red, 2) +
                    pow($b - $blue, 2) +
                    pow($g - $green, 2) +
                    pow($a - $alpha, 2)
                );

                if (
                    $distance > $tolerance
                ) {
                    $break = true;
                    break;
                }
            }

            if ($break)
                break;

            $cut['l'] += 1;
        }

        // right
        for ($i = 0; $i < $originalWidth; $i++) {
            $break = false;

            for ($y = 0; $y < $originalHeight; $y += $step) {
                $c = $this->image->pickColor(($originalWidth - $i - 1), $y);

                $r = $c->red()->toInt();
                $g = $c->green()->toInt();
                $b = $c->blue()->toInt();
                $a = $c->alpha()->toInt();

                $distance = sqrt(
                    pow($r - $red, 2) +
                    pow($b - $blue, 2) +
                    pow($g - $green, 2) +
                    pow($a - $alpha, 2)
                );

                if (
                    $distance > $tolerance
                ) {
                    $break = true;
                    break;
                }
            }

            if ($break)
                break;

            $cut['r'] += 1;
        }

        $x = $cut['l'];
        $y = $cut['t'];

        $width = $originalWidth - $cut['l'] - $cut['r'];
        if ($width < 1) {
            $width = 1;
        }

        $height = $originalHeight - $cut['t'] - $cut['b'];
        if ($height < 1) {
            $height = 1;
        }

        if ($border) {
            if ($cut['l']) {
                $l = $cut['l'] > $border ? $border : $cut['l'];
                $x -= $l;
                $width += $l;
            }

            if ($cut['r']) {
                $l = $cut['r'] > $border ? $border : $cut['r'];
                $width += $l;
            }

            if ($cut['t']) {
                $l = $cut['t'] > $border ? $border : $cut['t'];
                $y -= $l;
                $height += $l;
            }

            if ($cut['b']) {
                $l = $cut['b'] > $border ? $border : $cut['b'];
                $height += $l;
            }
        }

        $this->crop($width, $height, $x, $y);
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
        $watermarkSource = $manager->read($path)->scale($this->image->width() / 4, $this->image->height() / 4);
        $this->image->place($watermarkSource, position: 'bottom-right');
    }

    public function smart(int $width, int $height)
    {
        $originalWidth = $this->image->width();
        $originalHeight = $this->image->height();

        // detect color
        $corners = [
            [0, 0],
            [$originalWidth - 1, 0],
            [0, $originalHeight - 1],
            [$originalWidth - 1, $originalHeight - 1],
        ];

        $red = 0;
        $green = 0;
        $blue = 0;
        $alpha = 0;

        foreach ($corners as $corner) {
            $color = $this->image->pickColor($corner[0], $corner[1]);

            $red += $color->red()->toInt();
            $green += $color->green()->toInt();
            $blue += $color->blue()->toInt();
            $alpha+= $color->alpha()->toInt();
        }

        $red /= 4;
        $green /= 4;
        $blue /= 4;
        $alpha /= 4;

        if ($red == $green && $red == $blue && $red >= 250) {
            $this->trimWithBorder(50);
            $this->contain($width, $height);

        } else {
            $this->cover($width, $height);
        }
    }
}
