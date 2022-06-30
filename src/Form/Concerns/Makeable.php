<?php

namespace LaravelCms\Form\Concerns;

trait Makeable
{
    /**
     * Create a new Field element.
     *
     * @param string|null $name
     *
     * @return static
     */
    public static function make(?string $name = null): self
    {
        return (new static)->name($name);
    }
}
