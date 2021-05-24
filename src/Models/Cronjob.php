<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Cronjob extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \Wikichua\Instant\Http\Traits\AllModelTraits;

    protected $need_audit = true;
    protected $snapshot = false;
    protected $menu_icon = 'fas fa-voicemail';

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'mode',
        'timezone',
        'command',
        'frequency',
        'status',
        'created_by',
        'updated_by',
        'output',
    ];

    protected $appends = [
        'status_name',
        // 'readUrl'
    ];

    protected $searchableFields = [
        'name',
    ];

    protected $casts = ['output' => 'array'];

    public function scopeFilterName($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeFilterStatus($query, $search)
    {
        return $query->whereIn('status', $search);
    }

    public function getStatusNameAttribute($value)
    {
        return settings('cronjob_status')[$this->attributes['status']] ?? 'P';
    }

    public function getOutputAttribute($value)
    {
        return is_array($value)? $value:[];
    }

    public function getReadUrlAttribute($value)
    {
        return $this->readUrl = isset($this->id)? route('cronjob.show', $this->id):null;
    }

    public function onCachedEvent()
    {
        cache()->tags('cronjob')->flush();
    }
}
