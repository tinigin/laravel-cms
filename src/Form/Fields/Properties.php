<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Field;

class Properties extends Field
{
    /**
     * @var string
     */
    protected $view = 'cms::form.fields.properties';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'url' => '',
    ];

    public function exclude(string $url): self
    {
        return $this->set('url', $url);
    }
}
