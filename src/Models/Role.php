<?php

namespace Wikichua\Instant\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use \Wikichua\Instant\Http\Traits\AllModelTraits;
    public $searchableFields = ['name'];

    protected $appends = ['isAdmin', 'readUrl'];
    protected $menu_icon = 'fas fa-id-badge';

    protected $need_audit = true;
    protected $snapshot = true;

    public function permissions()
    {
        return $this->belongsToMany(config('instant.Models.Permission'));
    }

    public function users()
    {
        return $this->belongsToMany(config('instant.Models.User'));
    }

    public function getIsAdminAttribute($value)
    {
        return $this->admin ? 'Yes' : 'No';
    }

    public function scopeFilterName($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeFilterAdmin($query, $search)
    {
        if ($search == '') {
            return $query;
        }
        return $query->where('admin', $search);
    }

    public function getReadUrlAttribute($value)
    {
        return $this->readUrl = isset($this->id)? route('role.show', $this->id):null;
    }

    public function onCachedEvent()
    {
        cache()->tags('permissions')->flush();
        // $user_ids = \DB::table('role_user')->distinct('user_id')->where('role_id', $this->id)->pluck('user_id');
        // foreach ($user_ids as $user_id) {
        //     cache()->forget('permissions:'.$user_id);
        // }
    }
}
