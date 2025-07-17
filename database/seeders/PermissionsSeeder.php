<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache dulu
        Artisan::call('permission:cache-reset');

        // Daftar permission
        $permissions = [
            // Dashboard
            'dashboard.view',
            'my_dashboard.view',

            // ERF Management
            'manajemen_pekerjaan.view',
            'manajemen_pekerjaan.create',
            'manajemen_pekerjaan.edit',
            'manajemen_pekerjaan.delete',

            // My Works
            'my_works.view',

            // Morning Report
            'morning_report.view',

            // Key Performance Indicator
            'kpi_management.view',

            // Plant Settings
            'plant_settings.view',
            'plant_settings.create',
            'plant_settings.edit',
            'plant_settings.delete',

            // Noted Management
            'noted_management.view',
            'noted_management.create',
            'noted_management.edit',
            'noted_management.delete',

            // Request Category Settings
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            // User Management
            'user_management.view',
            'user_management.create',
            'user_management.edit',
            'user_management.delete',
            'user_management.reset_password',

            // Roles Management
            'role_management.view',
            'role_management.create',
            'role_management.edit',
            'role_management.delete',

            // Work Audit Log
            'work_audit.view',

            // Login Audit Trail
            'login_audit_trail.view',
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permission to roles
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo([
            'dashboard.view',
            'manajemen_pekerjaan.view',
            'manajemen_pekerjaan.create',
            'manajemen_pekerjaan.edit',
            'manajemen_pekerjaan.delete',
            'morning_report.view',
            'kpi_management.view',
            'plant_settings.view',
            'plant_settings.create',
            'plant_settings.edit',
            'plant_settings.delete',
            'noted_management.view',
            'noted_management.create',
            'noted_management.edit',
            'noted_management.delete',
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            'user_management.view',
            'user_management.create',
            'user_management.edit',
            'user_management.delete',
            'user_management.reset_password',
            'role_management.view',
            'role_management.create',
            'role_management.edit',
            'role_management.delete',
            'work_audit.view',
            'login_audit_trail.view'
        ]); // Sync all permissions to admin role    

        $role = Role::create(['name' => 'Lead']);
        $role->givePermissionTo([
            'my_dashboard.view',
            'my_works.view',
            'kpi_management.view',
        ]);


        $role = Role::create(['name' => 'User']);
        $role->givePermissionTo([
            'my_dashboard.view',
            'my_works.view',
            'kpi_management.view',
        ]);
    }
}
