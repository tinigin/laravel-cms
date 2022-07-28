<?php

namespace LaravelCms\Models\Cms;

use LaravelCms\Models\BaseModel;
use LaravelCms\Models\Cms\SectionGroup;
use LaravelCms\Models\Cms\User;

class Section extends BaseModel
{
    protected $table = 'cms_sections';

    protected $fillable = [
        'cms_section_group_id',
        'name',
        'icon',
        'description',
        'folder',
        'sort_order',
        'is_published'
    ];

    protected $allowedFilters = [
        'name',
        'description',
        'folder',
        'is_published',
        'cms_section_group_id'
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'folder',
        'is_published'
    ];

    protected $allowedRelationsFilters = [
        'users',
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    public $defaultSortField = 'sort_order';

    public $defaultSortOrder = 'asc';

    public function group()
    {
        return $this->belongsTo(SectionGroup::class, 'cms_section_group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'cms_user_to_sections', 'cms_section_id', 'cms_user_id');
    }
}
