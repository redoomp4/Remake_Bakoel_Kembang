<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiSeeder extends Seeder
{
    public static array $data = [
        ['nama_lokasi' => 'Gudang Utama', 'deskripsi' => 'Tempat penyimpanan utama untuk stok bunga potong dan aksesoris.'],
        ['nama_lokasi' => 'Rak Pendingin', 'deskripsi' => 'Rak berpendingin untuk menjaga kesegaran bunga potong.'],
        ['nama_lokasi' => 'Etalase Depan', 'deskripsi' => 'Area pajangan depan toko untuk bunga hias pot dan buket siap jual.'],
        ['nama_lokasi' => 'Rak Aksesoris', 'deskripsi' => 'Tempat penyimpanan pita, kertas pembungkus, dan perlengkapan rangkai.'],
        ['nama_lokasi' => 'Gudang Media Tanam', 'deskripsi' => 'Penyimpanan pupuk, sekam, dan media tanam.'],
    ];

    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id');

        if ($userIds->isEmpty()) {
            $this->command?->warn('Tidak ada user ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        foreach ($userIds as $userId) {
            foreach (self::$data as $item) {
                DB::table('lokasis')->updateOrInsert(
                    ['user_id' => $userId, 'nama_lokasi' => $item['nama_lokasi']],
                    [
                        'deskripsi' => $item['deskripsi'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
