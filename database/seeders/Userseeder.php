<?php

namespace Database\Seeders;

use App\Enums\UserPermissionsEnum;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Database\Seeder;

class Userseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'id' => 1,
                'password'     => 'admin',
                'firstname'    => 'admin',
                'username'     => 'admin',
                'lastname'     => 'admin',
                'phone_number' => '07068628887',
                'role'         => UserPermissionsEnum::ADMIN(),
                'email_verified_at' => now(),
                'is_active'         => true
            ]
        );

        Wallet::firstOrCreate(['user_id' => $user->id]);
    }
}
