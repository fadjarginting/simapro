<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles Table Seeder
        
        Role::create(['name' => 'User']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Lead']);
    }
}
