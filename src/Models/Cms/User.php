<?php

namespace LaravelCms\Models\Cms;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
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
    use HasFactory, Notifiable, Filterable, Attachable, HasRoles;

    const ACTIVE = 1;

    protected static Collection|null $all = null;

    protected string $guard = 'cms';
    protected $table = 'cms_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status_id',
        'remember_token',
        'time_from',
        'time_till',
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

    public function isTimeAllowed(): bool
    {
        if (is_null($this->time_from) && is_null($this->time_till))
            return true;

        $current = now();

        if (is_null($this->time_from)) {
            $till = Carbon::createFromFormat('H:i', $this->time_till);

            if ($current->lte($till))
                return true;

        } elseif (is_null($this->time_till)) {
            $from = Carbon::createFromFormat('H:i', $this->time_from);

            if ($current->gte($from))
                return true;

        } else {
            $from = Carbon::createFromFormat('H:i', $this->time_from);
            $till = Carbon::createFromFormat('H:i', $this->time_till);

            if ($from->gt($till)) {
                $till = $till->addDay();
            }

            if ($current->gte($from) && $current->lte($till))
                return true;
        }

        return false;
    }

    public static function getAll()
    {
        if (is_null(self::$all)) {
            self::$all = self::query()->orderBy('name', 'asc')->get();
        }

        return self::$all;
    }

    public static function pluck(string $column, string $key = null): array
    {
        $data = [];

        foreach (self::getAll() as $item) {
            if ($key)
                $data[$key == 'id' ? $item->getKey() : $item->$key] = $item->$column;
            else
                $data[] = $item->$column;
        }

        return $data;
    }

    public static function getWhere(array $options)
    {
        $data = [];

        $ids = null;
        if (isset($options['id']) && $options['id']) {
            $ids = is_array($options['id']) ? $options['id'] : [$options['id']];
            unset($options['id']);
        }

        foreach (self::getAll() as $item) {
            if ($ids && !in_array($item->getKey(), $ids))
                continue;

            if ($options) {
                if (array_intersect_assoc($item->attributesToArray(), $options) == $options) {
                    $data[] = $item;
                }

            } else if ($ids && !$options) {
                $data[] = $item;
            }
        }

        return collect($data);
    }

    public static function getBy($value, string $column = 'id')
    {
        foreach (self::getAll() as $item) {
            if ($column == 'id' && $value == $item->getKey()) {
                return $item;

            } else if ($column == 'slug' && $value == Str::slug($item->title, '_')) {
                return $item;

            } else if ($item->$column == $value) {
                return $item;
            }
        }

        return null;
    }
}
