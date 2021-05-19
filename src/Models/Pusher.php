<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Pusher extends Model
{
    use \Wikichua\Instant\Http\Traits\AllModelTraits;
    public $searchableFields = ['title', 'event'];

    protected $need_audit = true;
    protected $snapshot = true;
    protected $menu_icon = 'fas fa-recycle';
    protected $fillable = [
        'created_by',
        'updated_by',
        'brand_id',
        'locale',
        'event',
        'title',
        'message',
        'icon',
        'link',
        'timeout',
        'scheduled_at',
        'status',
    ];

    protected $appends = ['status_name', 'event_name', 'readUrl'];

    public function brand()
    {
        return $this->belongsTo(app(config('instant.Models.Brand')), 'brand_id', 'id');
    }

    public function getStatusNameAttribute($value)
    {
        return settings('pusher_status')[$this->attributes['status']];
    }

    public function getEventNameAttribute($value)
    {
        return settings('pusher_events')[$this->attributes['event']];
    }

    public function scopeFilterTitle($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%");
    }

    public function scopeFilterEvent($query, $search)
    {
        return $query->where('event', 'like', "%{$search}%");
    }

    public function getReadUrlAttribute($value)
    {
        if (\Route::has('pusher.show')) {
            return $this->readUrl = route('pusher.show', $this->id);
        }

        return '';
    }
}
