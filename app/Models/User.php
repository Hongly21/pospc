<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'UserID';

    protected $fillable = [
        'Username',
        'Email',
        'PasswordHash',
        'RoleID',
        'PhoneNumber',
        'UserImage',
        'Status',
        'ActionBy',
        'ActionAt'
    ];

    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }

    public function hasRole($roleName)
    {
        if (!$this->role) return false;
        return strtolower($this->role->RoleName) === strtolower($roleName);
    }

    public function hasPermission($permissionName)
    {
        if (!$this->role) return false;

        if ($this->hasRole('Admin')) {
            return true;
        }

        return $this->role->permissions()->where('PermissionName', $permissionName)->exists();
    }
}
