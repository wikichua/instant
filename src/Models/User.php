<?php

namespace Wikichua\Instant\Models;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class User extends Authenticatable
{
    use \Wikichua\Instant\Http\Traits\AdminUser;
    use \Wikichua\Instant\Http\Traits\AllModelTraits;
    use \Laravel\Sanctum\HasApiTokens;
    use \Lab404\Impersonate\Models\Impersonate;

    public $searchableFields = ['name', 'email'];

    protected $need_audit = true;
    protected $snapshot = true;
    protected $menu_icon = 'fas fa-users';

    protected $appends = ['roles_string', 'readUrl'];

    public function brand()
    {
        return $this->belongsTo(app(config('instant.Models.Brand')), 'brand_id', 'id');
    }

    public function getRolesStringAttribute()
    {
        return $this->roles->sortBy('name')->implode('name', ', ');
    }

    public function scopeFilterType($query, $search)
    {
        return $query->where('type', $search);
    }

    public function scopeFilterName($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeFilterEmail($query, $search)
    {
        return $query->where('email', 'like', "%{$search}%");
    }

    public function scopeFilterCreatedAt($query, $search)
    {
        if (\Str::contains($search, ' - ')) { // date range
            $search = explode(' - ', $search);
            $start_at = Carbon::parse($search[0])->format('Y-m-d 00:00:00');
            $stop_at = Carbon::parse($search[1])->addDay()->format('Y-m-d 00:00:00');
        } else { // single date
            $start_at = Carbon::parse($search)->format('Y-m-d 00:00:00');
            $stop_at = Carbon::parse($search)->addDay()->format('Y-m-d 00:00:00');
        }

        return $query->whereBetween('created_at', [
            $this->inUserTimezone($start_at),
            $this->inUserTimezone($stop_at),
        ]);
    }

    public function getReadUrlAttribute($value)
    {
        return $this->readUrl = route('user.show', $this->id);
    }

    public function activitylogs()
    {
        return $this->hasMany(config('instant.Models.Audit'), 'user_id', 'id')->orderBy('created_at', 'desc');
    }

    public function onCachedEvent()
    {
        // cache()->forget('permissions:'.$this->id);
        cache()->tags('permissions')->flush();
    }
}
