<?php

namespace LaravelCms\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use LaravelCms\Models\Cms\Section;
use LaravelCms\Models\Cms\SectionGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;

use LaravelCms\Contracts\BeforeAndAfter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends LaravelController implements BeforeAndAfter
{
    use ValidatesRequests;

    /**
     * @var \LaravelCms\Models\Cms\Section
     */
    protected $section;

    protected $guard = 'cms';

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $before = $this->before();

        $result = null;

        if ($before) {
            $result = $this->{$method}(...array_values($parameters));

            $this->after();
        }

        return $result ?: redirect(route('cms.dashboard'));
    }

    public function before()
    {
        return true;
    }

    public function after()
    {
        if (Auth::check($this->guard)) {
            View::share('user', auth()->user());
        } else {
//            throw new AuthenticationException;
        }

        View::share('navigation', $this->navigation());
    }

    protected function navigation(): array
    {
        $result = [];
        $groups = SectionGroup::query()->orderBy('sort_order', 'asc')->get();

        foreach ($groups as $group) {
            $sections = $group->sections()->where('is_published', true)->whereHas('users', function ($query) {
                $query->where('id', Auth::id());
            })->orderBy('sort_order', 'asc')->get();

            $isActive = false;

            if ($sections) {
                $sectionsData = [];

                foreach ($sections as $section) {
                    $url = route('cms.module.index', ['controller' => $section->folder], false);
                    $currentUrl = '/' . ltrim(request()->path(), '/');

                    try {
                        $isSectionActive = explode('/', $currentUrl)[2] == explode('/', $url)[2];
                    } catch (Exception as $e) {
                        $isSectionActive = false;
                    }

                    $sectionsData[] = [
                        'name' => $section->name,
                        'folder' => $section->folder,
                        'url' => $url,
                        'active' => $isSectionActive,
                        'current' => ($currentUrl == $url)
                    ];

                    if ($isSectionActive)
                        $isActive = true;
                }

                if ($sectionsData) {
                    $result[] = [
                        'name' => $group->name,
                        'sections' => $sectionsData,
                        'active' => $isActive
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @return \LaravelCms\Models\Cms\Section|null
     */
    protected function getSection(): Section|null
    {
        if (is_null($this->section)) {
            $this->section = Section::firstWhere(
                'folder',
                Str::before(
                    Str::after(
                        request()->path(),
                        config('cms.url_prefix') . '/'
                    ),
                    '/'
                )
            );

            if (!$this->section) {
                throw new NotFoundHttpException;
            }
        }

        return $this->section;
    }

    /**
     * Return module folder
     * @return string
     */
    protected function getSectionController(): string
    {
        return $this->getSection() ? $this->getSection()->folder : '';
    }
}
