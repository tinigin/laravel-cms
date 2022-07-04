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

    /**
     * @param string|null $group
     *
     * @return MorphToMany
     */
    public function images(string $group = null): MorphToMany
    {
        $query = $this->attachment($group);

        $query->whereIn('mime', [
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/pjpeg',
            'image/svg+xml',
            'image/tiff',
            'image/vnd.microsoft.icon',
            'image/vnd.wap.wbmp',
            'image/webp'
        ]);

        return $query;
    }
}
