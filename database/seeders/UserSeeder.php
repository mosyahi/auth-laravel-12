<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'              => 'Admin Super',
            'telepon'           => '081234567890',
            'status'            => 'active',                    // active / inactive / blocked
            'role'              => '1',
            'kode_user'         => null,
            'email'             => 'mosyahisample@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password'          => Hash::make('Admin.123'),
            'foto'              => null,
            'otp_type'          => 'N',                         // Y = Pake OTP, N = Tidak Pake OTP
            'otp'               => null,
            'otp_expired_at'    => null,
            'remember_token'    => Str::random(10),
        ]);
    }
}
