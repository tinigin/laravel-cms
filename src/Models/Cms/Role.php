<?php

namespace LaravelCms\Models\Cms;

use Spatie\Permission\Models\Role as BaseRole;
use LaravelCms\Filters\Filterable;

class Role extends BaseRole
{
    use Filterable;

    public $defaultSortField = 'name';
    public $defaultSortOrder = 'asc';
}
