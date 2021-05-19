<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use \Wikichua\Instant\Http\Traits\AllModelTraits;
    public $searchableFields = ['key'];

    protected $need_audit = true;
    protected $snapshot = true;
    protected $menu_icon = 'fas fa-cogs';

    protected $appends = ['isMultiple', 'rows', 'readUrl'];
    protected $fillable = [
        'key',
        'value',
        'protected',
        'created_by',
        'updated_by',
    ];

    public function scopeFilteKey($query, $search)
    {
        return $query->where('key', 'like', "%{$search}%");
    }

    public function getValueAttribute($value)
    {
        if (isset($this->attributes['protected']) && 1 == $this->attributes['protected']) {
            $value = decrypt(trim($value));
        }
        if (json_decode($value)) {
            return json_decode($value, 1);
        }

        return $value;
    }

    public function setValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
        if (request()->has('protected') && 1 == request()->get('protected')) {
            $this->attributes['value'] = encrypt($this->attributes['value']);
        }
    }

    public function getIsMultipleAttribute()
    {
        $value = $this->attributes['value'];
        if (isset($this->attributes['protected']) && 1 == $this->attributes['protected']) {
            $value = decrypt($value);
        }
        if (json_decode($value)) {
            return true;
        }

        return false;
    }

    public function getRowsAttribute()
    {
        $value = $this->attributes['value'];
        if (isset($this->attributes['protected']) && 1 == $this->attributes['protected']) {
            $value = decrypt($value);
        }
        if (json_decode($value)) {
            $array = json_decode($value, true);
            $rows = [];
            foreach ($array as $key => $val) {
                $rows[] = [
                    'index' => $key,
                    'value' => $val,
                ];
            }

            return $rows;
        }

        return [
            'index' => null,
            'value' => null,
        ];
    }

    public function getAllSettings()
    {
        $sets = [];
        $settings = app(config('instant.Models.Setting'))->all();
        foreach ($settings as $setting) {
            if (1 == $setting->protected) {
                $setting->value = decrypt($setting->value);
            }
            if (is_array($setting->value)) {
                foreach ($setting->value as $k => $v) {
                    $sets[$setting->key][] = [
                        'value' => $k,
                        'text' => $v,
                    ];
                }
            } else {
                $sets[$setting->key] = $setting->value;
            }
        }

        return $sets;
    }

    public function getReadUrlAttribute($value)
    {
        return $this->readUrl = route('setting.show', $this->id);
    }

    public function onCachedEvent()
    {
        cache()->forget('config-settings');
    }
}
