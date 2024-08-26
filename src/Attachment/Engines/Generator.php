<?php

namespace LaravelCms\Attachment\Engines;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use LaravelCms\Attachment\Contracts\Engine;
use LaravelCms\Attachment\MimeTypes;

class Generator implements Engine
{
    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var int
     */
    protected $time;

    /**
     * @var MimeTypes
     */
    protected $mimes;

    /**
     * @var string
     */
    protected $uniqueId;

    /**
     * @var ?string
     */
    protected $path;

    /**
     * Generator constructor.
     *
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file, $rename = false)
    {
        $this->file = $file;
        $this->path = null;
        $this->time = time();
        $this->mimes = new MimeTypes();
        $this->rename = $rename;
        $this->uniqueId = uniqid('', true);
    }

    /**
     * Returns name to create a real file on disk and write to the database.
     * Specified any string without extension.
     *
     * @return string
     */
    public function name(): string
    {
        switch ($this->rename) {
            case 'hash':
                return Str::limit(sha1($this->uniqueId . $this->file->getClientOriginalName()), 10, '');

            case 'orig':
                return Str::slug(pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME));

            default:
                return $this->rename;
        }
    }

    /**
     * Returns name to create a file with extension.
     *
     * @return string
     */
    public function fullName(): string
    {
        return pathinfo($this->name(), PATHINFO_FILENAME) . '.' . $this->extension();
    }

    /**
     * Returns the relative file path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path ?? date('Y/m/d', $this->time());
    }

    /**
     * Set a custom path
     *
     * @return Generator
     */
    public function setPath(?string $path = null)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Returns file hash string that will indicate
     * that the same file has already been downloaded.
     *
     * @return string
     */
    public function hash(): string
    {
        return sha1_file($this->file->path());
    }

    /**
     * Return a Unix file upload timestamp.
     *
     * @return int
     */
    public function time(): int
    {
        return $this->time;
    }

    /**
     * Returns file extension.
     *
     * @return string
     */
    public function extension(): string
    {
        $extension = $this->file->getClientOriginalExtension();
        if (!$extension) {
            $extension = $this->file->extension();
        }

        return empty($extension)
            ? $this->mimes->getExtension($this->file->getClientMimeType(), 'unknown')
            : $extension;
    }

    /**
     * Returns the file mime type.
     *
     * @return string
     */
    public function mime(): string
    {
        return $this->mimes->getMimeType($this->extension())
            ?? $this->mimes->getMimeType($this->file->getClientMimeType())
            ?? 'unknown';
    }
}
