<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Master data item bunga & perlengkapan toko Bakoel Kembang.
     */
    public static array $itemsData = [
        [
            'kode_barang' => 'BRG-ANG-001',
            'nama_barang' => 'Bunga Anggrek Bulan Putih',
            'kategori' => 'Bunga Hias Pot',
            'satuan' => 'Pot',
            'stok_minimum' => 5,
            'deskripsi' => 'Anggrek bulan putih kelopak besar kualitas kontes, mekar sempurna dengan 8-10 kuntum.',
        ],
        [
            'kode_barang' => 'BRG-ANG-002',
            'nama_barang' => 'Anggrek Dendrobium Ungu',
            'kategori' => 'Bunga Hias Pot',
            'satuan' => 'Pot',
            'stok_minimum' => 5,
            'deskripsi' => 'Dendrobium ungu cerah, tanaman rimbun tahan panas, cocok untuk teras dan taman.',
        ],
        [
            'kode_barang' => 'BRG-ANG-003',
            'nama_barang' => 'Anggrek Vanda Biru Princess',
            'kategori' => 'Bunga Hias Pot',
            'satuan' => 'Pot',
            'stok_minimum' => 3,
            'deskripsi' => 'Vanda Blue Magic langka tanpa media tanah, akar gantung sehat dan subur.',
        ],
        [
            'kode_barang' => 'BRG-MWR-001',
            'nama_barang' => 'Mawar Merah Sexy Red Holland',
            'kategori' => 'Bunga Potong',
            'satuan' => 'Tangkai',
            'stok_minimum' => 20,
            'deskripsi' => 'Mawar potong merah beludru grade A batang panjang 60cm, awet 7-10 hari di vas.',
        ],
        [
            'kode_barang' => 'BRG-MWR-002',
            'nama_barang' => 'Mawar Putih Avalanche',
            'kategori' => 'Bunga Potong',
            'satuan' => 'Tangkai',
            'stok_minimum' => 15,
            'deskripsi' => 'Mawar potong putih bersih kelopak ganda untuk buket pernikahan dan dekorasi.',
        ],
        [
            'kode_barang' => 'BRG-MWR-003',
            'nama_barang' => 'Mawar Peach Juliet David Austin',
            'kategori' => 'Bunga Potong',
            'satuan' => 'Tangkai',
            'stok_minimum' => 10,
            'deskripsi' => 'Mawar premium jenis garden rose warna peach lembut beraroma teh manis.',
        ],
        [
            'kode_barang' => 'BRG-KRS-001',
            'nama_barang' => 'Krisan Spray Kuning Golden',
            'kategori' => 'Bunga Potong',
            'satuan' => 'Ikat',
            'stok_minimum' => 10,
            'deskripsi' => 'Krisan spray mekar serentak warna kuning terang per ikat (isi 10 tangkai).',
        ],
        [
            'kode_barang' => 'BRG-LIL-001',
            'nama_barang' => 'Lili Casablanca Putih Wangi',
            'kategori' => 'Bunga Potong',
            'satuan' => 'Tangkai',
            'stok_minimum' => 8,
            'deskripsi' => 'Oriental lily putih wangi semerbak dengan 3-5 kuntum bunga per tangkai.',
        ],
        [
            'kode_barang' => 'BRG-BBY-001',
            'nama_barang' => 'Baby\'s Breath Million Star',
            'kategori' => 'Bunga Potong',
            'satuan' => 'Ikat',
            'stok_minimum' => 10,
            'deskripsi' => 'Bunga pengisi (filler) putih lembut impor kualitas tinggi per ikat besar.',
        ],
        [
            'kode_barang' => 'BRG-BKT-001',
            'nama_barang' => 'Buket Bunga Wisuda Elegance',
            'kategori' => 'Rangkaian & Buket',
            'satuan' => 'Buket',
            'stok_minimum' => 4,
            'deskripsi' => 'Buket rangkaian mawar merah, baby\'s breath, dan pita satin bernuansa elegan.',
        ],
        [
            'kode_barang' => 'BRG-PUP-001',
            'nama_barang' => 'Pupuk Khusus Anggrek Bloom Booster',
            'kategori' => 'Pupuk & Media Tanam',
            'satuan' => 'Pot',
            'stok_minimum' => 5,
            'deskripsi' => 'Nutrisi cair organik perangsang tunas dan bunga lebat untuk semua jenis anggrek.',
        ],
        [
            'kode_barang' => 'BRG-AKS-001',
            'nama_barang' => 'Pita Satin Hijau Emerald 2 Inch',
            'kategori' => 'Aksesoris & Pita',
            'satuan' => 'Meter',
            'stok_minimum' => 25,
            'deskripsi' => 'Pita kain satin premium warna emerald green untuk pembungkus buket dan hampers.',
        ],
    ];

    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command?->warn('Tidak ada user ditemukan. Jalankan UserSeeder terlebih dahulu.');

            return;
        }

        foreach ($users as $user) {
            foreach (self::$itemsData as $data) {
                $kategori = Kategori::where('user_id', $user->id)
                    ->where('kategori', $data['kategori'])
                    ->first();

                if (! $kategori) {
                    $kategori = Kategori::firstOrCreate(
                        ['user_id' => $user->id, 'kategori' => $data['kategori']],
                        ['deskripsi' => 'Kategori '.$data['kategori']]
                    );
                }

                $satuan = Satuan::where('user_id', $user->id)
                    ->where('nama_satuan', $data['satuan'])
                    ->first();

                if (! $satuan) {
                    $satuan = Satuan::firstOrCreate(
                        ['user_id' => $user->id, 'nama_satuan' => $data['satuan']]
                    );
                }

                Item::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'kode_barang' => $data['kode_barang'],
                    ],
                    [
                        'nama_barang' => $data['nama_barang'],
                        'public_token' => (string) Str::uuid(),
                        'id_kategori' => $kategori->id,
                        'id_satuan' => $satuan->id,
                        'stok_minimum' => $data['stok_minimum'],
                        'deskripsi' => $data['deskripsi'],
                    ]
                );
            }
        }

        $this->command?->info('ItemSeeder: Master data item berhasil dibuat untuk seluruh user.');
    }
}
