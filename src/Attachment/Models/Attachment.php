<?php

namespace LaravelCms\Attachment\Models;

use Exception;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LaravelCms\Attachment\MimeTypes;
use LaravelCms\Filters\Filterable;
use LaravelCms\Models\CMs\User;
use Illuminate\Support\Str;

/**
 * Class Attachment.
 */
class Attachment extends Model
{
    use Filterable, HasFactory;

    protected $table = 'attachments';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'original_name',
        'mime',
        'extension',
        'size',
        'path',
        'user_id',
        'user_type',
        'description',
        'alt',
        'sort',
        'hash',
        'disk',
        'group',
        'additional'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'url',
        'relativeUrl',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'sort' => 'integer',
        'additional' => 'array'
    ];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'name',
        'original_name',
        'mime',
        'extension',
        'disk',
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'name',
        'original_name',
        'mime',
        'extension',
        'disk',
    ];

    /**
     * Get the parent user model.
     * @return MorphTo
     */
    public function user()
    {
        return $this->morphTo();
    }

    public function attachmentable()
    {
        return $this->hasOne(Attachmentable::class);
    }

    public function getParentModel(): Model|null
    {
        $attachmentable = $this->attachmentable;
        if ($attachmentable) {
            return $attachmentable->model;
        }

        return null;
    }

    /**
     * Return the address by which you can access the file.
     *
     * @param string|null $default
     *
     * @return string|null
     */
    public function url(string $default = null): ?string
    {
        /** @var Filesystem|Cloud $disk */
        $disk = Storage::disk($this->getAttribute('disk'));
        $path = $this->physicalPath();

        return $path !== null && $disk->exists($path)
            ? $disk->url($path)
            : $default;
    }

    /**
     * Return the address by which you can access the thumbnail file.
     *
     * @param string $dimension
     * @param string|null $default
     *
     * @return string|null
     */
    public function thumbnailUrl(string $dimension, string $default = null): ?string
    {
        /** @var Filesystem|Cloud $disk */
        $disk = Storage::disk($this->getAttribute('disk'));
        $path = $this->physicalThumbnailPath($dimension);

        return $path !== null && $disk->exists($path)
            ? $disk->url($path)
            : $default;
    }

    /**
     * @return string|null
     */
    public function getUrlAttribute(): ?string
    {
        return $this->url();
    }

    /**
     * @return string|null
     */
    public function getRelativeUrlAttribute(): ?string
    {
        $url = $this->url();

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->name . '.' . $this->extension;
    }

    public function hasThumbnail($dimension): bool
    {
        return $this->additional && isset($this->additional['thumbnails'][$dimension]);
    }

    public function getAdditionalByKey(string $key): string|array|null
    {
        return isset($this->additional[$key])
            ? $this->additional[$key]
            : null;
    }

    /**
     * @return string|null
     */
    public function getThumbnailFilename($dimension): string
    {
        if (!$this->additional || !isset($this->additional['thumbnails'][$dimension])) {
            $values = $this->additional;

            if (!array_key_exists('thumbnails', $values)) {
                $values['thumbnails'] = [];
            }

            $values['thumbnails'][$dimension] = $dimension . "_" . $this->getFilename();
            $this->additional = $values;
        }

        return $this->additional['thumbnails'][$dimension];
    }

    /**
     * @return string|null
     */
    public function getTitleAttribute(): ?string
    {
        if ($this->original_name !== 'blob') {
            return $this->original_name;
        }

        return $this->name . '.' . $this->extension;
    }

    /**
     * @return string|null
     */
    public function physicalPath(): ?string
    {
        if ($this->path === null || $this->name === null) {
            return null;
        }

        return $this->path . $this->name . '.' . $this->extension;
    }

    public function physicalThumbnailPath($dimension): ?string
    {
        if ($this->path === null || !isset($this->additional['thumbnails'][$dimension])) {
            return null;
        }

        return $this->path . $this->additional['thumbnails'][$dimension];
    }

    /**
     * @throws Exception
     *
     * @return bool|null
     */
    public function delete()
    {
        if ($this->exists) {
            if (Storage::disk($this->disk)->exists($this->physicalPath())) {
                // Physical removal of all copies of a file.
                if ($this->additional && isset($this->additional['thumbnails'])) {
                    foreach ($this->additional['thumbnails'] as $dimension => $filename) {
                        Storage::disk($this->disk)->delete(
                            $this->physicalThumbnailPath($dimension)
                        );
                    }
                }

                Storage::disk($this->disk)->delete($this->physicalPath());

                $this->relationships()->delete();
            }
        }

        return parent::delete();
    }

    /**
     * @return HasMany
     */
    public function relationships()
    {
        return $this->hasMany(Attachmentable::class, 'attachment_id');
    }

    /**
     * Get MIME type for file.
     *
     * @return string
     */
    public function getMimeType(): string
    {
        $mimes = new MimeTypes();

        $type = $mimes->getMimeType($this->getAttribute('extension'));

        return $type ?? 'unknown';
    }

    public function isImage(): bool
    {
        return Str::startsWith($this->getMimeType(), 'image');
    }

    public static function boot()
    {
        parent::boot();

        self::created(function(Model $model) {
            if (in_array('sort', $model->fillable)) {
                $model->sort = ((int) DB::table($model->table)->max('sort') + 1);
                $model->save();
            }
        });
    }

    public function data(): array
    {
        $data = [
            'url' => $this->url(),
            'name' => $this->getFilename(),
            'original_name' => $this->original_name,
            'mime' => $this->mime,
            'sort' => $this->sort,
            'group' => $this->group
        ];

        if ($this->getAdditionalByKey('title'))
            $data['title'] = $this->getAdditionalByKey('title');

        if ($this->getAdditionalByKey('description'))
            $data['description'] = $this->getAdditionalByKey('description');

        if ($this->getAdditionalByKey('thumbnails')) {
            $data['thumbnails'] = [];

            $thumbnails = $this->getAdditionalByKey('thumbnails');
            ksort($thumbnails, SORT_NUMERIC);
            foreach ($thumbnails as $size => $filename) {
                $data['thumbnails'][] = [
                    'url' => $this->thumbnailUrl($size),
                    'size' => $size
                ];
            }
        }

        return $data;
    }
}
