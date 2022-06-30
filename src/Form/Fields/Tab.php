<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Contracts\Fieldable;
use LaravelCms\Form\Contracts\Tabable;
use LaravelCms\Form\Field;
use Illuminate\Support\Facades\Cookie;

class Tab implements Fieldable, Tabable
{
    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'fields'          => [],
        'name'            => null,
        'title'           => null,
        'active'          => false
    ];

    /**
     * Required Attributes.
     *
     * @var array
     */
    protected $required = [];

    /**
     * @var string
     */
    protected $view = 'cms::form.fields.tab';

    /**
     * @param array $group
     *
     * @return static
     */
    public static function make(array $fields = [])
    {
        return (new static())->setFields($fields);
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->get('fields', []);
    }

    /**
     * @param array $tab
     *
     * @return $this
     */
    public function setFields(array $fields = []): self
    {
        return $this->set('fields', $fields);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->process();

        return view($this->view, $this->attributes);
    }

    protected function process()
    {
        $activeTab = Cookie::get('active_tab', $this->get('name'));
        $this->set('active', $activeTab == $this->get('name') ?: false);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return static
     */
    public function set(string $key, $value = true): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param string     $key
     * @param mixed|null $value
     *
     * @return static|mixed|null
     */
    public function get(string $key, $value = null)
    {
        return $this->attributes[$key] ?? $value;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function form(string $name): self
    {
        $tab = array_map(function ($field) use ($name) {
            return $field->form($name);
        }, $this->getTab());

        return $this->setTab($tab);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }

    public function renderNavItem()
    {
        $this->process();

        return view('cms::form.fields.tab-item', $this->getAttributes());
    }

    /**
     * @return $this
     */
    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    /**
     * @return $this
     */
    public function title(string $title): self
    {
        return $this->set('title', $title);
    }
}
