<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FoodIngredientSeeder extends Seeder
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
                'id' => 1,
                'food_id' => 'Garri',
                'ingredient_id' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 1,
                'food_id' => 'Garri',
                'ingredient_id' => '1800',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 4,
                'name' => 'Apple',
                'price' => '500',
                'description' => 'nil',
                'image' => 'https://img.freepik.com/free-psd/fresh-glistening-red-apple-with-leaf-transparent-background_84443-27689.jpg?t=st=1741159983~exp=1741163583~hmac=03268953d6d1d4560eceeb626eb3cc097cfee516329b8f485cdb542f23deae4d&w=740',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

}
