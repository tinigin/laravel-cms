<?php

namespace LaravelCms\Attachment;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use LaravelCms\Attachment\Contracts\Engine;
use LaravelCms\Attachment\Engines\Generator;
use LaravelCms\Attachment\Models\Attachment;
use LaravelCms\Events\ReplicateFileEvent;
use LaravelCms\Events\UploadFileEvent;
use LaravelCms\Support\ImageHelper;
use LaravelCms\Support\Imagemagick;
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

    protected $additional = [];

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
        bool|int $trim = false,
        array $additional = []
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
        $this->additional = $additional;
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
        $isVideo = $this->engine->isVideo();
        $isImage = $this->engine->isImage();

        if ($isImage) {
            if ($this->trim) {
                $trimer = new ImageHelper(is_string($this->file) ? $this->file : $this->file->getRealPath());

                if (!is_bool($this->trim)) {
                    $trimer->trimWithBorder(
                        border: $this->trim
                    );
                } else if ($this->trim) {
                    $trimer->trim();
                }

                $trimer->save(
                    is_string($this->file) ? $this->file : $this->file->getRealPath(),
                    quality: 100
                );
            }

            if (
                config('cms.attachment.max.width', null) ||
                config('cms.attachment.max.height', null)
            ) {
                $filepath = is_string($this->file) ? $this->file : $this->file->getRealPath();
                $maxWidth = config('cms.attachment.max.width');
                $maxHeight = config('cms.attachment.max.height');

                if (config('cms.attachment.max.driver') == 'imagick') {
                    $imagick = new Imagemagick($filepath);
                    list($width, $height) = $imagick->getimagesize();

                    if (
                        ($maxWidth && $width > $maxWidth) ||
                        ($maxHeight && $height > $maxHeight)
                    ) {
                        $imagick->scale(
                            config('cms.attachment.max.width'),
                            $maxHeight
                        );
                    }

                } else {
                    $helper = new ImageHelper($filepath);

                    if (
                        ($maxWidth && $helper->image()->width() > $maxWidth) ||
                        ($maxHeight && $helper->image()->height() > $maxHeight)
                    ) {
                        $helper->scaleDown(
                            config('cms.attachment.max.width', null),
                            config('cms.attachment.max.height', null)
                        );
                        $helper->save(
                            is_string($this->file) ? $this->file : $this->file->getRealPath(),
                            quality: 100
                        );
                    }
                }
            }
        }

        $this->storage->putFileAs($this->engine->path(), $this->file, $this->engine->fullName(), [
            'mime_type' => $this->engine->mime(),
        ]);

        $additional = $this->additional;

        if ($this->thumbnails && !$isVideo) {
            $additional['thumbnails'] = [];

            foreach ($this->thumbnails as $thumbnailData) {
                $filename = $thumbnailData['w'] . 'x' . $thumbnailData['h'] . '_' . $this->engine->fullName();
                $tmpFile = tempnam(sys_get_temp_dir(), $thumbnailData['w'] . 'x' . $thumbnailData['h'] . '_' . $this->engine->name()) . ".{$this->engine->extension()}";

                $imageHelper = new ImageHelper(is_string($this->file) ? $this->file : $this->file->getRealPath());
                switch ($thumbnailData['mode']) {
                    case 'width':
                        $imageHelper->resizeToWidth($thumbnailData['w']);
                        break;

                    case 'fit':
                    case 'free':
                        $imageHelper->resizeToBestFit($thumbnailData['w'], $thumbnailData['h']);
                        break;

                    case 'height':
                        $imageHelper->resizeToHeight($thumbnailData['h']);
                        break;

                    case 'force':
                        $imageHelper->resizeToBestFit($thumbnailData['w'], $thumbnailData['h']);
                        break;

                    case 'contain':
                        $imageHelper->contain($thumbnailData['w'], $thumbnailData['h'], position: 'center');
                        break;

                    case 'scale':
                        $imageHelper->scale($thumbnailData['w'], $thumbnailData['h']);
                        break;

                    case 'scaleDown':
                        $imageHelper->scaleDown($thumbnailData['w'], $thumbnailData['h']);
                        break;

                    case 'cover':
                        $imageHelper->cover($thumbnailData['w'], $thumbnailData['h']);
                        break;

                    case 'smart':
                        $imageHelper->smart(
                            $thumbnailData['w'],
                            $thumbnailData['h'],
                            increase: isset($thumbnailData['increase']) && $thumbnailData['increase'] === false ? false : true,
                            exact: isset($thumbnailData['exact']) && $thumbnailData['exact'] === false ? false : true
                        );
                        break;

                    case 'crop':
                        $imageHelper->crop(
                            $thumbnailData['w'],
                            $thumbnailData['h'],
                            position: 'center'
                        );
                        break;
                }

                if (isset($thumbnailData['watermark'])) {
                    $imageHelper->watermark($thumbnailData['watermark']);
                }

                if (isset($thumbnailData['format']) && in_array($thumbnailData['format'], ['jpg', 'png', 'webp'])) {
                    $filename = str_replace(".{$this->engine->extension()}", ".{$thumbnailData['format']}", $filename);
                    $tmpFile = str_replace(".{$this->engine->extension()}", ".{$thumbnailData['format']}", $tmpFile);
                }

                $imageHelper->save($tmpFile);

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
            'additional'    => $additional,
            'alt'           => isset($additional['alt']) ? $additional['alt'] : (isset($additional['title']) ? $additional['title'] : null),
            'description'   => isset($additional['description']) ? $additional['description'] : null,
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
