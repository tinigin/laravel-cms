<?php

namespace LaravelCms\Tests\Unit;

use Illuminate\Support\Collection;
use LaravelCms\Tests\TestUnitCase;
use Symfony\Component\Finder\Finder;

class LocaleTest extends TestUnitCase
{
    public function testLoadTranslations(): void
    {
        $this->assertEquals(trans('90 Days', [], 'ru'), '90 дней');
    }

    public function testTranslationsJsonValidation(): void
    {
        $this->getTranslationFiles()
            ->each(function (string $file) {
                $content = file_get_contents($file);

                $this->assertJson($content);
            });
    }

//    public function testUsageAllTranslateString(): void
//    {
//        $this->getTranslationFiles()
//            ->map(function (string $file) {
//                $content = file_get_contents($file);
//
//                $this->assertJson($content, "$file is not valid JSON");
//
//                return array_keys(json_decode($content, true));
//            })
//            ->flatten()
//            ->unique()
//            ->each(function (string $translate) {
//                $this->assertTrue(
//                    $this->checkUsageTranslateStringInProject($translate),
//                    "The string '$translate' is not used in the project!"
//                );
//            });
//    }

    /**
     * @return Collection
     */
    protected function getTranslationFiles(): Collection
    {
        $patternPath = __DIR__ . '/../../resources/lang' . '/*.json';

        return collect(glob($patternPath));
    }

    /**
     * This solution checks for the presence of the
     * passed string in the project source codes.
     * She can only say that there is definitely no word.
     * Will give positive results on popular words that
     * can be used to name variables, properties, classes.
     *
     * @param string $string
     *
     * @return bool
     */
    protected function checkUsageTranslateStringInProject(string $string): bool
    {
        return (new Finder())
            ->ignoreUnreadableDirs()
            ->followLinks()
            ->in([
                __DIR__ . '/../../src',
                __DIR__ . '/../../stubs',
                __DIR__ . '/../../resources/views',
            ])
            ->contains($string)
            ->hasResults();
    }
}
