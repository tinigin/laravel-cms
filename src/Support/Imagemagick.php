<?php

namespace LaravelCms\Support;

class Imagemagick {
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    public function convertToRgb()
    {
        $colorspace = shell_exec("identify -format '%[colorspace]' {$this->filepath}");
        if ($colorspace == 'CMYK') {
            shell_exec("magick convert {$this->filepath} -colorspace sRGB -type truecolor {$this->filepath}");
        }
    }

    public function scale($width, $height)
    {
        shell_exec("magick {$this->filepath} -scale {$width}x{$height} {$this->filepath}");
    }

    public function getimagesize()
    {
        $result = shell_exec("magick identify -format \"%wx%h\" {$this->filepath}");
        
        return explode("x", $result);
    }
}