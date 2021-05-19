<?php

namespace Wikichua\Instant\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Notifications\ResetPassword;
use Wikichua\Instant\Notifications\ResetAdminPassword;

trait AdminUser
{
    // roles relationship
    public function roles()
    {
        return $this->belongsToMany(config('instant.Models.Role'));
    }

    // permissions relationship
    public function permissions()
    {
        return $this->belongsToMany(config('instant.Models.Permission'));
    }

    // activity logs relationship
    public function activity_logs()
    {
        return $this->hasMany(config('instant.Models.Audit'));
    }

    // combined user + role permissions
    public function flatPermissions($user_id = '')
    {
        if ('' == $user_id || null == $user_id) {
            $user_id = auth()->id();
        }

        return Cache::tags('permissions')->remember('permissions:'.$user_id, (60 * 60 * 24), function () {
            return $this->permissions->merge($this->roles->flatMap(function ($role) {
                return $role->permissions;
            }));
        });
    }

    // check if user has permission
    public function hasPermission($name, $user_id = '')
    {
        return $this->flatPermissions($user_id)->contains('name', $name) || $this->roles->contains('admin', true);
    }

    // use admin url in password reset email link
    public function sendPasswordResetNotification($token)
    {
        if (request()->route()->getName('admin.password.email')) {
            $this->notify(new ResetAdminPassword($token));
        } else {
            $this->notify(new ResetPassword($token));
        }
    }

    public function allPermissions()
    {
        $permissions = [];
        foreach (app(config('instant.Models.Permission'))->all() as $permission) {
            if (auth()->user()->can($permission->name)) {
                $permissions[] = $permission->name;
            }
        }

        return $permissions;
    }
}
