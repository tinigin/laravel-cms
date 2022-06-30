<?php

namespace LaravelCms\Form\Actions;

use LaravelCms\Form\Action;

/**
 * Class Button.
 *
 * @method Button name(string $name = null)
 * @method Button modal(string $modalName = null)
 * @method Button icon(string $icon = null)
 * @method Button class(string $classes = null)
 * @method Button parameters(array|object $name)
 * @method Button confirm(string $confirm = true)
 * @method Button action(string $url)
 * @method Button disabled(bool $disabled)
 */
class Button extends Action
{
    /**
     * @var string
     */
    protected $view = 'cms::actions.button';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class'      => 'btn btn-default',
        'type'       => 'submit',
        'label'      => null,
        'method'     => null,
        'icon'       => null,
        'action'     => null,
        'confirm'    => null,
        'parameters' => [],
        'onclick'    => null,
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'form',
        'autofocus',
        'disabled',
        'tabindex',
        'name',
        'value',
        'onclick'
    ];

    /**
     * Button constructor.
     */
    public function __construct()
    {
        $this->addBeforeRender(function () {
            if ($this->get('action') !== null) {
                return;
            }

            // correct URL for async request
            $url = request()->header('ORCHID-ASYNC-REFERER', url()->current());

            $query = http_build_query($this->get('parameters'));

            $action = rtrim("{$url}/{$this->get('method')}?{$query}", '/?');
            $this->set('action', $action);
        })->addBeforeRender(function () {
            $action = $this->get('action');

            if ($action !== null) {
                $this->set('formaction', $action);
            }
        });
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return $this
     */
    public function method(string $name, array $parameters = []): self
    {
        return $this
            ->set('method', $name)
            ->when(! empty($parameters), function () use ($parameters) {
                $this->set('parameters', $parameters);
            });
    }
}
