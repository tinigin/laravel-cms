<?php

namespace LaravelCms\Models\Cms;

use LaravelCms\Models\BaseModel;
use LaravelCms\Models\Cms\SectionGroup;
use LaravelCms\Models\Cms\User;

class Setting extends BaseModel
{
    protected $table = 'settings';

    protected $fillable = [
        'title',
        'key',
        'value',
    ];

    protected $allowedFilters = [
        'title',
        'key',
        'value',
    ];

    protected $allowedSorts = [
        'title',
        'key',
        'value',
    ];

    public $defaultSortField = 'key';
    public $defaultSortOrder = 'asc';

    public $timestamps = false;
}
