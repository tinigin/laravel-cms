<?php

namespace LaravelCms\Form;

use Closure;
use Illuminate\Database\Eloquent\Model;
use LaravelCms\Form\Contracts\Fieldable;
use LaravelCms\Form\Contracts\Groupable;
use Throwable;
use LaravelCms\Form\Contracts\Tabable;
use LaravelCms\Form\Fields\Tab;

class Builder
{
    /**
     * Items to be displayed in the form.
     *
     * @var Fieldable[]|mixed
     */
    public $items;

    /**
     * Transmitted values for display in a form.
     *
     * @var Model|Repository
     */
    public $data;

    /**
     * The form language.
     *
     * @var string|null
     */
    public $language;

    /**
     * The form prefix.
     *
     * @var string|null
     */
    public $prefix;

    /**
     * Type of render
     * @var string
     */
    public $view = 'default';

    /**
     * Tabs
     * @var Tabable[]
     */
    protected $tabs = [];

    /**
     * Fields
     * @var Fieldable[]
     */
    protected $fields = [];

    /**
     * Buttons
     * @var Action[]
     */
    protected $buttons = [];

    /**
     * HTML form string.
     *
     * @var string
     */
    protected $form = '';

    /**
     * Form action
     * @var string
     */
    protected $action = '';

    /**
     * Method
     * @var string
     */
    protected $method = 'POST';

    /**
     * Builder constructor.
     *
     * @param Fieldable[]     $items
     * @param Repository|null $data
     */
    public function __construct(iterable $items, Repository $data = null)
    {
        $this->items = collect($items);
        $this->data = $data ?? new Repository();
    }

    /**
     * Push element to the form
     * @param $item
     * @return $this
     */
    public function push(mixed $item): self
    {
        $this->items->push($item);
        return $this;
    }

    /**
     * @param string|null $language
     *
     * @return $this
     */
    public function setLanguage(string $language = null): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param string|null $action
     *
     * @return $this
     */
    public function setAction(string $action = null): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param string|null $prefix
     *
     * @return $this
     */
    public function setPrefix(string $prefix = null): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Setting form method
     * @param string $method
     * @return $this
     */
    public function method(string $method = 'POST'): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set type of view type
     * @param string $value
     */
    public function view(string $value = 'default'): self
    {
        $this->view = $value;
        return $this;
    }

    /**
     * Generate a ready-made html form for display to the user.
     *
     * @throws Throwable
     *
     * @return string
     */
    public function generateForm(): string
    {
        collect($this->fields)->each(function (Fieldable $field) {
            if (is_subclass_of($field, Tabable::class)) {
                $this->form .= $this->renderTabs($field);
            } else if (is_subclass_of($field, Groupable::class)) {
                $this->form .= $this->renderGroup($field);
            } else {
                $this->form .= $this->renderField($field);
            }
        });

        return $this->form;
    }

    /**
     * Generate a ready-made html form for display to the user.
     *
     * @throws Throwable
     *
     * @return string
     */
    public function render()
    {
        $this->process();

        switch ($this->view) {
            case 'default':
                return $this->generateForm();

            case 'card':
                return view('cms::form.builder', [
                    'view' => $this->view,
                    'tabs' => $this->tabs,
                    'fields' => $this->fields,
                    'buttons' => $this->buttons,
                    'action' => $this->action,
                    'method' => $this->method
                ]);
        }
    }

    /**
     * Process form data
     * @return self
     */
    protected function process(): self
    {
        $this->items->each(function ($item) {
            if (is_subclass_of($item, Tabable::class)) {
                $this->tabs[] = $item;
            } elseif (is_subclass_of($item, Action::class)) {
                $this->buttons[] = $item;
            }elseif (is_subclass_of($item, Groupable::class)) {
                $this->fields[] = $this->renderGroup($item);
            }elseif (is_subclass_of($item, Fieldable::class)) {
                $this->fields[] = $this->renderField($item);
            }
        });

        return $this;
    }

    /**
     * @param Groupable $group
     *
     * @throws \Throwable
     *
     * @return array|string
     */
    private function renderGroup(Groupable $group)
    {
        $prepare = collect($group->getGroup())->map(function ($field) {
            return $this->render($field);
        })
            ->filter()
            ->toArray();

        return $group->setGroup($prepare)->render();
    }

    /**
     * Render field for forms.
     *
     * @param Fieldable $field
     *
     * @throws Throwable
     *
     * @return mixed
     */
    private function renderField(Fieldable $field)
    {
        $field->set('lang', $this->language);
        $field->set('prefix', $this->buildPrefix($field));

        foreach ($this->fill($field->getAttributes()) as $key => $value) {
            $field->set($key, $value);
        }

        return $field->render();
    }

    /**
     * @param Fieldable $field
     *
     * @return string|null
     */
    private function buildPrefix(Fieldable $field): ?string
    {
        return $field->get('prefix', $this->prefix);
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    private function fill(array $attributes): array
    {
        $name = $attributes['name'];

        $bindValueName = rtrim($name, '.');
        $attributes['value'] = $this->getValue($bindValueName, $attributes['value'] ?? null);

        //set prefix
        if ($attributes['prefix'] !== null) {
            $name = '.'.$name;
        }

        $attributes['name'] = self::convertDotToArray($name);

        return $attributes;
    }

    /**
     * Gets value of Repository.
     *
     * @param string     $key
     * @param mixed|null $value
     *
     * @return mixed
     */
    private function getValue(string $key, $value = null)
    {
        if ($this->language !== null) {
            $key = $this->language.'.'.$key;
        }

        if ($this->prefix !== null) {
            $key = $this->prefix.'.'.$key;
        }

        $data = $this->data->getContent($key);

        // default value
        if ($data === null) {
            return $value;
        }

        if ($value instanceof Closure) {
            return $value($data, $this->data);
        }

        return $data;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function convertDotToArray(string $string): string
    {
        $name = '';
        $binding = explode('.', $string);

        foreach ($binding as $key => $bind) {
            $name .= $key === 0 ? $bind : '['.$bind.']';
        }

        return $name;
    }

    public function __toString()
    {
        return $this->render()->toHtml();
        ;
    }
}
