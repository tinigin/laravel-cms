<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Field;

/**
 * Class Switcher.
 *
 * @method Switcher autocomplete($value = true)
 * @method Switcher autofocus($value = true)
 * @method Switcher checked($value = true)
 * @method Switcher disabled($value = true)
 * @method Switcher name(string $value = null)
 * @method Switcher placeholder(string $value = null)
 * @method Switcher readonly($value = true)
 * @method Switcher required(bool $value = true)
 * @method Switcher tabindex($value = true)
 * @method Switcher value($value = true)
 * @method Switcher help(string $value = null)
 * @method Switcher title(string $value = null)
 */
class Switcher extends Field
{
    /**
     * @var string
     */
    protected $view = 'cms::form.fields.switch';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'type'     => 'checkbox',
        'class'    => 'custom-control-input',
        'value'    => false,
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'autofocus',
        'checked',
        'disabled',
        'name',
        'placeholder',
        'readonly',
        'required',
        'tabindex',
        'type',
    ];
}
