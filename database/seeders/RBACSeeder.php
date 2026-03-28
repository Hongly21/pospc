<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RBACSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Roles
        $adminRole = Role::firstOrCreate(['RoleName' => 'Admin']);
        $managerRole = Role::firstOrCreate(['RoleName' => 'Manager']);
        $staffRole = Role::firstOrCreate(['RoleName' => 'Staff']);

        // 2. Create Permissions
        $permissions = [
            'view_dashboard',
            'manage_products',
            'view_products',
            'manage_users',
            'manage_inventory',
            'make_sales'
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['PermissionName' => $p]);
        }

        // 3. Assign Permissions to Roles
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions);

        // Staff only gets Sales and View Products
        $staffPermissions = Permission::whereIn('PermissionName', ['make_sales', 'view_products'])->get();
        $staffRole->permissions()->sync($staffPermissions);

        // 4. Assign Admin Role to User ID 1 (You)
        $user = User::find(1);
        if($user) {
            $user->RoleID = $adminRole->RoleID;
            $user->save();
        }
    }
}
