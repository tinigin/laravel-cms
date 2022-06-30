<?php

namespace LaravelCms\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use LaravelCms\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;

class DashboardController extends BaseController
{
    use AuthorizesRequests;

    protected $limit = 10;

    protected $tiles = [
        'users' => [\LaravelCms\Models\Cms\User::class, 'Пользователи'],
        'sections' => [\LaravelCms\Models\Cms\Section::class, 'Модули'],
        'section-groups' => [\LaravelCms\Models\Cms\SectionGroup::class, 'Группы'],
    ];

    public function index()
    {
        $data = [];

        /**
         * @var Model $class
         */
        foreach ($this->tiles as $controller => $sectionData) {
            list($class, $title) = $sectionData;

            $data[$controller] = [
                'list' => $class::latest()
                    ->take($this->limit)
                    ->get(),
                'title' => $title,
            ];
        }

        return view('cms::index', ['data' => $data]);
    }
}
