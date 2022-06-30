<?php

namespace LaravelCms\Attachment;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelCms\Attachment\Models\Attachment;

/**
 * Trait Attachable.
 */
trait Attachable
{
    /**
     * @param string|null $group
     *
     * @return MorphToMany
     */
    public function attachment(string $group = null): MorphToMany
    {
        $query = $this->morphToMany(
            Attachment::class,
            'attachmentable',
            'attachmentable',
            'attachmentable_id',
            'attachment_id'
        );

        if ($group !== null) {
            $query->where('group', $group);
        }

        return $query
            ->orderBy('sort');
    }
}
