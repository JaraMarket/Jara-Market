<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('foods')->insert([
            [
                'id' => 1,
                'name' => 'Garri',
                'description' => '1800',
                'price' => '1800',
                'description' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'food_id' => 'Garri',
                'ingredient_id' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'food_id' => 'Garri',
                'ingredient_id' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 4,
                'food_id' => 'Garri',
                'ingredient_id' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
