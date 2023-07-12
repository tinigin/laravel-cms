<?php

namespace LaravelCms\Form;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use LaravelCms\Form\Contracts\Fieldable;
use LaravelCms\Form\Contracts\Groupable;
use Throwable;

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
     * @var array
     */
    protected $groups = [];

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
     * Tabs
     * @var array
     */
    protected $images = [];

    /**
     * Is readonly
     *
     * @var bool
     */
    protected $readonly = false;

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

    public function readonly(bool $value = true)
    {
        $this->readonly = $value;
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
     * Set|Get groups
     * @param $groups
     * @return array|$this
     */
    public function groups($groups = null): self|array
    {
        if ($groups) {
            $this->groups = $groups;
            return $this;
        }

        return $this->groups;
    }

    public function images(string $url): self
    {
        $this->images = $url;

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
    public function render()
    {
        $this->process();

        switch ($this->view) {
            case 'default':
            case 'card':
                return view('cms::form.builder', [
                    'view' => $this->view,
                    'groups' => $this->groups(),
                    'fields' => $this->fields,
                    'buttons' => $this->buttons,
                    'action' => $this->action,
                    'method' => $this->method,
                    'images' => $this->images,
                    'readonly' => $this->readonly
                ]);
        }
    }

    /**
     * Process form data
     * @return self
     */
    public function process(): self
    {
        if ($this->groups()) {
            $activeTab = request()->cookie('active_tab', null);
            if ($activeTab == 'undefined' || !$activeTab) {
                $activeTab = array_key_first($this->groups);
            }

            $groups = [];
            foreach ($this->groups() as $groupKey => $groupTitle) {
                $groups[$groupKey]['title'] = $groupTitle;
                $groups[$groupKey]['fields'] = [];
                $groups[$groupKey]['errors'] = 0;

                if ($activeTab == $groupKey) {
                    $groups[$groupKey]['active'] = true;
                } else {
                    $groups[$groupKey]['active'] = false;
                }
            }

            $this->groups($groups);
            unset($groups);
        }

        $this->items->each(function ($item) {
            $f = &$this->fields;
            if ($item->get('group')) {
                if (!array_key_exists($item->get('group'), $this->fields))
                    $this->fields[$item->get('group')] = [];
                $f = &$this->fields[$item->get('group')];
            }

            if (is_subclass_of($item, Action::class)) {
                $this->buttons[] = $item;
            }elseif (is_subclass_of($item, Groupable::class)) {
                $f[] = $this->renderGroup($item);
            }elseif (is_subclass_of($item, Fieldable::class)) {
                if ($this->readonly)
                    $item->readonly(true);
                
                $f[] = $this->renderField($item);

                if ($item->hasError() && $item->get('group')) {
                    $this->groups[$item->get('group')]['errors'] += 1;
                }
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
    }
}
