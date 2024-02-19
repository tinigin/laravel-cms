<?php

namespace LaravelCms\Attachment;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelCms\Attachment\Contracts\Engine;
use LaravelCms\Attachment\Engines\Generator;
use LaravelCms\Attachment\Models\Attachment;
use LaravelCms\Events\ReplicateFileEvent;
use LaravelCms\Events\UploadFileEvent;
use LaravelCms\Support\ImageResize;

/**
 * Class File.
 */
class File
{
    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var Filesystem
     */
    protected $storage;

    /**
     * @var string
     */
    protected $disk;

    /**
     * @var string|null
     */
    protected $group;

    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var bool
     */
    protected $duplicate = false;

    /**
     * orig, hash, value
     * @var string|null
     */
    protected $rename = 'orig';

    protected $thumbnails = [];

    protected $trim = false;

    /**
     * File constructor.
     *
     * @param UploadedFile $file
     * @param string|null  $disk
     * @param string|null  $group
     */
    public function __construct(
        UploadedFile $file,
        string $disk = null,
        string $group = null,
        string $rename = 'orig',
        array $thumbnails = [],
        bool|int $trim = false
    ) {
        abort_if($file->getSize() === false, 415, 'File failed to load.');

        $this->file = $file;
        $this->disk = $disk ?? config('cms.attachment.disk', 'public');
        $this->storage = Storage::disk($this->disk);

        /** @var string $generator */
        $generator = config('cms.attachment.generator', Generator::class);

        $this->engine = new $generator($file, $rename);

        $this->group = $group;
        $this->rename = $rename;
        $this->thumbnails = $thumbnails;
        $this->trim = $trim;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     *
     * @return Model|Attachment
     */
    public function load(): Model
    {
        $attachment = $this->getMatchesHash();

        if ($attachment === null) {
            return $this->save();
        }

        $attachment = $attachment->replicate()->fill([
            'original_name' => $this->file->getClientOriginalName(),
            'sort'          => 0,
            'user_id'       => Auth::id(),
            'user_type'     => Auth::user() ? get_class(Auth::user()) : '',
            'group'         => $this->group,
        ]);

        $attachment->save();

        event(new ReplicateFileEvent($attachment, $this->engine->time()));

        return $attachment;
    }

    /**
     * @param bool $status
     *
     * @return File
     */
    public function allowDuplicates(bool $status = true): self
    {
        $this->duplicate = $status;

        return $this;
    }

    /**
     * @return Attachment|null
     */
    private function getMatchesHash()
    {
        if ($this->duplicate) {
            return null;
        }

        return Attachment::where('hash', $this->engine->hash())
            ->where('disk', $this->disk)
            ->first();
    }

    /**
     * @return Model|Attachment
     */
    private function save(): Model
    {
        if ($this->trim) {
            $trimer = new ImageResize(is_string($this->file) ? $this->file : $this->file->getRealPath());

            if (!is_bool($this->trim)) {
                $trimer->trimWithBorder(
                    border: $this->trim
                );
            } else {
                $trimer->trim();
            }

            $trimer->save(
                is_string($this->file) ? $this->file : $this->file->getRealPath(),
                quality: 100
            );
        }

        $this->storage->putFileAs($this->engine->path(), $this->file, $this->engine->fullName(), [
            'mime_type' => $this->engine->mime(),
        ]);

        $additional = [];

        if ($this->thumbnails) {
            $additional['thumbnails'] = [];

            foreach ($this->thumbnails as $thumbnailData) {
                $filename = $thumbnailData['w'] . 'x' . $thumbnailData['h'] . '_' . $this->engine->fullName();
                $tmpFile = tempnam(sys_get_temp_dir(), $filename);

                $resizer = new ImageResize(is_string($this->file) ? $this->file : $this->file->getRealPath());
                switch ($thumbnailData['mode']) {
                    case 'width':
                        $resizer->resizeToWidth($thumbnailData['w']);
                        break;

                    case 'fit':
                    case 'free':
                        $resizer->resizeToBestFit($thumbnailData['w'], $thumbnailData['h'], true);
                        break;

                    case 'height':
                        $resizer->resizeToHeight($thumbnailData['h']);
                        break;

                    case 'force':
                        $resizer->resize($thumbnailData['w'], $thumbnailData['h'], true);
                        break;

                    case 'crop':
                        $resizer->crop(
                            $thumbnailData['w'],
                            $thumbnailData['h'],
                            true,
                            ImageResize::CROPCENTER
                        );
                        break;
                }
                if (in_array($thumbnailData['mode'], ['free']))
                    $resizer->save($tmpFile, exact_size: [$thumbnailData['w'], $thumbnailData['h']]);
                else
                    $resizer->save($tmpFile);

                $additional['thumbnails'][$thumbnailData['w'] . 'x' . $thumbnailData['h']] = $filename;

                $this->storage->putFileAs($this->engine->path(), $tmpFile, $filename, [
                    'mime_type' => $this->engine->mime(),
                ]);
            }
        }

        $attachment = Attachment::create([
            'name'          => $this->engine->name(),
            'mime'          => $this->engine->mime(),
            'hash'          => $this->engine->hash(),
            'extension'     => $this->engine->extension(),
            'original_name' => $this->file->getClientOriginalName(),
            'size'          => $this->file->getSize(),
            'path'          => Str::finish($this->engine->path(), '/'),
            'disk'          => $this->disk,
            'group'         => $this->group,
            'user_id'       => Auth::id(),
            'user_type'     => Auth::user() ? get_class(Auth::user()) : '',
            'additional'    => $additional
        ]);

        event(new UploadFileEvent($attachment, $this->engine->time()));

        return $attachment;
    }

    /**
     * set a custom Path
     *
     * @return File
     */
    public function path(?string $path = null)
    {
        $this->engine->setPath($path);

        return $this;
    }
}
