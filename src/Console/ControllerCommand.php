<?php

namespace LaravelCms\Console;

use Illuminate\Console\GeneratorCommand;

class ControllerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cms:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new CMS controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Http\Controllers\Cms';
    }
}
