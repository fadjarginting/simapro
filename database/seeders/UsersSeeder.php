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
        $admin->assignRole('Admin');

        // Create a user with the 'user' role
        $user = User::factory()->create([
            'name' => 'aan',
            'email' => 'aan@example.com',
            'discipline_id' => 1, // Assuming discipline_id is required
        ]);
        $user->assignRole('Lead');

        $leads = [
            ['name' => 'AULIA EKADANA FAUTHRISNO', 'email' => 'aulia.e@example.com', 'discipline_id' => 1],
            ['name' => 'NOVRIADI M', 'email' => 'novriadi.m@example.com', 'discipline_id' => 2],
            ['name' => 'MOCH CHOIRIL ANAM', 'email' => 'anam.mc@example.com', 'discipline_id' => 3],
            ['name' => 'ANDRA NOVENDRI', 'email' => 'andra.n@example.com', 'discipline_id' => 2],
            ['name' => 'MARJUKI', 'email' => 'marjuki@example.com', 'discipline_id' => 4],
            ['name' => 'MUHAMMAD FATHUL MUBIN', 'email' => 'fathul.mubin@example.com', 'discipline_id' => 1],
        ];

        foreach ($leads as $leadData) {
            $lead = User::factory()->create([
            'name' => $leadData['name'],
            'email' => $leadData['email'],
            'discipline_id' => $leadData['discipline_id'],
            ]);
            $lead->assignRole('Lead');
        }

    }
}
