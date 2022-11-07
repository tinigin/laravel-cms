<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Field;

/**
 * Class CheckBox.
 *
 * @method CheckBox name(string $value = null)
 */
class External extends Field
{
    /**
     * @var string
     */
    protected $view = 'cms::form.fields.external';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'url' => '',
        'parent_id' => null
    ];

    public function exclude(string $url): self
    {
        return $this->set('url', $url);
    }

    public function parentId(int $id = null): self
    {
        return $this->set('parent_id', $id);
    }
}
