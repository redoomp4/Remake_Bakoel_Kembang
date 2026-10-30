<?php

namespace Database\Seeders;

use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Pemasok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BarangMasukSeeder extends Seeder
{
    /**
     * Data template barang masuk dengan multi-batch & harga riil bervariasi.
     */
    public static array $masukTemplates = [
        // 1. Anggrek Bulan Putih (3 batch beda harga & tanggal)
        [
            'kode_barang' => 'BRG-ANG-001',
            'jumlah' => 20,
            'harga_satuan' => 75000,
            'days_ago' => 30,
            'time' => '08:30:00',
            'pemasok' => 'UD Anggrek Indah',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Kulakan Anggrek #AG-101',
        ],
        [
            'kode_barang' => 'BRG-ANG-001',
            'jumlah' => 15,
            'harga_satuan' => 82000,
            'days_ago' => 14,
            'time' => '09:15:00',
            'pemasok' => 'PT Flora Prima Indonesia',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Restok Anggrek #AG-108',
        ],
        [
            'kode_barang' => 'BRG-ANG-001',
            'jumlah' => 10,
            'harga_satuan' => 80000,
            'days_ago' => 3,
            'time' => '08:00:00',
            'pemasok' => 'UD Anggrek Indah',
            'lokasi' => 'Gudang Utama',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Masuk Batch 3 #AG-115',
        ],

        // 2. Anggrek Dendrobium Ungu (2 batch)
        [
            'kode_barang' => 'BRG-ANG-002',
            'jumlah' => 25,
            'harga_satuan' => 45000,
            'days_ago' => 26,
            'time' => '10:00:00',
            'pemasok' => 'UD Anggrek Indah',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Dendro Ungu #DEN-01',
        ],
        [
            'kode_barang' => 'BRG-ANG-002',
            'jumlah' => 20,
            'harga_satuan' => 48000,
            'days_ago' => 10,
            'time' => '09:00:00',
            'pemasok' => 'UD Anggrek Indah',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Dendro Ungu #DEN-02',
        ],

        // 3. Anggrek Vanda Biru Princess (1 batch bernilai tinggi)
        [
            'kode_barang' => 'BRG-ANG-003',
            'jumlah' => 12,
            'harga_satuan' => 120000,
            'days_ago' => 16,
            'time' => '11:00:00',
            'pemasok' => 'PT Flora Prima Indonesia',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Vanda Princess #VAN-01',
        ],

        // 4. Mawar Merah Sexy Red (3 batch)
        [
            'kode_barang' => 'BRG-MWR-001',
            'jumlah' => 100,
            'harga_satuan' => 4000,
            'days_ago' => 25,
            'time' => '07:30:00',
            'pemasok' => 'CV Kembang Sari Nusantara',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Mawar Merah #MWR-01',
        ],
        [
            'kode_barang' => 'BRG-MWR-001',
            'jumlah' => 80,
            'harga_satuan' => 4500,
            'days_ago' => 12,
            'time' => '07:45:00',
            'pemasok' => 'Kebun Bunga Berkah Tani',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Pasokan Panen #MWR-09',
        ],
        [
            'kode_barang' => 'BRG-MWR-001',
            'jumlah' => 50,
            'harga_satuan' => 4200,
            'days_ago' => 2,
            'time' => '08:15:00',
            'pemasok' => 'Toko Bunga Melati Jaya',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Suplai Mingguan #MWR-14',
        ],

        // 5. Mawar Putih Avalanche (2 batch)
        [
            'kode_barang' => 'BRG-MWR-002',
            'jumlah' => 60,
            'harga_satuan' => 5000,
            'days_ago' => 20,
            'time' => '08:00:00',
            'pemasok' => 'CV Kembang Sari Nusantara',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Mawar Putih #MWP-01',
        ],
        [
            'kode_barang' => 'BRG-MWR-002',
            'jumlah' => 50,
            'harga_satuan' => 5500,
            'days_ago' => 6,
            'time' => '08:30:00',
            'pemasok' => 'PT Flora Prima Indonesia',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Mawar Putih #MWP-02',
        ],

        // 6. Mawar Peach Juliet (2 batch)
        [
            'kode_barang' => 'BRG-MWR-003',
            'jumlah' => 40,
            'harga_satuan' => 8000,
            'days_ago' => 17,
            'time' => '09:00:00',
            'pemasok' => 'PT Flora Prima Indonesia',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Juliet Rose #JLT-01',
        ],
        [
            'kode_barang' => 'BRG-MWR-003',
            'jumlah' => 30,
            'harga_satuan' => 8500,
            'days_ago' => 3,
            'time' => '09:30:00',
            'pemasok' => 'PT Flora Prima Indonesia',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Juliet Rose #JLT-02',
        ],

        // 7. Krisan Spray Kuning Golden (2 batch)
        [
            'kode_barang' => 'BRG-KRS-001',
            'jumlah' => 40,
            'harga_satuan' => 12000,
            'days_ago' => 18,
            'time' => '08:45:00',
            'pemasok' => 'Kebun Bunga Berkah Tani',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Krisan Lembang #KRS-01',
        ],
        [
            'kode_barang' => 'BRG-KRS-001',
            'jumlah' => 30,
            'harga_satuan' => 13000,
            'days_ago' => 5,
            'time' => '08:30:00',
            'pemasok' => 'CV Kembang Sari Nusantara',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Krisan #KRS-02',
        ],

        // 8. Lili Casablanca Putih (2 batch)
        [
            'kode_barang' => 'BRG-LIL-001',
            'jumlah' => 25,
            'harga_satuan' => 22000,
            'days_ago' => 22,
            'time' => '09:00:00',
            'pemasok' => 'PT Flora Prima Indonesia',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Lili Import #LIL-01',
        ],
        [
            'kode_barang' => 'BRG-LIL-001',
            'jumlah' => 20,
            'harga_satuan' => 24000,
            'days_ago' => 7,
            'time' => '09:15:00',
            'pemasok' => 'PT Flora Prima Indonesia',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Lili Batch 2 #LIL-02',
        ],

        // 9. Baby's Breath (2 batch)
        [
            'kode_barang' => 'BRG-BBY-001',
            'jumlah' => 30,
            'harga_satuan' => 35000,
            'days_ago' => 24,
            'time' => '08:00:00',
            'pemasok' => 'Toko Bunga Melati Jaya',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Baby Breath #BB-01',
        ],
        [
            'kode_barang' => 'BRG-BBY-001',
            'jumlah' => 25,
            'harga_satuan' => 38000,
            'days_ago' => 8,
            'time' => '08:30:00',
            'pemasok' => 'Toko Bunga Melati Jaya',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Restok Baby Breath #BB-02',
        ],

        // 10. Buket Bunga Wisuda (2 batch)
        [
            'kode_barang' => 'BRG-BKT-001',
            'jumlah' => 15,
            'harga_satuan' => 95000,
            'days_ago' => 15,
            'time' => '13:00:00',
            'pemasok' => 'Toko Bunga Melati Jaya',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Buket Ready #BKT-01',
        ],
        [
            'kode_barang' => 'BRG-BKT-001',
            'jumlah' => 10,
            'harga_satuan' => 100000,
            'days_ago' => 4,
            'time' => '13:30:00',
            'pemasok' => 'Toko Bunga Melati Jaya',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'catatan' => 'Nota Buket Ready #BKT-02',
        ],

        // 11. Pupuk Khusus Anggrek
        [
            'kode_barang' => 'BRG-PUP-001',
            'jumlah' => 30,
            'harga_satuan' => 25000,
            'days_ago' => 28,
            'time' => '10:00:00',
            'pemasok' => 'UD Anggrek Indah',
            'lokasi' => 'Gudang Media Tanam',
            'kondisi' => 'Baik',
            'catatan' => 'Nota Pupuk Anggrek #PUP-01',
        ],

        // 12. Pita Satin Hijau Emerald
        [
            'kode_barang' => 'BRG-AKS-001',
            'jumlah' => 100,
            'harga_satuan' => 3000,
            'days_ago' => 30,
            'time' => '11:00:00',
            'pemasok' => 'CV Kembang Sari Nusantara',
            'lokasi' => 'Rak Aksesoris',
            'kondisi' => 'Baik',
            'catatan' => 'Nota Pita Roll #PITA-01',
        ],
    ];

    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command?->warn('Tidak ada user ditemukan. Jalankan UserSeeder & ItemSeeder terlebih dahulu.');

            return;
        }

        $baseDate = Carbon::now()->startOfDay();

        foreach ($users as $user) {
            // Hapus data masuk seeder sebelumnya milik user agar rapi dan idempotent
            BarangMasuk::where('user_id', $user->id)
                ->where('catatan', 'like', 'Nota %')
                ->delete();

            $items = Item::where('user_id', $user->id)->get()->keyBy('kode_barang');
            $lokasis = Lokasi::where('user_id', $user->id)->get()->keyBy('nama_lokasi');
            $kondisis = Kondisi::where('user_id', $user->id)->get()->keyBy('nama_kondisi');
            $pemasoks = Pemasok::where('user_id', $user->id)->get()->keyBy('nama_pemasok');

            $defaultLokasi = $lokasis->first() ?? Lokasi::firstOrCreate(
                ['user_id' => $user->id, 'nama_lokasi' => 'Gudang Utama'],
                ['deskripsi' => 'Penyimpanan utama.']
            );
            $defaultKondisi = $kondisis->first() ?? Kondisi::firstOrCreate(
                ['user_id' => $user->id, 'nama_kondisi' => 'Segar'],
                ['deskripsi' => 'Kondisi segar.']
            );
            $defaultPemasok = $pemasoks->first() ?? Pemasok::firstOrCreate(
                ['user_id' => $user->id, 'nama_pemasok' => 'Pemasok Umum'],
                ['jenis' => 'Pemasok Lokal']
            );

            foreach (self::$masukTemplates as $template) {
                $item = $items->get($template['kode_barang']);
                if (! $item) {
                    continue;
                }

                $lokasi = $lokasis->get($template['lokasi']) ?? $defaultLokasi;
                $kondisi = $kondisis->get($template['kondisi']) ?? $defaultKondisi;
                $pemasok = $pemasoks->get($template['pemasok']) ?? $defaultPemasok;

                $tgl = $baseDate->copy()->subDays($template['days_ago']);
                [$h, $m, $s] = explode(':', $template['time']);
                $tgl->setTime((int) $h, (int) $m, (int) $s);

                BarangMasuk::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'jumlah' => $template['jumlah'],
                    'harga_satuan' => $template['harga_satuan'],
                    'total_harga' => $template['jumlah'] * $template['harga_satuan'],
                    'tanggal_masuk' => $tgl,
                    'id_pemasok' => $pemasok->id,
                    'id_lokasi' => $lokasi->id,
                    'id_kondisi' => $kondisi->id,
                    'catatan' => $template['catatan'],
                ]);
            }
        }

        $this->command?->info('BarangMasukSeeder: Data batch barang masuk berhasil dibuat.');
    }
}
