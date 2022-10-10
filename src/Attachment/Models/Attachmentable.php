<?php

namespace LaravelCms\Attachment\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Attachmentable.
 */
class Attachmentable extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $table = 'attachmentable';

    public function model()
    {
        return $this->morphTo('attachmentable', 'attachmentable_type', 'attachmentable_id');
    }
}
