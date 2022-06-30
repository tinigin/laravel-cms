<?php

namespace LaravelCms\Form;

use LaravelCms\Form\Field;
use LaravelCms\Form\Repository;
use LaravelCms\Form\Contracts\Actionable;

class Action extends Field implements Actionable
{
    /**
     * Override the form view.
     *
     * @var string
     */
    protected $typeForm = 'cms::partials.fields.clear';

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'type',
        'autofocus',
        'disabled',
        'tabindex',
    ];

    /**
     * A set of attributes for the assignment
     * of which will automatically translate them.
     *
     * @var array
     */
    protected $translations = [
        'name',
    ];

    /**
     * @param string|null $name
     *
     * @return self
     */
    public function name(string $name = null): self
    {
        return $this->set('name', $name ?? '');
    }

    /**
     * @param string $visual
     *
     * @return static
     */
    public function type(string $visual): self
    {
        $this->set('class', 'btn btn-'.$visual);

        return $this;
    }

    /**
     * @param Repository|null $repository
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function build(Repository $repository = null)
    {
        return $this->render();
    }

    /**
     * @return string
     */
    protected function getId(): ?string
    {
        return $this->get('id');
    }
}
