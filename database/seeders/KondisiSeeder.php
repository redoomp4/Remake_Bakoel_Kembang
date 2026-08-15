<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KondisiSeeder extends Seeder
{
    public static array $data = [
        ['nama_kondisi' => 'Segar', 'deskripsi' => 'Bunga dalam kondisi segar dan layak jual.'],
        ['nama_kondisi' => 'Layu Sebagian', 'deskripsi' => 'Sebagian bunga mulai layu, masih bisa dijual dengan diskon.'],
        ['nama_kondisi' => 'Rusak/Busuk', 'deskripsi' => 'Barang rusak atau busuk dan tidak layak jual.'],
        ['nama_kondisi' => 'Baik', 'deskripsi' => 'Kondisi baik untuk barang non-bunga seperti aksesoris dan pupuk.'],
        ['nama_kondisi' => 'Kadaluarsa', 'deskripsi' => 'Barang sudah melewati tanggal kadaluarsa.'],
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
                DB::table('kondisis')->updateOrInsert(
                    ['user_id' => $userId, 'nama_kondisi' => $item['nama_kondisi']],
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
