<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'orchid.bpn@gmail.com',
            ],
            [
                'name' => 'Bakoel Kembang', // Jadi nama TOKO aja
                'username' => 'admin111',   // Jadi nama Pemilik / Admin
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'status' => 'aktif',
                'position' => 'admin',
                'phone' => '081142389833',
                'photo' => 'default.jpg',
            ]
        );
    }
}
