<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;

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
        'ActionAt',
        'last_login_at',
        'last_logout_at',
        'last_login_ip',
        'remember_token'
    ];

    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'UserID', 'UserID');
    }

    public function hasRole($roleName)
    {
        if (!$this->role) return false;
        return strtolower($this->role->RoleName) === strtolower($roleName);
    }

    public function hasAnyRole(array $roleNames)
    {
        if (!$this->role) {
            return false;
        }

        foreach ($roleNames as $roleName) {
            if (strtolower($this->role->RoleName) === strtolower($roleName)) {
                return true;
            }
        }

        return false;
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
