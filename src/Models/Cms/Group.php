<?php

namespace LaravelCms\Models\Cms;

use Junges\ACL\Models\Group as BaseGroup;
use LaravelCms\Filters\Filterable;

class Group extends BaseGroup
{
    use Filterable;

    public $defaultSortField = 'name';
    public $defaultSortOrder = 'asc';
}
