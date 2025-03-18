<?php

namespace Database\Seeders;


use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FoodCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('food_categories')->insert([
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Cabohydrate',
                 'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Protein',
                 'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Vitamin',
                 'created_at' => now(),
                'updated_at' => now(),
            ],
           
            [
                'id' => Str::uuid()->toString(),
                'userid' => 'b6e0c47a-754f-47d5-a647-67be8a319d28',
                'ryderid' => 'ab32f860-9e04-43b5-989e-dbe74da16dc6',
                'pickup_address' => '12 Fastline Street',
                'pickup_phone' => '2233445566',
                'pickup_state' => 'Bayelsa',
                'receiver_name' => 'Arthur Curry',
                'receiver_email' => 'arthurcurry@example.com',
                'receiver_address' => '99 Atlantis Avenue',
                'receiver_phone' => '3344556677',
                'receiver_state' => 'Rivers',
                'package_type' => 'Marine Equipment',
                'package_weight' => '70kg',
                'package_qty' => '7',
                'comment' => 'Waterproof items.',
                'charges' => '50000',
                'departureTime' => now()->addHours(7)->toDateTimeString(),
                'arrivalTime' => now()->addHours(14)->toDateTimeString(),
                'status' => 'In Transit',
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
