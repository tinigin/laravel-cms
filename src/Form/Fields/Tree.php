<?php

namespace LaravelCms\Form\Fields;

use LaravelCms\Form\Concerns\Multipliable;
use Illuminate\Database\Eloquent\Model;
use LaravelCms\Form\Field;

/**
 * Class CheckBox.
 *
 * @method CheckBox name(string $value = null)
 * @method CheckBox readonly($value = true)
 * @method CheckBox required(bool $value = true)
 * @method CheckBox title(string $value = null)
 */
class Tree extends Field
{
    use Multipliable;

    /**
     * @var string
     */
    protected $view = 'cms::form.fields.tree';

    /**
     * Default attributes value.
     *
     * @var array
     */
    protected $attributes = [
        'class' => 'tree',
        'value' => [],
        'tree' => null,
        'exclude' => null
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'name',
        'required',
    ];

    public function model($model): self
    {
        /* @var $model Model */
        $model = is_object($model) ? $model : new $model();
        $exclude = $this->get('exclude');

        $tree = $model->toTree(exclude: $exclude);

        if (!$this->get('required')) {
            if ($tree)
                $tree = [[
                    'id' => NULL,
                    'name' => 'Не выбрано',
                    'folder' => 'none',
                    'url' => '',
                    'children' => $tree
                ]];
            else
                $tree = [[
                    'id' => NULL,
                    'name' => 'Не выбрано',
                    'folder' => 'none',
                    'url' => '',
                ]];
        }

        return $this->set('tree', $tree);
    }

    public function exclude(int $id = null): self
    {
        return $this->set('exclude', [$id]);
    }

    public function set(string $key, $value = true): Field
    {
        if ($key == 'value' && !is_array($value))
            $value = [$value];

        return parent::set($key, $value);
    }
}
