<?php

namespace LaravelCms\Form\Contracts;

use LaravelCms\Form\Field;

interface Tabable extends Fieldable
{
    /**
     * @return Field[]
     */
    public function getFields(): array;

    /**
     * @param array $fields
     *
     * @return Tabable
     */
    public function setFields(array $fields = []): self;
}
