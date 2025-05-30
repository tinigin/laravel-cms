<?php

namespace LaravelCms\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LaravelCms\Facades\Alert;
use LaravelCms\Facades\Toast;
use LaravelCms\Form\Actions\Link;
use LaravelCms\Form\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use LaravelCms\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use LaravelCms\Table\Grid;

use LaravelCms\Form\Actions\Button;
use LaravelCms\Form\Builder;
use LaravelCms\Attachment\File;

class ModuleController extends BaseController
{
    /**
     * Class name for object
     *
     * @var string
     */
    protected $className = null;

    protected $relations = [];

    protected $difficult = [];

    protected $grid = [];

    protected $mode = 'default';

    protected $model = null;

    protected $objectId = null;

    protected $parentId = null;

    public $title = '';

    public $description = '';

    public $actions = [
        'add' => true,
        'view' => false,
        'edit' => true,
        'delete' => true,
        'multiple-delete' => true,
    ];

    public $readonly = false;

    public function before()
    {
        if (parent::before()) {
            if (request()->has('modal_parent_id')) {
                $this->parentId = request()->get('modal_parent_id');
                $this->mode = 'simple';
            }

            if (
                ($this->getSection()->is_published != true && !$this->parentId) ||
                !$this->getSection()->users()->where('id', Auth::id())->count()
            ) {
                return false;
            }

            if (request()->has('set-referer')) {
                request()->headers->add(['referer' => request()->get('set-referer')]);
            }

            return true;
        }
    }

    public function can(string $action)
    {
        return isset($this->actions[$action]) ? $this->actions[$action] : false;
    }

    public function isReadOnly()
    {
        return $this->readonly;
    }

    protected function getGridOptions()
    {
        $options = $this->grid;

        if (!isset($options['add']))
            $options['add'] = $this->can('add');

        if (!isset($options['view']))
            $options['view'] = $this->can('view');

        if (!isset($options['edit']))
            $options['edit'] = $this->can('edit');

        if (!isset($options['delete']))
            $options['delete'] = $this->can('delete');

        if (!isset($options['multiple-delete']))
            $options['multiple-delete'] = $this->can('multiple-delete');

        return $options;
    }

    public function index()
    {
        // sorting save
        if (request()->method() == 'POST') {
            if (request()->get('save-sorting') && request()->get('items')) {
                $items = explode(',', request()->get('items'));
                if (!empty($items)) {
                    $model = new $this->className;
                    $list = $this->className::whereIn('id', $items)
                        ->orderBy($model->defaultSortField, $model->defaultSortOrder)
                        ->get();

                    if ($list) {
                        $currentSort = [];
                        $items = array_flip($items);

                        foreach ($list as $item) {
                            $currentSort[] = $item->sort_order;
                        }

                        foreach ($list as $item) {
                            $newSortIndex = $currentSort[$items[$item->id]];
                            if ($newSortIndex) {
                                $item->sort_order = $newSortIndex;
                                $item->save();
                            }
                        }
                    }
                }

            } elseif (request()->get('multiple-delete') && request()->get('items')) {
                $items = explode(',', request()->get('items'));
                if (!empty($items)) {
                    $list = $this->className::destroy($items);
                }
            }

            redirect()->to(request()->fullUrl())->send();
        }

        $grid = new Grid($this->getGridOptions(), $this->getSection());

        if ($this->mode == 'simple')
            return view('cms::simple.module')
                ->with('grid', $grid)
                ->with('title', $this->getSection()->name);
        else
            return view('cms::module')
                ->with('grid', $grid)
                ->with('title', $this->getSection()->name);
    }

    protected function formFields(): array
    {
        return [];
    }

    protected function formGroups(): array
    {
        return [];
    }

    public function getTitle($default = ''): string
    {
        return $this->title ?: $default;
    }

    public function getDescription($default = ''): string
    {
        return $this->description ?: $default;
    }

    protected function getModel(): Model | null
    {
        if (is_null($this->model) && $this->objectId)
            $this->model = $this->className::findOrFail($this->objectId);

        return $this->model;
    }

    /**
     * Return form Builder object
     * @param boolean $create
     * @return Builder
     */
    protected function getForm(bool $create = false): Builder
    {
        $repository = null;

        $formFields = $this->formFields();

        if ($this->objectId) {
            $model = $this->className::findOrFail($this->objectId);
            $values = $model->getAttributes();

            if ($this->relations) {
                foreach ($this->relations as $relation) {
                    if (method_exists($model, "{$relation}Values")) {
                        $method = "{$relation}Values";
                        $values[$relation] = $model->$method();
                    } else
                        $values[$relation] = $model->$relation()->allRelatedIds()->toArray();
                }
            }

            if ($this->difficult) {
                foreach ($this->difficult as $key) {
                    $method = 'get' . Str::studly($key) . 'Attribute';
                    if (method_exists($model, $method)) {
                        $values[$key] = $model->$method();
                    }
                }
            }

            /**
             * @var Collection|array $files
             */
            $files = [];
            if (method_exists($model, 'attachment')) {
                $files = $model->attachment;
            }

            foreach ($formFields as $field) {
                if ($field instanceof \LaravelCms\Form\Fields\File) {
                    $group = str_replace('[]', '', $field->get('name'));

                    if ($files) {
                        $fieldFiles = $files->filter(function ($item) use ($group) {
                            return $item->group == $group;
                        });

                        if ($fieldFiles)
                            $values[$field->get('name')] = $fieldFiles;
                    }
                }
            }

            $repository = new Repository($values);
        }

        $form = new Builder($formFields, $repository);
        if ($this->readonly)
            $form->readonly(true);
        $form->groups($this->formGroups());

        if (!$this->readonly) {
            if ($create) {
                $form->push(
                    Button::make('create')
                        ->label('Добавить')
                        ->value('true')
                        ->class('btn btn-primary')
                );

            } else {
                $form->push(
                    Button::make('update')
                        ->label('Сохранить')
                        ->value('true')
                        ->class('btn btn-primary')
                );

                if ($this->can('delete'))
                    $form->push(
                        Link::make('Удалить')
                            ->href(route('cms.module.destroy', ['controller' => $this->getSectionController(), 'objectId' => $this->objectId], false))
                            ->class('btn btn-danger')
                            ->confirm('true')
                            ->withoutFormType()
                    );

                $form->images(route('cms.module.images', ['controller' => $this->getSectionController(), 'objectId' => $this->objectId], false));
            }
        }

        return $form;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $form = $this->getForm(true);

        $form->setAction(route('cms.module.store', ['controller' => $this->getSectionController()], false));

        if ($this->mode == 'simple')
            return view('cms::simple.module')
                ->with('form', $form->view('card'))
                ->with('title', $this->getTitle('Добавление'))
                ->with('description', $this->getDescription());
        else
            return view('cms::module')
                ->with('form', $form->view('card'))
                ->with('title', $this->getTitle('Добавление'))
                ->with('description', $this->getDescription());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->update();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     */
    public function edit($objectId)
    {
        $this->objectId = $objectId;

        if (method_exists($this, 'beforeEdit'))
            $this->beforeEdit();

        if ($this->getModel()->is_approved)
            return redirect(
                route(
                    'cms.module.view',
                    ['controller' => $this->getSectionController(), 'objectId' => $this->model->getKey()],
                    false
                ) . ($this->mode == 'simple' ? '?modal_parent_id=' . request()->get('modal_parent_id') : '')
            );

        $form = $this->getForm();

        $form->setAction(route('cms.module.update', [
            'controller' => $this->getSectionController(),
            'objectId' => $this->objectId
        ], false));

        $form->method('PUT');

        if ($this->mode == 'simple')
            return view('cms::simple.module')
                ->with('form', $form->view('card'))
                ->with('title', $this->getTitle('Редактирование'))
                ->with('description', $this->getDescription());
        else
            return view('cms::module')
                ->with('form', $form->view('card'))
                ->with('title', $this->getTitle('Редактирование'))
                ->with('description', $this->getDescription());
    }

    protected function getFieldByKey($key)
    {
        $fields = $this->formFields();
        foreach ($fields as $field) {
            if (trim($field->get('name'), '[]') == $key)
                return $field;
        }

        return false;
    }

    public function view($objectId)
    {
        $this->objectId = $objectId;
        $this->readonly = true;

        $form = $this->getForm();

        if ($this->mode == 'simple')
            return view('cms::simple.module')
                ->with('form', $form->view('card'))
                ->with('title', $this->getTitle('Просмотр'))
                ->with('description', $this->getDescription());
        else
            return view('cms::module')
                ->with('form', $form->view('card'))
                ->with('title', $this->getTitle('Просмотр'))
                ->with('description', $this->getDescription());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function update($objectId = null)
    {
        $this->objectId = $objectId;

        if (method_exists($this, 'beforeUpdate'))
            $this->beforeUpdate();

        $validated = $this->validate(
            request(),
            $this->rules()
        );

        if (array_key_exists('password', $validated) && is_null($validated['password'])) {
            unset($validated['password']);
        } elseif (array_key_exists('password', $validated))
            $validated['password'] = bcrypt($validated['password']);

        // default value for boolean fields
        foreach ($this->rules() as $key => $validators) {
            if (
                (is_string($validators) && strpos($validators, 'boolean') !== false) ||
                (is_array($validators) && in_array('boolean', $validators))
            ) {
                if (!isset($validated[$key])) {
                    $validated[$key] = false;
                }
            }
        }

        /**
         * @var Model $this- >model
         */
        if ($this->objectId) {
            // update
            $this->model = $this->className::findOrFail($this->objectId);
            $this->model->fill($validated);
            $this->model->save();
        } else {
            // create
            $this->model = $this->className::create($validated);
        }

        // Relationships
        foreach ($this->relations as $key) {
            $relationValue = isset($validated[$key]) ? $validated[$key] : null;

            if ($this->objectId) {
                if ($relationValue)
                    $this->model->$key()->sync($relationValue);
                else
                    $this->model->$key()->detach();
            } else if ($relationValue) {
                $this->model->$key()->attach($relationValue);
            }
        }

        if ($this->difficult) {
            foreach ($this->difficult as $key) {
                $method = 'set' . Str::studly($key) . 'Attribute';

                if (method_exists($this->model, $method)) {
                    $difficultValue = isset($validated[$key]) ? $validated[$key] : null;
                    $values[$key] = $this->model->$method($difficultValue);
                }
            }
        }

        // Files
        if (request()->allFiles()) {
            $attachments = [];
            foreach (request()->allFiles() as $key => $files) {
                $field = $this->getFieldByKey($key);
                if (!$field)
                    continue;

                $settings = $field->getSettings();
                $multiple = $field->get('multiple');

                if (!$multiple) {
                    $exitsFiles = $this->model->attachment($key)->get();
                    if ($exitsFiles) {
                        foreach ($exitsFiles as $exitsFile) {
                            $exitsFile->delete();
                        }
                    }
                }

                if (!is_array($files))
                    $files = [$files];

                if (is_array($files) && $files) {
                    foreach ($files as $file) {
                        $f = new File(
                            $file,
                            group: $key,
                            rename: $field->get('rename'),
                            thumbnails: isset($settings['thumbnails']) ? $settings['thumbnails'] : [],
                            trim: isset($settings['trim']) ? $settings['trim'] : false
                        );
                        $attachments[] = $f->path($this->model->getUploadPath())->allowDuplicates()->load();
                    }
                }

                $attachmentIds = array_map(function ($item) {
                    return $item->id;
                }, $attachments);

                $this->model->attachment()->syncWithoutDetaching(
                    $attachmentIds
                );
            }
        }

        Toast::success('Данные успешно сохранены');
        if ($this->mode == 'simple')
            Alert::success('Данные успешно сохранены', 'Данные успешно сохранены');

        if (method_exists($this, 'afterUpdate'))
            $this->afterUpdate();

        return redirect(
            route(
                'cms.module.edit',
                ['controller' => $this->getSectionController(), 'objectId' => $this->model->getKey()],
                false
            ) . ($this->mode == 'simple' ? '?modal_parent_id=' . request()->get('modal_parent_id') : '')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function destroy($objectId)
    {
        $model = $this->className::findOrFail($objectId);
        $model->delete();

        Toast::success('Запись удалена');

        return redirect(
            route('cms.module.index', ['controller' => $this->getSectionController()], false)
        );
    }

    public function images($objectId)
    {
        $result = [];

        $model = $this->className::findOrFail($objectId);
        if ($model && ($images = $model->images)) {
            foreach ($images as $image) {
                $result[] = [
                    'title' => $image->name . '.' . $image->extension,
                    'value' => $image->url()
                ];
            }
        }

        return response()->json($result);
    }
}
