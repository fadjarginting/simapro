<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an admin user and assign the 'admin' role
        $admin = User::factory()->create([
            'name' => 'Saya',
            'email' => 'saya@example.com',
        ]);
        $admin->assignRole('admin');

        // Create a user with the 'user' role
        $user = User::factory()->create([
            'name' => 'aan',
            'email' => 'aan@example.com',
        ]);
        $user->assignRole('user');
    }
}
