<?php

namespace Database\Seeders;

use App\Models\Plant;
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
        $this->call(UsersSeeder::class);
        $this->call(PlantSeeder::class);
        $this->call(PrioritySeeder::class);


        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        // Assign role as admin
        $user->assignRole('admin');

        // spatie add role
        // // Create 50 users with the random role
        User::factory(40)->create()->each(function ($user) {
            // Assign a random role to each user
            $roles = ['admin', 'user'];
            $randomRole = $roles[array_rand($roles)];
            $user->assignRole($randomRole);
        });
    }
}
