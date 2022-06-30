<?php

namespace LaravelCms\Form\Contracts;

use LaravelCms\Form\Field;

interface Groupable extends Fieldable
{
    /**
     * @return Field[]
     */
    public function getGroup(): array;

    /**
     * @param array $group
     *
     * @return Groupable
     */
    public function setGroup(array $group = []): self;
}
