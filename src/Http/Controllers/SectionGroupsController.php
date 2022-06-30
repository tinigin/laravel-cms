<?php

namespace LaravelCms\Http\Controllers;

use LaravelCms\Form\Fields\Input;
use LaravelCms\Form\Fields\Switcher;
use LaravelCms\Models\Cms\SectionGroup;

class SectionGroupsController extends ModuleController
{
    protected $className = SectionGroup::class;

    protected $grid = [
        'class' => SectionGroup::class,
        'sortable' => true,
        'add' => true,
        'delete' => true,
        'multiple-delete' => true,
        'limit' => 25,
        'columns' => [
            'id' => [
                'label' => '№',
                'is-sortable' => true,
                'type' => 'number',
                'filter' => false,
            ],
            'name' => [
                'label' => 'Название',
                'is-sortable' => true,
                'type' => 'string',
                'filter' => true,
            ],
            'is_published' => [
                'label' => 'Публиковать',
                'is-sortable' => true,
                'type' => 'boolean',
                'options' => [
                    false => 'Нет',
                    true => 'Да'
                ],
                'filter' => true,
            ],
        ]
    ];

    /**
     * Array of validation rules
     * @return array
     */
    public function rules($currentObjectId = null): array
    {
        return [
            'name' => 'required|max:255',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Return array of form fields
     * @return array
     */
    protected function getFormFields(): array
    {
        return [
            Input::make('name')
                ->title('Название')
                ->required()
                ->horizontal(),
            Switcher::make('is_published')
                ->title('Публиковать')
                ->value(1)
                ->horizontal(),
        ];
    }
}
