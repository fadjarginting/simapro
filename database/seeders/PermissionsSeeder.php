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

            // ERF Management
            'erf_management.view',
            'erf_management.create',
            'erf_management.edit',
            'erf_management.delete',

            // Progress Report
            'progress_report.view',
            'progress_report.create',
            'progress_report.show',
            'progress_report.edit',
            'progress_report.delete',

            // Morning Report
            'morning_report.view',

            // Key Performance Indicator
            'kpi_management.view',

            // EAT Schedule
            'eat_schedule.view',
            'eat_schedule.create',
            'eat_schedule.edit',
            'eat_schedule.delete',

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

            // Work Priority Settings
            'work_priority.view',
            'work_priority.create',
            'work_priority.edit',
            'work_priority.delete',

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
        $role->givePermissionTo($permissions);

        $role = Role::create(['name' => 'Lead']);
        $role->givePermissionTo($permissions);


        $role = Role::create(['name' => 'User']);
        $role->givePermissionTo([
            'erf_management.view',
            'progress_report.view',
            'morning_report.view',
            'kpi_management.view',
        ]);
    }
}
