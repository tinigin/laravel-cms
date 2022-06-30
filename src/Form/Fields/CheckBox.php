<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Field;

/**
 * Class CheckBox.
 *
 * @method CheckBox autofocus($value = true)
 * @method CheckBox checked($value = true)
 * @method CheckBox disabled($value = true)
 * @method CheckBox name(string $value = null)
 * @method CheckBox placeholder(string $value = null)
 * @method CheckBox readonly($value = true)
 * @method CheckBox required(bool $value = true)
 * @method CheckBox tabindex($value = true)
 * @method CheckBox value($value = true)
 * @method CheckBox help(string $value = null)
 * @method CheckBox sendTrueOrFalse($value = true)
 * @method CheckBox title(string $value = null)
 */
class CheckBox extends Field
{
    /**
     * @var string
     */
    protected $view = 'cms::form.fields.checkbox';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'type'          => 'checkbox',
        'class'         => 'form-check-input',
        'value'         => false,
        'novalue'       => 0,
        'yesvalue'      => 1,
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
        'value',
        'type',
        'novalue',
        'yesvalue',
    ];
}
