<?php

namespace LaravelCms\Form\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelCms\Form\Concerns\ComplexFieldConcern;
use LaravelCms\Form\Concerns\Multipliable;
use LaravelCms\Form\Field;

/**
 * Class Hidden.
 *
 * @method Select name(string $value = null)
 * @method Select required(bool $value = true)
 * @method Select title(string $value = null)
 */
class Hidden extends Field implements ComplexFieldConcern
{
    /**
     * @var string
     */
    protected $view = 'cms::form.fields.hidden';

    protected $attributes = [
        'type' => 'hidden',
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'name',
        'required',
        'type'
    ];
}
