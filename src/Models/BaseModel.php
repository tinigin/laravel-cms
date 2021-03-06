<?php

namespace LaravelCms\Models;

use LaravelCms\Attachment\Attachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelCms\Attachment\Models\Attachment;
use LaravelCms\Filters\Filterable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    use HasFactory, Filterable, Attachable;

    protected $directoryLevel = 4;

    protected $allowedFilters = [];

    protected $allowedSorts = [];

    protected $allowedRelationsFilters = [];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    public $defaultSortField = 'id';

    public $defaultSortOrder = 'asc';

    public static function boot()
    {
        parent::boot();

        self::created(function(Model $model) {
            if (in_array('sort_order', $model->fillable)) {
                $model->sort_order = (DB::table($model->table)->max('sort_order') + 1);
                $model->save();
            }
        });

        self::deleting(function($model) {
            if (!method_exists($model, 'runSoftDelete')) {
                $model->attachment->each->delete();
            }
        });
    }

    public function getUploadPath()
    {
        $path = Str::lower((new \ReflectionClass($this))->getShortName());

        if ($this->directoryLevel > 0) {
            $key = md5($this->getKey());

            for($i = 0; $i < $this->directoryLevel; ++$i) {
                if(($prefix = substr($key, $i + $i, 2)) !== false) {
                    $path = Str::finish($path, '/') . $prefix;
                }
            }

            $path = Str::finish($path, '/') . $key;
        } else {
            $path = Str::finish($path, '/') . $this->getKey();
        }

        return $path;
    }

    public function data(
        array $attributes = [],
        array $additionalData = [],
        bool|string $files = false,
        bool|string $images = false
    ): array {
        $data = [
            'id' => $this->getKey()
        ];

        foreach ($this->getAttributes() as $key => $value) {
            if ($attributes && !in_array($key, $attributes))
                continue;

            $data[$key] = $value;
        }

        if ($additionalData)
            foreach ($additionalData as $key => $value)
                $data[$key] = $value;

        if ($files) {
            if (is_bool($files))
                $availableFiles = $this->attachment;
            else
                $availableFiles = $this->attachment($files)->get();

            if ($availableFiles) {
                $data['files'] = [];

                /**
                 * @var Attachment $file
                 */
                foreach ($availableFiles as $file) {
                    $data['files'][] = $file->data();
                }
            }
        }

        if ($images) {
            if (is_bool($images))
                $availableImages = $this->images;
            else
                $availableImages = $this->images($images)->get();

            if ($availableImages) {
                $data['images'] = [];

                /**
                 * @var Attachment $file
                 */
                foreach ($availableImages as $file) {
                    $data['images'][] = $file->data();
                }
            }
        }

        return $data;
    }
}
