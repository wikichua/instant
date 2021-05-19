<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use \Wikichua\Instant\Http\Traits\AllModelTraits;
    public $searchableFields = ['name', 'group'];

    protected $menu_icon = 'fas fa-lock';
    protected $need_audit = true;
    protected $snapshot = true;

    protected $appends = ['readUrl'];

    // roles relationship
    public function roles()
    {
        return $this->belongsToMany(config('instant.Models.Role'));
    }

    // users relationship
    public function users()
    {
        return $this->belongsToMany(config('instant.Models.User'));
    }

    // create permission group
    public function createGroup($group, $names = [], $user_id = '1')
    {
        foreach ($names as $name) {
            $this->create([
                'group' => $group,
                'name' => $name,
                'created_by' => auth()->check() ? auth()->id() : $user_id,
                'updated_by' => auth()->check() ? auth()->id() : $user_id,
            ]);
        }
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = str_slug($value);
    }

    public function scopeFilterName($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeFilterGroup($query, $search)
    {
        return $query->whereIn('group', $search);
    }

    public function getReadUrlAttribute($value)
    {
        return $this->readUrl = isset($this->id) ? route('permission.show', $this->id):null;
    }

    public function onCachedEvent()
    {
        cache()->tags('permissions')->flush();
        // $role_ids = $this->roles()->pluck('role_id');
        // $user_ids = \DB::table('role_user')->distinct('user_id')->whereIn('role_id', $role_ids)->pluck('user_id');
        // foreach ($user_ids as $user_id) {
        //     cache()->forget('permissions:'.$user_id);
        // }
    }
}
