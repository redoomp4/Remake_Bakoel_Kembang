<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    public static array $data = [
        'Tangkai',
        'Pot',
        'Ikat',
        'Buket',
        'Karung',
        'Meter',
    ];

    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id');

        if ($userIds->isEmpty()) {
            $this->command?->warn('Tidak ada user ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        foreach ($userIds as $userId) {
            foreach (self::$data as $namaSatuan) {
                DB::table('satuans')->updateOrInsert(
                    ['user_id' => $userId, 'nama_satuan' => $namaSatuan],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
