<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin111'],
            [
                'name' => 'Admin',
                'email' => 'orchid.bpn@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'status' => 'aktif',
                'position' => 'admin',
                'phone' => '081142389833',
                'photo' => 'default.jpg',
            ]
        );


        // Seeder untuk Penjual
        User::firstOrCreate(
            ['username' => 'penjual123'], // Unik check berdasarkan username
            [
                'name' => 'Toko Penjual',
                'email' => 'penjual@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // Ganti password sesuai kebutuhan
                'role' => 'penjual', // Mengubah role menjadi penjual
                'status' => 'aktif',
                'position' => 'penjual',
                'phone' => '081234567890',
                'photo' => 'default.jpg',
            ]
        );
    }
}
