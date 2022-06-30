<?php

namespace App\Core\Events;

use Illuminate\Queue\SerializesModels;
use LaravelCms\Attachment\Models\Attachment;

/**
 * Class UploadedFileEvent.
 */
class UploadedFileEvent
{
    use SerializesModels;

    /**
     * @var Attachment
     */
    public $attachment;

    /**
     * UploadedFileEvent constructor.
     *
     * @param Attachment $attachment
     */
    public function __construct(Attachment $attachment)
    {
        $this->attachment = $attachment;
    }
}
