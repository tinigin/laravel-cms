<?php

namespace LaravelCms\Models\Cms;

use LaravelCms\Attachment\Attachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use LaravelCms\Attachment\Models\Attachment;
use LaravelCms\Models\Cms\Section;
use LaravelCms\Notifications\MailResetPasswordToken;
use LaravelCms\Filters\Filterable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Filterable, Attachable;

    const ACTIVE = 1;

    protected string $guard = 'cms';
    protected $table = 'cms_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status_id',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $allowedFilters = [
        'name',
        'email',
        'status_id',
        'sections'
    ];

    protected $allowedSorts = [
        'name',
        'email',
        'sections'
    ];

    protected $allowedRelationsFilters = [
        'section.id' => \LaravelCms\Models\Cms\Section::class
    ];

    public $defaultSortField = 'name';

    public $defaultSortOrder = 'asc';

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'cms_user_to_sections', 'cms_user_id', 'cms_section_id');
    }

    /**
     * Send a password reset email to the user
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordToken($token));
    }

    /**
     * Get all of the user's files.
     */
    public function files()
    {
        return $this->morphMany(Attachment::class, 'user');
    }
}
