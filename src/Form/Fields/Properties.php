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

    public function render()
    {
        $id = $this->getId();
        $this->set('id', $id);
        return view($this->view, array_merge($this->getAttributes(), [
            'attributes'     => $this->getAllowAttributes(),
            'dataAttributes' => $this->getAllowDataAttributes(),
            'id'             => $id,
            'slug'           => $this->getSlug(),
            'oldName'        => $this->getOldName(),
            'typeForm'       => $this->typeForm ?? $this->vertical()->typeForm,
            'settings'       => $this->settings,
            'readonly'       => $this->get('readonly', false),
        ]));
    }
}
