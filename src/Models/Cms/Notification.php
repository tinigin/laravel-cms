<?php

namespace LaravelCms\Models\Cms;

use LaravelCms\Models\BaseModel;
use LaravelCms\Models\Cms\User;

class Notification extends BaseModel
{
    protected $table = 'cms_notifications';

    protected $fillable = [
        'subject',
        'message',
        'is_important',
    ];

    protected $allowedFilters = [
        'subject',
        'message',
        'is_important',
    ];

    protected $allowedSorts = [
        'id',
        'subject',
        'message',
        'is_important',
    ];

    protected $allowedRelationsFilters = [];

    protected $casts = [
        'is_important' => 'boolean'
    ];

    public $defaultSortField = 'created_at';
    public $defaultSortOrder = 'desc';

    public function users()
    {
        return $this->belongsToMany(User::class, 'cms_user_notifications')->withTimestamps();
    }

    public static function send(
        array|int $to,
        string $message,
        string $subject = null,
        int $from = null,
        bool$isImportant = false): void
    {
        $notification = new Notification();
        $notification->message = $message;
        if ($subject)
            $notification->subject = $subject;
        $notification->is_important = $isImportant;
        $notification->save();

        if (!is_array($to))
            $to = [$to];

        foreach ($to as $t) {
            $relation = new UserNotification();
            $relation->cms_user_id = $t;
            $relation->cms_notification_id = $notification->getKey();
            if ($from)
                $relation->from_user_id = $from;
            $relation->readed_at = null;
            $relation->save();
        }
    }
}
