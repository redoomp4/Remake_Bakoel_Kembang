<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id');

        if ($userIds->isEmpty()) {
            $this->command?->warn('Tidak ada user ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        foreach ($userIds as $userId) {
            $items = DB::table('items')->where('user_id', $userId)->pluck('nama_barang')->all();

            if (empty($items)) {
                $this->command?->warn("Belum ada item untuk user_id {$userId}. Jalankan ItemSeeder dahulu.");
                continue;
            }

            // Hapus notifikasi lama milik user ini agar seeder aman dijalankan ulang.
            DB::table('notifications')->where('user_id', $userId)->delete();

            $notifikasi = [
                [
                    'message' => "Stok \"{$items[array_rand($items)]}\" akan segera kadaluarsa dalam 3 hari.",
                    'type' => 'expired_soon',
                    'is_read' => false,
                ],
                [
                    'message' => "Stok \"{$items[array_rand($items)]}\" sudah di bawah batas minimum, segera lakukan restok.",
                    'type' => 'low_stock',
                    'is_read' => false,
                ],
                [
                    'message' => "Barang \"{$items[array_rand($items)]}\" tidak ada pergerakan penjualan selama 30 hari terakhir.",
                    'type' => 'slow_moving',
                    'is_read' => true,
                ],
                [
                    'message' => "Stok \"{$items[array_rand($items)]}\" akan kadaluarsa dalam 1 hari, segera diskon atau jual.",
                    'type' => 'expired_soon',
                    'is_read' => false,
                ],
                [
                    'message' => "Stok \"{$items[array_rand($items)]}\" hampir habis, sisa di bawah stok minimum.",
                    'type' => 'low_stock',
                    'is_read' => true,
                ],
            ];

            foreach ($notifikasi as $notif) {
                DB::table('notifications')->insert([
                    'user_id' => $userId,
                    'message' => $notif['message'],
                    'type' => $notif['type'],
                    'is_read' => $notif['is_read'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
