<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Field;

/**
 * Class Password.
 *
 * @method Password autocomplete($value = true)
 * @method Password autofocus($value = true)
 * @method Password checked($value = true)
 * @method Password disabled($value = true)
 * @method Password maxlength(int $value)
 * @method Password min(int $value)
 * @method Password name(string $value = null)
 * @method Password pattern($value = true)
 * @method Password placeholder(string $value = null)
 * @method Password readonly($value = true)
 * @method Password required(bool $value = true)
 * @method Password help(string $value = null)
 * @method Password title(string $value = null)
 */
class Password extends Field
{
    /**
     * @var string
     */
    protected $view = 'cms::form.fields.password';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'type'  => 'password',
        'class' => 'form-control',
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'autocomplete',
        'autofocus',
        'checked',
        'disabled',
        'maxlength',
        'min',
        'name',
        'pattern',
        'placeholder',
        'readonly',
        'required',
        'src',
        'tabindex',
        'type',
    ];
}
