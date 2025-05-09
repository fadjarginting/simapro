<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Noted;
use App\Models\Plant;
use App\Models\Classification;
use App\Models\Priority;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            ['id' => 1, 'name' => 'P1', 'slug' => 'p-1'],
            ['id' => 2, 'name' => 'P2', 'slug' => 'p-2'],
        ];

        foreach ($priorities as $priority) {
            Priority::create([
                'name' => $priority['name'],
                'slug' => $priority['slug'],
            ]);
        }

        //category table
        $categories = [
            ['id' => 1, 'name' => 'OPEX', 'slug' => 'opex'],
            ['id' => 2, 'name' => 'CAPEX', 'slug' => 'capex'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
            ]);
        }
    }
}
