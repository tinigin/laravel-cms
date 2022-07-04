<?php

namespace LaravelCms\Form\Fields;

use Illuminate\Support\Arr;
use LaravelCms\Attachment\Models\Attachment;
use LaravelCms\Form\Field;
use LaravelCms\Support\Assert;
use LaravelCms\Support\Init;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class File.
 *
 * @method File name(string $value = null)
 * @method File placeholder(string $value = null)
 * @method File value($value = true)
 * @method File help(string $value = null)
 * @method File maxFileSize($value = true)
 * @method File maxFiles($value = true)
 * @method File accept($value = true)
 * @method File resizeQuality($value = true)
 * @method File resizeWidth($value = true)
 * @method File resizeHeight($value = true)
 * @method File title(string $value = null)
 * @method File rename(string $value = null)
 */
class File extends Field
{
    /**
     * @var string
     */
    protected $view = 'cms::form.fields.file';

    /**
     * All attributes that are available to the field.
     *
     * @var array
     */
    protected $attributes = [
        'class'           => 'custom-file-input',
        'type'            => 'file',
        'value'           => null,
        'multiple'        => true,
        'maxFileSize'     => null,
        'maxFiles'        => 9999,
        'acceptedFiles'   => null,
        'resizeQuality'   => 0.8,
        'resizeWidth'     => null,
        'resizeHeight'    => null,
        'accept'          => null
    ];

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'name',
        'type',
        'multiple',
        'placeholder',
        'required',
        'accept'
    ];

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        // Set max file size
        $this->addBeforeRender(function () {
            $maxFileSize = $this->get('maxFileSize');

            $serverMaxFileSize = Init::maxFileUpload(Init::MB);

            if ($maxFileSize === null) {
                $this->set('maxFileSize', $serverMaxFileSize);

                return;
            }

            throw_if(
                $maxFileSize > $serverMaxFileSize,
                'Cannot set the desired maximum file size. This contradicts the settings specified in .ini'
            );
        });
    }

    /**
     * @param string $storage
     *
     * @throws \Throwable
     *
     * @return $this
     */
    public function storage(string $storage): self
    {
        $disk = config("filesystems.disks." . $storage);

        throw_if($disk === null, 'The selected storage was not found');

        return $this
            ->set('storage', $storage)
            ->set('visibility', $disk['visibility'] ?? null);
    }
}
