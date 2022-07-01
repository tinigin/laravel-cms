<?php

namespace LaravelCms\Http\Controllers;

use LaravelCms\Form\Fields\Input;
use LaravelCms\Form\Fields\Password;
use LaravelCms\Form\Fields\Switcher;
use LaravelCms\Form\Fields\TextArea;
use LaravelCms\Form\Fields\Select;
use LaravelCms\Http\Controllers\ModuleController;
use LaravelCms\Models\Cms\User;
use Illuminate\Validation\Rule;

class UsersController extends ModuleController
{
    protected $className = User::class;

    protected $relations = [
        'sections'
    ];

    protected $grid = [
        'class' => User::class,
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
                'label' => 'Имя',
                'is-sortable' => true,
                'type' => 'string',
                'filter' => true,
            ],
            'email' => [
                'label' => 'Email',
                'is-sortable' => true,
                'type' => 'email',
                'filter' => true,
            ],
            'sections' => [
                'label' => 'Модули',
                'is-sortable' => false,
                'type' => 'multiple',
                'class' => \LaravelCms\Models\Cms\Section::class,
                'filter' => true,
            ],
            'status_id' => [
                'label' => 'Доступ разрешен',
                'is-sortable' => true,
                'type' => 'boolean',
                'options' => [
                    0 => 'Нет',
                    1 => 'Да'
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
            'email' => ['required', Rule::unique('cms_users')->ignore($currentObjectId), 'max:255', 'email'],
            'password' => 'nullable|required_with:password_confirmation|string|confirmed|min:8',
            'status_id' => 'boolean',
            'sections' => 'nullable',
            'general' => 'nullable'
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
                ->title('Имя')
                ->required()
                ->horizontal(),
            Input::make('email')
                ->title('Email')
                ->placeholder('sample@yandex.ru')
                ->required()
                ->horizontal(),
            Select::make('sections')
                ->title('Доступные модули')
                ->fromModel(\LaravelCms\Models\Cms\Section::class, 'name')
                ->multiple()
                ->horizontal(),
            Password::make('password')
                ->title('Пароль')
                ->horizontal(),
            Password::make('password_confirmation')
                ->title('')
                ->help('Укажите пароль только если хотите его сменить.')
                ->horizontal(),
            Switcher::make('status_id')
                ->title('Доступ разрешён?')
                ->value(1)
                ->horizontal(),
        ];
    }
}
