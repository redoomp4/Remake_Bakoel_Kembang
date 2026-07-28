<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin111',
            'email' => 'orchid.bpn@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'superadmin', 
            'status'=> 'aktif',
            'position' => 'admin',
            'phone' => '081142389833',
            'photo' => 'default.jpg',
        ]);
    }
}
