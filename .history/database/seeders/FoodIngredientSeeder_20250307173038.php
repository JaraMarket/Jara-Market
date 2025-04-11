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
                'ingredient_id' => '1800'
            [
                'id' => 2,
                'name' => 'Obu',
                'price' => '800',
                'description' => 'nil',
                'image' => 'https://img.freepik.com/premium-photo/black-eyed-peas-wooden-bowl-isolated-white-background_33736-2909.jpg?w=740',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Mango',
                'price' => '300',
                'description' => 'nil',
                'image' => 'https://img.freepik.com/free-photo/delicious-mango-still-life_23-2151542130.jpg?t=st=1741159925~exp=1741163525~hmac=c668d7171c5b1fd8e8c4e06dcbe34d5100ce360f43953d0da5b8337be0b14e84&w=1060',
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
