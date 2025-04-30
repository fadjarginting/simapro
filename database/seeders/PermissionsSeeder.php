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

            // User Management
            'user_management.view',
            'user_management.create',
            'user_management.edit',
            'user_management.delete',

            // Roles Management
            'roles_management.view',
            'roles_management.create',
            'roles_management.edit',
            'roles_management.delete',

            // Work Audit Log
            'work_audit.view',

            // Login Audit Trail
            'login_audit_trail.view',
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permission to roles
        // Cari role yang sudah ada, bukan buat baru!
        $admin = Role::where('name', 'admin')->first();
        if (!$admin) {
            $admin = Role::create(['name' => 'admin']);
        }
        $admin->givePermissionTo($permissions);

        $user = Role::where('name', 'user')->first();
        $user?->givePermissionTo([
            'erf_management.view',
            'progress_report.view',
            'morning_report.view',
            'kpi_management.view',
        ]);

        $engineer = Role::where('name', 'engineer')->first();
        $engineer?->givePermissionTo([
            'erf_management.view',
            'progress_report.view',
            'morning_report.view',
            'kpi_management.view',
        ]);

        $manager = Role::where('name', 'manager')->first();
        if (!$manager) {
            $manager = Role::create(['name' => 'manager']);
        }
        $manager->givePermissionTo([
            'erf_management.view',
            'progress_report.view',
            'morning_report.view',
            'kpi_management.view',
        ]);
    }
}
