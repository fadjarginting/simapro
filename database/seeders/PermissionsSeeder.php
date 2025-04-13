<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar permission sesuai dengan menu dan aksi
        $permissions = [
            // Dashboard
            'dashboard.view',
            
            // User Management
            'user_management.view',
            'user_management.create',
            'user_management.edit',
            'user_management.delete',
            'user_management.reset_password',
            
            // Roles Management
            'roles_management.view',
            'roles_management.create',
            'roles_management.edit',
            'roles_management.delete',

            // Documents Activity Log
            'documents_activity_log.view',

            // Login Audit Trail
            'login_audit_trail.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permission to role
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo($permissions);

        
    }
}
