<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Daftar kategori barang untuk UMKM kembang/bunga.
     */
    public static array $data = [
        ['kategori' => 'Bunga Potong', 'deskripsi' => 'Bunga segar yang dijual per tangkai, seperti mawar, melati, dan krisan.'],
        ['kategori' => 'Bunga Hias Pot', 'deskripsi' => 'Tanaman bunga hidup dalam pot untuk dekorasi dalam/luar ruangan.'],
        ['kategori' => 'Rangkaian & Buket', 'deskripsi' => 'Rangkaian bunga jadi seperti buket wisuda, pernikahan, dan papan bunga.'],
        ['kategori' => 'Aksesoris & Pita', 'deskripsi' => 'Perlengkapan pendukung rangkaian bunga seperti pita, kertas, dan floral foam.'],
        ['kategori' => 'Pupuk & Media Tanam', 'deskripsi' => 'Pupuk, sekam, dan media tanam untuk perawatan bunga hias pot.'],
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
                DB::table('kategoris')->updateOrInsert(
                    ['user_id' => $userId, 'kategori' => $item['kategori']],
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
