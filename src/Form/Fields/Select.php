<?php

namespace LaravelCms\Form\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelCms\Form\Concerns\ComplexFieldConcern;
use LaravelCms\Form\Concerns\Multipliable;
use LaravelCms\Form\Field;

/**
 * Class Select.
 *
 * @method Select accesskey($value = true)
 * @method Select autofocus($value = true)
 * @method Select disabled($value = true)
 * @method Select name(string $value = null)
 * @method Select required(bool $value = true)
 * @method Select size($value = true)
 * @method Select tabindex($value = true)
 * @method Select help(string $value = null)
 * @method Select popover(string $value = null)
 * @method Select options($value = null)
 * @method Select title(string $value = null)
 * @method Select maximumSelectionLength(int $value = 0)
 */
class Select extends Field implements ComplexFieldConcern
{
    use Multipliable;

    /**
     * @var string
     */
    protected $view = 'cms::form.fields.select';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class'   => 'form-control selectpicker',
        'options' => [],
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'accesskey',
        'autofocus',
        'disabled',
        'name',
        'required',
        'size',
        'tabindex',
        'tags',
        'maximumSelectionLength',
    ];

    /**
     * @param string|Builder|Model $model
     * @param string       $name
     * @param string|null  $key
     *
     * @return self
     */
    public function fromModel($model, string $name, string $key = null): self
    {
        /* @var $model Model */
        $model = is_object($model) ? $model : new $model();
        $key = $key ?? $model->getModel()->getKeyName();

        return $this->setFromEloquent($model, $name, $key);
    }

    /**
     * @param Builder|Model $model
     * @param string        $name
     * @param string        $key
     *
     * @return self
     */
    private function setFromEloquent($model, string $name, string $key): self
    {
        $options = $model->pluck($name, $key);

        if ($this->get('required') === false && !$this->get('multiple')) {
            $options->prepend(__('Choose an option'), '');
        }
        $this->set('options', $options);

        return $this->addBeforeRender(function () use ($name) {
            $value = [];

            collect($this->get('value'))->each(static function ($item) use (&$value, $name) {
                if (is_object($item)) {
                    $value[$item->id] = $item->$name;
                } else {
                    $value[] = $item;
                }
            });

            $this->set('value', $value);
        });
    }

    /**
     * @param Builder     $builder
     * @param string      $name
     * @param string|null $key
     *
     * @return self
     */
    public function fromQuery(Builder $builder, string $name, string $key = null): self
    {
        $key = $key ?? $builder->getModel()->getKeyName();

        return $this->setFromEloquent($builder->get(), $name, $key);
    }

    /**
     * @param string $name
     * @param string $key
     *
     * @return self
     */
    public function empty(string $name = '', string $key = ''): self
    {
        return $this->addBeforeRender(function () use ($name, $key) {
            $options = $this->get('options', []);

            if (! is_array($options)) {
                $options = $options->toArray();
            }

            $value = [$key => $name] + $options;

            $this->set('options', $value);
        });
    }

    public function taggable()
    {
        return $this->set('tags', true);
    }
}
