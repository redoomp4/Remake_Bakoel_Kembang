<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PemasokSeeder extends Seeder
{
    public static array $data = [
        [
            'nama_pemasok' => 'CV Kembang Sari Nusantara',
            'email' => 'kontak@kembangsari.co.id',
            'jenis' => 'Distributor Bunga Potong',
            'nama_pic' => 'Budi Santoso',
            'alamat' => 'Jl. Raya Pasar Bunga No. 12, Rawabelong, Jakarta',
            'no_telepon' => '021-5551234',
            'bergabung_sejak' => '2022-03-15',
        ],
        [
            'nama_pemasok' => 'Toko Bunga Melati Jaya',
            'email' => 'melatijaya@gmail.com',
            'jenis' => 'Grosir Bunga Lokal',
            'nama_pic' => 'Siti Aminah',
            'alamat' => 'Jl. Melati Indah No. 5, Bandung',
            'no_telepon' => '022-7778899',
            'bergabung_sejak' => '2022-07-01',
        ],
        [
            'nama_pemasok' => 'UD Anggrek Indah',
            'email' => 'anggrekindah@yahoo.com',
            'jenis' => 'Petani Anggrek',
            'nama_pic' => 'Hendra Wijaya',
            'alamat' => 'Jl. Kebun Anggrek No. 8, Malang',
            'no_telepon' => '0341-556677',
            'bergabung_sejak' => '2023-01-20',
        ],
        [
            'nama_pemasok' => 'PT Flora Prima Indonesia',
            'email' => 'sales@floraprima.id',
            'jenis' => 'Importir & Distributor',
            'nama_pic' => 'Ratna Kusuma',
            'alamat' => 'Jl. Industri Bunga No. 20, Bogor',
            'no_telepon' => '0251-334455',
            'bergabung_sejak' => '2021-11-10',
        ],
        [
            'nama_pemasok' => 'Kebun Bunga Berkah Tani',
            'email' => 'berkahtani@gmail.com',
            'jenis' => 'Petani Lokal',
            'nama_pic' => 'Agus Prasetyo',
            'alamat' => 'Desa Cikahuripan, Lembang, Bandung Barat',
            'no_telepon' => '081234567890',
            'bergabung_sejak' => '2023-05-05',
        ],
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
                DB::table('pemasoks')->updateOrInsert(
                    ['user_id' => $userId, 'nama_pemasok' => $item['nama_pemasok']],
                    [
                        'email' => $item['email'],
                        'jenis' => $item['jenis'],
                        'nama_pic' => $item['nama_pic'],
                        'alamat' => $item['alamat'],
                        'no_telepon' => $item['no_telepon'],
                        'bergabung_sejak' => $item['bergabung_sejak'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
