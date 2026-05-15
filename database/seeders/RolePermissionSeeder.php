<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'jobs.create',
            'jobs.view-own',
            'jobs.download-own',
            'presets.manage-own',
            'billing.manage-own',
            'admin.dashboard',
            'admin.users.manage',
            'admin.plans.manage',
            'admin.jobs.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $userRole       = Role::findOrCreate('user', 'web');
        $adminRole      = Role::findOrCreate('admin', 'web');
        $superAdminRole = Role::findOrCreate('superadmin', 'web');

        $userPermissions = Permission::whereIn('name', [
            'jobs.create',
            'jobs.view-own',
            'jobs.download-own',
            'presets.manage-own',
            'billing.manage-own',
        ])->where('guard_name', 'web')->get();

        $userRole->syncPermissions($userPermissions);
        $adminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
        $superAdminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
    }
}
