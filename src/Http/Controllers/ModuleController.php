<?php

namespace LaravelCms\Http\Controllers;

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

    protected $grid = [];

    public function before()
    {
        if (parent::before()) {
            if (
                $this->getSection()->is_published != true ||
                !$this->getSection()->users()->where('id', Auth::id())->count()
            ) {
                return false;
            }

            return true;
        }
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

                        foreach ($list AS $item) {
                            $currentSort[] = $item->sort_order;
                        }

                        foreach ($list AS $item) {
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

        $grid = new Grid($this->grid, $this->getSection());

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

    /**
     * Return form Builder object
     * @param boolean $create
     * @param int|null $objectId
     * @return Builder
     */
    protected function getForm($create = false, $objectId = null): Builder
    {
        $repository = null;

        $formFields = $this->formFields();
        if ($objectId) {
            $model = $this->className::findOrFail($objectId);
            $values = $model->getAttributes();

            if ($this->relations) {
                foreach ($this->relations as $relation) {
                    $values[$relation] = $model->$relation()->allRelatedIds();
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
                        $fieldFiles = $files->filter(function($item) use ($group) {
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
        $form->groups($this->formGroups());

        if ($create) {
            $form->push(
                Button::make('create')
                    ->label('Добавить')
                    ->value('true')
                    ->class('btn btn-primary')
            );
            $form->setAction(route('cms.module.store', ['controller' => $this->getSectionController()]));

        } else {
            $form->push(
                Button::make('update')
                    ->label('Сохранить')
                    ->value('true')
                    ->class('btn btn-primary')
            );
            $form->push(
                Link::make('Удалить')
                    ->href(route('cms.module.destroy', ['controller' => $this->getSectionController(), 'objectId' => $objectId], false))
                    ->class('btn btn-danger float-right')
                    ->confirm('true')
                    ->withoutFormType()
            );

            $form->images(route('cms.module.images', ['controller' => $this->getSectionController(), 'objectId' => $objectId], false));

            $form->setAction(route('cms.module.update', [
                'controller' => $this->getSectionController(),
                'objectId' => $objectId
            ]));

            $form->method('PUT');
        }

        return $form->view('card');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $form = $this->getForm(true);

        return view('cms::module')
            ->with('form', $form)
            ->with('title', 'Добавление');
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
     * @param  int $id
     */
    public function edit($objectId)
    {
        $form = $this->getForm(false, $objectId);

        return view('cms::module')
            ->with('form', $form)
            ->with('title', 'Редактирование');
    }

    protected function getFieldByKey($key)
    {
        $fields = $this->formFields();
        foreach ($fields as $field) {
            if ($field->get('name') == $key)
                return $field;
        }

        return false;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($objectId = null)
    {
        $formFields = $this->formFields();
        $validated = $this->validate(
            request(),
            $this->rules($objectId)
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
         * @var Model $model
         */
        if ($objectId) {
            // update
            $model = $this->className::findOrFail($objectId);
            $model->fill($validated);
            $model->save();
        } else {
            // create
            $model = $this->className::create($validated);
        }

        // Relationships
        foreach ($validated as $key => $value) {
            if (in_array($key, $this->relations)) {
                if ($objectId) {
                    if ($value)
                        $model->$key()->sync($value);
                    else
                        $model->$key()->detach();
                } else if ($value) {
                    $model->$key()->attach($value);
                }
            }
        }

        // Files
        if(request()->allFiles()) {
            $attachments = [];
            foreach (request()->allFiles() as $key => $files) {
                $field = $this->getFieldByKey($key);
                if (!$field)
                    continue;

                if (!is_array($files))
                    $files = [$files];

                if (is_array($files) && $files) {
                    foreach ($files as $file) {
                        $f = new File($file, group: $key, rename: $field->get('rename'));
                        $attachments[] = $f->path($model->getUploadPath())->allowDuplicates()->load();
                    }
                }

                $attachmentIds = array_map(function($item) {
                    return $item->id;
                }, $attachments);

                $model->attachment()->syncWithoutDetaching(
                    $attachmentIds
                );
            }
        }

        Toast::success('Данные успешно сохранены');

        redirect()->to(route(
            'cms.module.edit',
            ['controller' => $this->getSectionController(), 'objectId' => $model->id],
            false
        ))->send();
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
        )->send();
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
