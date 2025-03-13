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

            // Released Documents
            'released_documents.view',
            'released_documents.create',
            'released_documents.edit',
            'released_documents.delete',
            'released_documents.download',
            'released_documents.add_new_revision',
            'released_documents.share',

            // In Progress Eng Docs
            'in_progress_eng_docs.view',
            'in_progress_eng_docs.create',
            'in_progress_eng_docs.edit',
            'in_progress_eng_docs.delete',
            'in_progress_eng_docs.download',
            'in_progress_eng_docs.add_new_revision',
            'in_progress_eng_docs.share',

            // Download Request
            'download_request.view',            

            // Document Categories
            'document_categories.manage_document_categories',

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

        
    }
}
