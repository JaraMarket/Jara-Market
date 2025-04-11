<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class FoodStuffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('food_stuffs')->insert([
            [
                'id' => 1,
                'name' => 'Garri',
                'category_id' => '1',
                'amount' => '350',
                'image' => 'https://img.freepik.com/free-psd/pile-fine-beige-sand-isolated-transparent-background_191095-86674.jpg?t=st=1741159817~exp=1741163417~hmac=049ad381c7510d91db99e6720a68fe6fef5c3983f89761a19d6752b1a6d05750&w=740',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Bean',
                'category_id' => '2',
                'amount' => '500',
                'image' => 'https://img.freepik.com/premium-photo/black-eyed-peas-wooden-bowl-isolated-white-background_33736-2909.jpg?w=740',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Mango',
                'category_id' => '3',
                'amount' => '450',
                'image' => 'Cabohydrate',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 4,
                'name' => 'Cabohydrate',
                'category_id' => '3',
                'amount' => 'Cabohydrate',
                'image' => 'Cabohydrate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
