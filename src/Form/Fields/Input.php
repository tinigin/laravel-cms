<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Concerns\Multipliable;
use LaravelCms\Form\Field;

/**
 * Class Input.
 *
 * @method Input accept($value = true)
 * @method Input autocomplete($value = true)
 * @method Input autofocus($value = true)
 * @method Input checked($value = true)
 * @method Input disabled($value = true)
 * @method Input max(int $value)
 * @method Input maxlength(int $value)
 * @method Input min(int $value)
 * @method Input minlength(int $value)
 * @method Input name(string $value = null)
 * @method Input pattern($value = true)
 * @method Input placeholder(string $value = null)
 * @method Input readonly($value = true)
 * @method Input required(bool $value = true)
 * @method Input size($value = true)
 * @method Input src($value = true)
 * @method Input tabindex($value = true)
 * @method Input type($value = true)
 * @method Input value($value = true)
 * @method Input help(string $value = null)
 * @method Input mask($value = true)
 * @method Input title(string $value = null)
 */
class Input extends Field
{
    use Multipliable;

    /**
     * @var string
     */
    protected $view = 'cms::form.fields.input';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class'    => 'form-control',
        'datalist' => [],
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'accept',
        'autocomplete',
        'autofocus',
        'checked',
        'disabled',
        'list',
        'max',
        'maxlength',
        'min',
        'minlength',
        'name',
        'pattern',
        'placeholder',
        'readonly',
        'required',
        'size',
        'src',
        'tabindex',
        'type',
        'value',
        'mask',
    ];

    /**
     * Input constructor.
     */
    public function __construct()
    {
        $this->addBeforeRender(function () {
            $mask = $this->get('mask');

            if (is_array($mask)) {
                $this->set('mask', json_encode($mask));
            }
        });
    }

    /**
     * @param array $datalist
     *
     * @return Input
     */
    public function datalist(array $datalist = []): self
    {
        if (empty($datalist)) {
            return $this;
        }

        $this->set('datalist', $datalist);

        return $this->addBeforeRender(function () {
            $this->set('list', 'datalist-'.$this->get('name'));
        });
    }
}
