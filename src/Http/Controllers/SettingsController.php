<?php

namespace LaravelCms\Http\Controllers;

use LaravelCms\Form\Fields\Input;
use LaravelCms\Form\Fields\TextArea;
use LaravelCms\Http\Controllers\ModuleController;
use Illuminate\Validation\Rule;
use LaravelCms\Models\Cms\Setting;

class SettingsController extends ModuleController
{
    protected $className = Setting::class;

    protected $relations = [];

    protected $grid = [
        'class' => Setting::class,
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
            'title' => [
                'label' => 'Название',
                'is-sortable' => true,
                'type' => 'string',
                'filter' => true,
            ],
            'key' => [
                'label' => 'Ключ',
                'is-sortable' => true,
                'type' => 'string',
                'filter' => true,
            ],
        ]
    ];

    /**
     * Array of validation rules
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:128',
            'key' => ['required', Rule::unique('settings')->ignore($this->objectId), 'max:64'],
            'value' => 'nullable|max:256',
        ];
    }

    /**
     * Return array of form fields
     * @return array
     */
    protected function formFields(): array
    {
        return [
            Input::make('title')
                ->title('Название')
                ->required()
                ->horizontal(),
            Input::make('key')
                ->title('Ключ')
                ->required()
                ->horizontal(),
            TextArea::make('value')
                ->title('Значение')
                ->rows(5)
                ->horizontal(),
        ];
    }
}
