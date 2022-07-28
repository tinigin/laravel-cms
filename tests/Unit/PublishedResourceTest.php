<?php

namespace LaravelCms\Tests\Unit;

use LaravelCms\Support\Formats;
use LaravelCms\Tests\TestUnitCase;

class PublishedResourceTest extends TestUnitCase
{
    /**
     * This test will check the size of the published files,
     * so as not to accidentally publish non-minified versions.
     *
     * Usually to solve this problem you only need to run the Laravel Mix:
     *  `npm run production`
     *
     * These are approximate values that can be changed.
     */
    public function testFilesAreMinified(): void
    {
        $maxCssSize = 500 * 1028; //  ~500 kb
        $maxJsSize = 500 * 1028; // ~500 kb

        $this->assertLessThan($maxCssSize,
            filesize(public_path('/assets/cms/css/app.css')),
            'File app.css more ' . Formats::formatBytes($maxCssSize)
        );

        $this->assertLessThan($maxJsSize,
            filesize(public_path('/assets/cms/js/app.js')),
            'File app.js more ' . Formats::formatBytes($maxJsSize)
        );
    }
}
