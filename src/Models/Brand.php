<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \Wikichua\Instant\Http\Traits\AllModelTraits;

    protected $need_audit = true;
    protected $snapshot = true;
    protected $menu_icon = 'fas fa-laptop';

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'created_by',
        'updated_by',
        'name',
        'domain',
        'published_at',
        'expired_at',
        'status',
    ];

    protected $appends = [
        'status_name',
        'readUrl',
    ];

    protected $searchableFields = ['name', 'domain'];

    protected $casts = [
    ];

    public function getPublishedAtAttribute($value)
    {
        return $this->inUserTimezone($value);
    }

    public function getExpiredAtAttribute($value)
    {
        return $this->inUserTimezone($value);
    }

    public function getStatusNameAttribute($value)
    {
        return isset($this->attributes['status']) ? settings('brand_status')[$this->attributes['status']]:'';
    }

    public function scopeFilterName($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeFilterDomain($query, $search)
    {
        return $query->where('domain', 'like', "%{$search}%");
    }

    public function scopeFilterPublishedAt($query, $search)
    {
        $date = $this->getDateFilter($search);

        return $query->whereBetween('published_at', [
            $this->inUserTimezone($date['start_at']),
            $this->inUserTimezone($date['stop_at']),
        ]);
    }

    public function scopeFilterExpiredAt($query, $search)
    {
        $date = $this->getDateFilter($search);

        return $query->whereBetween('expired_at', [
            $this->inUserTimezone($date['start_at']),
            $this->inUserTimezone($date['stop_at']),
        ]);
    }

    public function scopeFilterStatus($query, $search)
    {
        return $query->whereIn('status', $search);
    }

    public function getReadUrlAttribute($value)
    {
        return $this->readUrl = (\Route::has('brand.show') && isset($this->id)) ? route('brand.show', $this->id) : '';
    }

    public function onCachedEvent()
    {
        cache()->tags(['brand', 'page'])->flush();
    }
}
