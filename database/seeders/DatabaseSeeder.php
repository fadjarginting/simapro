<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // call RolesTableSeeder
        $this->call(PermissionsSeeder::class);
        $this->call(RolesTableSeeder::class);

        
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Assign role as admin
        $user->assignRole('admin');
        



        // spatie add role
        

        
    }
}
