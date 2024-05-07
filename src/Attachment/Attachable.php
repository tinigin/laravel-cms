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
    public function attachment(string|array $group = null): MorphToMany
    {
        $query = $this->morphToMany(
            Attachment::class,
            'attachmentable',
            'attachmentable',
            'attachmentable_id',
            'attachment_id'
        );

        if ($group !== null) {
            if (!is_array($group))
                $group = [$group];

            $query->whereIn('group', $group);
        }

        return $query
            ->orderBy('sort');
    }

    /**
     * @param string|null $group
     *
     * @return MorphToMany
     */
    public function images(string|array $group = null): MorphToMany
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

    /**
     * @param string|null $group
     *
     * @return MorphToMany
     */
    public function files(string|array $group = null): MorphToMany
    {
        $query = $this->attachment($group);

        $query->whereNotIn('mime', [
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
