<?php

namespace LaravelCms\Models\Cms;

use LaravelCms\Models\BaseModel;

class UserNotification extends BaseModel
{
    protected $table = 'cms_user_notifications';

    protected $fillable = [
        'from_user_id',
        'cms_user_id',
        'cms_notification_id',
        'readed_at',
    ];

    protected $allowedFilters = [
        'from_user_id',
        'cms_user_id',
        'cms_notification_id',
        'readed_at',
    ];

    protected $allowedSorts = [
        'from_user_id',
        'cms_user_id',
        'cms_notification_id',
        'readed_at',
    ];

    protected $allowedRelationsFilters = [];

    protected $casts = [
        'readed_at' => 'datetime',
    ];

    public $defaultSortField = 'created_at';
    public $defaultSortOrder = 'desc';

    public function user()
    {
        return $this->belongsTo(User::class, 'cms_user_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'cms_notification_id');
    }

    public function textNotificationId()
    {
        return $this->notification ? $this->notification->subject : '—';
    }

    public function textUserId()
    {
        return $this->user ? $this->user->name : '—';
    }
}
