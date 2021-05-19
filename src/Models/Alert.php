<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use \Wikichua\Instant\Http\Traits\AllModelTraits;
    public $searchableFields = [];

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'brand_id',
        'message',
        'icon',
        'link',
        'status',
    ];

    protected $appends = [];

    public function brand()
    {
        return $this->belongsTo(app(config('instant.Models.Brand')), 'brand_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(app(config('instant.Models.User')), 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(app(config('instant.Models.User')), 'receiver_id', 'id');
    }
}
