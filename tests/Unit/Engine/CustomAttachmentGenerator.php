<?php

namespace LaravelCms\Tests\Unit\Engine;

use LaravelCms\Attachment\Engines\Generator;

class CustomAttachmentGenerator extends Generator
{
    /**
     * @return string
     */
    public function path(): string
    {
        return 'custom';
    }
}
