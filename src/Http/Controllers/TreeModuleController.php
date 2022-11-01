<?php

namespace LaravelCms\Http\Controllers;

use LaravelCms\Http\Controllers\ModuleController;

class TreeModuleController extends ModuleController
{
    public function index()
    {
        $class = new ($this->className);
        $tree = $class->toTree();

        return view('cms::module')
            ->with('tree', $tree)
            ->with('type', 'sortable')
            ->with('url', route('cms.module.sort', ['controller' => $this->getSection()->folder], false))
            ->with('name', 'none')
            ->with('value', [])
            ->with('controller', $this->getSection()->folder)
            ->with('title', $this->getSection()->name);
    }

    public function sort()
    {
        if (request()->has('item')) {
            $items = request()->get('item');
            if ($items) {
                $class = $this->className;
                $classInstance = new ($class);

                $list = $this->className::whereIn('id', $items)
                    ->orderBy($classInstance->defaultSortField, $classInstance->defaultSortOrder)
                    ->get();
                if ($list->count()) {
                    $currentSort = [];
                    $data = array_flip($items);

                    foreach ($list AS $item) {
                        $currentSort[] = $item->sort_order;
                    }

                    foreach ($list AS $item) {
                        $newSortIndex = $currentSort[$data[$item->getKey()]];
                        if ($newSortIndex) {
                            $this->className::where('id', $item->getKey())
                                ->update(['sort_order' => $newSortIndex]);
                        }
                    }
                }
            }
        }

        return response('true', 200)->header('Content-Type', 'text/plain');
    }

}
