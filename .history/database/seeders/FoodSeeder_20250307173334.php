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
        DB::table('food_ingredients')->insert([
            [
                'id' => 1,
                'food_id' => 'Garri',
                'ingredient_id' => '1800',
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
