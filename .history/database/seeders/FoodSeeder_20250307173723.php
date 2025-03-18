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
                'name' => 'Afang',
                'description' => 'nil',
                'price' => '6800',
                'image_url' => 'https://img.freepik.com/free-psd/pile-fine-beige-sand-isolated-transparent-background_191095-86674.jpg?t=st=1741159817~exp=1741163417~hmac=049ad381c7510d91db99e6720a68fe6fef5c3983f89761a19d6752b1a6d05750&w=740',
                'category_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Egusi',
                'description' => 'nil',
                'price' => '9800',
                'image_url' => '1800',
                'category_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'White Soup',
                'description' => 'nil',
                'price' => '10800',
                'image_url' => '1800',
                'category_id' => '3',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 4,
                'name' => 'Rice',
                'description' => 'nil',
                'price' => '11800',
                'image_url' => '1800',
                'category_id' => '3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
