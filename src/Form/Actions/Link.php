<?php

namespace LaravelCms\Form\Actions;

use LaravelCms\Form\Action;

/**
 * Class Link.
 *
 * @method Link name(string $name = null)
 * @method Link class(string $classes = null)
 * @method Link parameters(array|object $name)
 * @method Link target(string $target = null)
 * @method Link title(string $title = null)
 * @method Link download($download = true)
 */
class Link extends Action
{
    /**
     * @var string
     */
    protected $view = 'cms::actions.link';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class' => 'btn btn-default',
        'href'  => '#!',
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    public $inlineAttributes = [
        'autofocus',
        'disabled',
        'tabindex',
        'href',
        'target',
        'title',
        'download',
        'confirm',
        'prompt',
    ];

    /**
     * Set the link.
     *
     * @param string $link
     *
     * @return $this
     */
    public function href(string $link = ''): self
    {
        $this->set('href', $link);

        return $this;
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param bool   $absolute
     *
     * @return $this
     */
    public function route(string $name, $parameters = [], $absolute = true): self
    {
        $route = route($name, $parameters, $absolute);

        return $this->href($route);
    }
}
