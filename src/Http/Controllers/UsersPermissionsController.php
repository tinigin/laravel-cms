<?php

namespace LaravelCms\Http\Controllers;

use LaravelCms\Models\Cms\Permission;
use Illuminate\Validation\Rule;
use LaravelCms\Form\Fields\Input;
use LaravelCms\Form\Fields\Select;
use LaravelCms\Http\Controllers\ModuleController;

class UsersPermissionsController extends ModuleController
{
    protected $className = Permission::class;

    protected $relations = [];

    protected $grid = [
        'class' => Permission::class,
        'sortable' => false,
        'add' => true,
        'delete' => true,
        'multiple-delete' => false,
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
            ]
        ]
    ];

    /**
     * Array of validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
        ];
    }

    /**
     * Return array of form fields
     * @return array
     */
    protected function formFields(): array
    {
        return [
            Input::make('name')
                ->title('Название')
                ->required()
                ->horizontal(),
        ];
    }
}
