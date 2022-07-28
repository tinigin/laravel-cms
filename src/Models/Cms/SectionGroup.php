<?php

namespace LaravelCms\Models\Cms;

use LaravelCms\Models\BaseModel;
use LaravelCms\Models\Cms\Section;

class SectionGroup extends BaseModel
{
    protected $table = 'cms_section_groups';

    protected $fillable = [
        'name',
        'icon',
        'sort_order',
        'is_published'
    ];

    protected $allowedFilters = [
        'name',
        'is_published',
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'is_published'
    ];

    protected $allowedRelationsFilters = [
        'sections',
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    public $defaultSortField = 'sort_order';

    public $defaultSortOrder = 'asc';

    public function sections()
    {
        return $this->hasMany(Section::class, 'cms_section_group_id');
    }
}
