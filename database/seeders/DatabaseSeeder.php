<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            
            // Data master referensi (tidak saling bergantung satu sama lain)
            KategoriSeeder::class,
            SatuanSeeder::class,
            LokasiSeeder::class,
            KondisiSeeder::class,
            PemasokSeeder::class,
 
            // Data barang (bergantung pada kategori & satuan)
            // ItemSeeder::class,
 
            // // Data transaksi (bergantung pada items, pemasok, lokasi, kondisi)
            // BarangMasukSeeder::class,
            // BarangKeluarSeeder::class,
 
            // Notifikasi (bergantung pada items)
            // NotificationSeeder::class,
        ]);
    }
    
}
