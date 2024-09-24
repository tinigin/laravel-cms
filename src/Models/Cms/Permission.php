<?php

namespace LaravelCms\Models\Cms;

use Spatie\Permission\Models\Permission as BasePermission;
use LaravelCms\Filters\Filterable;

class Permission extends BasePermission
{
    use Filterable;

    public $defaultSortField = 'name';
    public $defaultSortOrder = 'asc';
}
