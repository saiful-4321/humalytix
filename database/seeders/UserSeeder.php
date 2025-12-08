<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Enums\PaymentStatusEnum;
use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Models\EkycProgressbar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@mistrivai.com',
                'password' => Hash::make('12345678'), // It's better to hash the password
                'mobile' => '01644331161',
                'nid' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'email_verified_at' => now(),
                'api_token' => str()->random(20),
                'status' => UserStatusEnum::Verified,
                'payment_status' => PaymentStatusEnum::Pending,
                'bo_status' => ActiveInactiveEnum::Active,
                'bo_cdbl_approval_file' => null,
                'remember_token' => str()->random(10),
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => null,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@mistrivai.com',
                'password' => Hash::make('12345678'), // It's better to hash the password
                'mobile' => '01731632434',
                'nid' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'email_verified_at' => now(),
                'api_token' => str()->random(20),
                'status' => UserStatusEnum::Verified,
                'payment_status' => PaymentStatusEnum::Pending,
                'bo_status' => ActiveInactiveEnum::Active,
                'bo_cdbl_approval_file' => null,
                'remember_token' => str()->random(10),
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => null,
            ]
        ]);

        // progress
        EkycProgressbar::insert([
            [
                'user_id' => 1,
                'basic_registration' => 1,
                'otp_verification' => 1,
            ],
            [
                'user_id' => 2,
                'basic_registration' => 1,
                'otp_verification' => 1,
            ]
        ]);
    }
}
