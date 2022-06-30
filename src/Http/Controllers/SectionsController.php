<?php

namespace LaravelCms\Http\Controllers;

use LaravelCms\Form\Fields\Input;
use LaravelCms\Form\Fields\Switcher;
use LaravelCms\Form\Fields\TextArea;
use LaravelCms\Form\Fields\Select;
use LaravelCms\Http\Controllers\ModuleController;
use LaravelCms\Models\Cms\Section;
use Illuminate\Validation\Rule;

class SectionsController extends ModuleController
{
    protected $className = Section::class;

    protected $relations = [
        'users'
    ];

    protected $grid = [
        'class' => Section::class,
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
            'folder' => [
                'label' => 'Папка',
                'is-sortable' => true,
                'type' => 'string',
                'filter' => true,
            ],
            'group' => [
                'label' => 'Группа',
                'is-sortable' => false,
                'type' => 'multiple',
                'class' => \LaravelCms\Models\Cms\SectionGroup::class,
                'filter' => true,
            ],
            'users' => [
                'label' => 'Пользователи',
                'is-sortable' => false,
                'type' => 'multiple',
                'class' => \LaravelCms\Models\Cms\User::class,
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
            'folder' => ['required', Rule::unique('cms_sections')->ignore($currentObjectId), 'max:255'],
            'cms_section_group_id' => 'required',
            'is_published' => 'boolean',
            'users' => 'nullable',
            'general' => 'nullable'
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
            Input::make('folder')
                ->title('Папка')
                ->placeholder('Например: news')
                ->required()
                ->horizontal(),
            Select::make('cms_section_group_id')
                ->title('Группа')
                ->fromModel(\LaravelCms\Models\Cms\SectionGroup::class, 'name')
                ->required()
                ->horizontal(),
            Select::make('users')
                ->title('Пользователи')
                ->fromModel(\LaravelCms\Models\Cms\User::class, 'name')
                ->multiple()
                ->horizontal(),
            TextArea::make('description')
                ->title('Описание')
                ->rows(5)
                ->horizontal(),
            Switcher::make('is_published')
                ->title('Публиковать')
                ->value(1)
                ->horizontal(),
        ];
    }
}
