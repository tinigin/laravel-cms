<?php

namespace LaravelCms\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Http;
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

        if (config('cms.attachment.parent_folder'))
            $path = Str::finish(config('cms.attachment.parent_folder'), '/') . $path;

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

    public function uploadFile(string $file, string $group, array $thumbnails = [], string $rename = 'hash', array $additional = [])
    {
        $uploadedFile = null;
        $isDownloaded = false;

        $extension = FileFacade::extension($file);
        $filename = FileFacade::name($file) . '.' . $extension;

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            $isDownloaded = true;
            $tmpPath = storage_path('tmp');
            if (!is_dir($tmpPath))
                mkdir($tmpPath);

            $filepath = "{$tmpPath}/{$filename}";
            $response = Http::withHeaders(['Accept-Encoding' => 'gzip, deflate'])->timeout(30)->get($file);
            if ($response->successful()) {
                $body = $response->body();
                if (file_put_contents($filepath, $body)) {
                    $uploadedFile = new UploadedFile($filepath, $filename);
                }
            }

        } else if (File::exists($file)) {
            $uploadedFile = new UploadedFile($file, $filename);
        }

        if ($uploadedFile) {
            $f = new \LaravelCms\Attachment\File(
                $uploadedFile,
                group: $group,
                rename: $rename,
                thumbnails: $thumbnails ? $thumbnails : [],
                additional: $additional
            );
            $attachment = $f->path($this->getUploadPath())->allowDuplicates()->load();

            $this->attachment()->syncWithoutDetaching(
                $attachment->id
            );

            if ($isDownloaded)
                unlink($uploadedFile->getPathname());

            return $attachment;
        }

        return false;
    }

    public function uploadImageFile(string $group, string $url = '', string $path = '', bool|int $trim = false, array $thumbnails = [], string $rename = 'hash')
    {
        $extension = FileFacade::extension($url);
        $filename = FileFacade::name($url) . '.' . $extension;

        $tmpPath = storage_path('tmp');
        if (!is_dir($tmpPath))
            mkdir($tmpPath);

        $filepath = "{$tmpPath}/{$filename}";
        $response = Http::withHeaders(['Accept-Encoding' => 'gzip, deflate'])->timeout(30)->get($url);
        if ($response->successful()) {
            $body = $response->body();
            if (file_put_contents($filepath, $body)) {
                $attachments = [];
                $uploadedFile = new UploadedFile($filepath, $filename);

                try {
                    $f = new \LaravelCms\Attachment\File(
                        $uploadedFile,
                        group: $group,
                        rename: $rename,
                        thumbnails: $thumbnails[$group],
                        trim: $trim
                    );
                    $attachments[] = $f->path($this->getUploadPath())->allowDuplicates()->load();

                } catch (\Exception $e) {

                }

                $attachmentIds = array_map(function ($item) {
                    return $item->id;
                }, $attachments);

                $this->attachment()->syncWithoutDetaching(
                    $attachmentIds
                );

                unlink($filepath);
            }
        }
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
                $availableFiles = $this->files;
            else
                $availableFiles = $this->files($files)->get();

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

    /**
     * Generate cache key from params
     *
     * @param $base
     * @param null $params
     * @return string
     */
    public static function getCacheKey($base, $params = null): string
    {
        $result = '';

        if ($params && is_array($params)) {
            foreach ($params AS $key => $value) {
                if (!is_numeric($key))
                    $result .= $key . '=' . $value . ';';
                else
                    $result .= $value . ';';
            }

        } else if ($params && is_string($params)) {
            $result .= $params . ';';

        }

        $result = $base . '-' . md5($result);

        return $result;
    }
}
