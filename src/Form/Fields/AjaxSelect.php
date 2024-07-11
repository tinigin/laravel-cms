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
 * @method AjaxSelect name(string $value = null)
 * @method AjaxSelect required(bool $value = true)
 * @method AjaxSelect help(string $value = null)
 * @method AjaxSelect options($value = null)
 * @method AjaxSelect title(string $value = null)
 */
class AjaxSelect extends Field implements ComplexFieldConcern
{
    use Multipliable;

    /**
     * @var string
     */
    protected $view = 'cms::form.fields.ajax-select';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class'   => 'form-control',
        'options' => [],
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'name',
        'required',
        'url'
    ];

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function getOldValue()
    {
        return $this->get('value');
    }

}
