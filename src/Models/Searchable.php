<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Searchable extends Model
{
    use \Wikichua\Instant\Http\Traits\AllModelTraits;

    protected $dates = [];
    protected $fillable = [];

    protected $appends = [];

    protected $searchableFields = [];

    protected $casts = [
        'tags' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(config('instant.Models.Brand'))->withDefault(['name' => null]);
    }

    public function scopeFilterTags($query, $search)
    {
        $searches = [
            $search,
            strtolower($search),
            strtoupper($search),
            ucfirst($search),
            ucwords($search),
        ];

        return $query->whereRaw('`tags` RLIKE ":\.*?('.implode('|', $searches).')\.*?"');
    }
}
