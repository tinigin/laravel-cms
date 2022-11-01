<?php

namespace LaravelCms\Models;

use LaravelCms\Models\BaseModel;

class TreeModel extends BaseModel
{
    /**
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * Get children of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, $this->parentColumn);
    }

    /**
     * Get parent of current node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, $this->parentColumn);
    }

    /**
     * Format data to tree like array.
     *
     * @return array
     */
    public function toTree(array $exclude = [])
    {
        return $this->buildNestedArray(exclude: $exclude);
    }

    /**
     * Build Nested array.
     *
     * @param array $nodes
     * @param int   $parentId
     *
     * @return array
     */
    protected function buildNestedArray(array $nodes = [], $parentId = 0, array $exclude = [])
    {
        $branch = [];

        if (empty($nodes)) {
            $nodes = $this->allNodes();
        }

        foreach ($nodes as $node) {
            if (in_array($node['id'], $exclude))
                continue;

            if ($node[$this->parentColumn] == $parentId) {
                $children = $this->buildNestedArray($nodes, $node[$this->getKeyName()], $exclude);

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Get all elements.
     *
     * @return mixed
     */
    public function allNodes()
    {
        $self = new static();

        return $self->orderBy($self->defaultSortField, $this->defaultSortOrder)->get()->toArray();
    }
}
