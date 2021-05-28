<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Versionizer extends Model
{
    use \Wikichua\Instant\Http\Traits\AllModelTraits;
    protected $need_audit = false;
    protected $menu_icon = 'fas fa-code-branch';

    protected $dates = ['reverted_at'];
    protected $fillable = [
        'id',
        'mode',
        'model_class',
        'model_id',
        'data',
        'changes',
        'brand_id',
        'reverted_at',
        'reverted_by',
        'created_at',
        'updated_at',
    ];

    protected $appends = [];

    protected $searchableFields = [];

    protected $casts = [
        'data' => 'array',
        'changes' => 'array',
    ];

    public function brand()
    {
        return $this->belongsTo(config('instant.Models.Brand'))->withDefault(['name' => null]);
    }

    public function getRevertedAtAttribute($value)
    {
        return $this->inUserTimezone($value);
    }

    public function model()
    {
        return $this->model_class ? app($this->model_class)->find($this->model_id) : null;
    }

    public function revertor()
    {
        return $this->belongsTo(config('instant.Models.User'), 'reverted_by', 'id');
    }

    public function scopeFilterData($query, $search)
    {
        $searches = [
            $search,
            strtolower($search),
            strtoupper($search),
            ucfirst($search),
            ucwords($search),
        ];

        return $query->whereRaw('`data` RLIKE ":\.*?('.implode('|', $searches).')\.*?"');
    }

    public function scopeFilterChanges($query, $search)
    {
        $searches = [
            $search,
            strtolower($search),
            strtoupper($search),
            ucfirst($search),
            ucwords($search),
        ];

        return $query->whereRaw('`changes` RLIKE ":\.*?('.implode('|', $searches).')\.*?"');
    }
}
