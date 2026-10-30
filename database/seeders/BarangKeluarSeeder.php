<?php

namespace Database\Seeders;

use App\Models\BarangKeluar;
use App\Models\Item;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BarangKeluarSeeder extends Seeder
{
    /**
     * Data template barang keluar (penjualan) terhubung FIFO dengan barang masuk.
     */
    public static array $keluarTemplates = [
        // 1. Anggrek Bulan Putih
        // Batch 1 masuk day -30 (qty 20 @ 75k, Etalase Depan, Segar)
        // Batch 2 masuk day -14 (qty 15 @ 82k, Etalase Depan, Segar)
        [
            'kode_barang' => 'BRG-ANG-001',
            'jumlah_keluar' => 10,
            'harga_jual' => 140000,
            'days_ago' => 22,
            'time' => '11:00:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Hotel Grand Senyiur',
            'lokasi_tujuan' => 'Lobby Hotel Senyiur',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Dekorasi anggrek meja resepsionis',
        ],
        // Multi-batch consumption: ambil 10 sisa Batch 1 (@ 75k) + 2 dari Batch 2 (@ 82k)
        [
            'kode_barang' => 'BRG-ANG-001',
            'jumlah_keluar' => 12,
            'harga_jual' => 150000,
            'days_ago' => 10,
            'time' => '14:30:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Ibu Rina Wijaya',
            'lokasi_tujuan' => 'Perumahan Bukit Damai',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Pesanan hiasan teras rumah',
        ],
        [
            'kode_barang' => 'BRG-ANG-001',
            'jumlah_keluar' => 5,
            'harga_jual' => 150000,
            'days_ago' => 1,
            'time' => '16:00:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Dr. Budi Santoso',
            'lokasi_tujuan' => 'Klinik Permata Medika',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Kado bunga pembukaan klinik',
        ],

        // 2. Anggrek Dendrobium Ungu
        // Batch 1 masuk day -26 (qty 25 @ 45k, Etalase Depan, Segar)
        [
            'kode_barang' => 'BRG-ANG-002',
            'jumlah_keluar' => 15,
            'harga_jual' => 85000,
            'days_ago' => 18,
            'time' => '10:30:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Kantor Dinas Pertanian',
            'lokasi_tujuan' => 'Ruang Rapat Utama',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Penghijauan ruangan dinas',
        ],
        [
            'kode_barang' => 'BRG-ANG-002',
            'jumlah_keluar' => 10,
            'harga_jual' => 90000,
            'days_ago' => 6,
            'time' => '15:00:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Bapak Surya Kencana',
            'lokasi_tujuan' => 'Batakan',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Koleksi taman anggrek pribadi',
        ],

        // 3. Anggrek Vanda Biru Princess
        // Batch 1 masuk day -16 (qty 12 @ 120k, Etalase Depan, Segar)
        [
            'kode_barang' => 'BRG-ANG-003',
            'jumlah_keluar' => 6,
            'harga_jual' => 220000,
            'days_ago' => 8,
            'time' => '13:00:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Kolektor Bunga Ibu Diana',
            'lokasi_tujuan' => 'Klandasan',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Pesanan anggrek vanda langka',
        ],

        // 4. Mawar Merah Sexy Red
        // Batch 1 masuk day -25 (qty 100 @ 4k, Rak Pendingin, Segar)
        // Batch 2 masuk day -12 (qty 80 @ 4.5k, Rak Pendingin, Segar)
        [
            'kode_barang' => 'BRG-MWR-001',
            'jumlah_keluar' => 60,
            'harga_jual' => 9000,
            'days_ago' => 20,
            'time' => '09:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Wedding Organizer Senja',
            'lokasi_tujuan' => 'Ballroom Hotel Aston',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Bunga dekorasi pelaminan wedding',
        ],
        // Multi-batch: 40 dari Batch 1 (@ 4k) + 10 dari Batch 2 (@ 4.5k)
        [
            'kode_barang' => 'BRG-MWR-001',
            'jumlah_keluar' => 50,
            'harga_jual' => 10000,
            'days_ago' => 11,
            'time' => '11:15:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Florist Sakura Pasar Segar',
            'lokasi_tujuan' => 'Kios Bunga No. 4',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Grosir tangkai mawar buket wisuda',
        ],
        [
            'kode_barang' => 'BRG-MWR-001',
            'jumlah_keluar' => 40,
            'harga_jual' => 10000,
            'days_ago' => 4,
            'time' => '15:30:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Pelanggan Romantis',
            'lokasi_tujuan' => 'Ambil di Toko',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Pesanan buket romantis spesial',
        ],

        // 5. Mawar Putih Avalanche
        // Batch 1 masuk day -20 (qty 60 @ 5k, Rak Pendingin, Segar)
        [
            'kode_barang' => 'BRG-MWR-002',
            'jumlah_keluar' => 35,
            'harga_jual' => 12000,
            'days_ago' => 15,
            'time' => '10:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Gereja Maria Assumpta',
            'lokasi_tujuan' => 'Altar Utama Gereja',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Dekorasi kebaktian Minggu',
        ],
        [
            'kode_barang' => 'BRG-MWR-002',
            'jumlah_keluar' => 25,
            'harga_jual' => 13000,
            'days_ago' => 3,
            'time' => '13:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Wedding Studio Foto',
            'lokasi_tujuan' => 'Studio 1 Balikpapan',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Properti photoshoot pengantin',
        ],

        // 6. Mawar Peach Juliet
        // Batch 1 masuk day -17 (qty 40 @ 8k, Rak Pendingin, Segar)
        [
            'kode_barang' => 'BRG-MWR-003',
            'jumlah_keluar' => 25,
            'harga_jual' => 18000,
            'days_ago' => 10,
            'time' => '14:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Rustic Wedding Planner',
            'lokasi_tujuan' => 'Pantai Manggar Villa',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Rangkaian bohemian wedding outdoor',
        ],
        // Multi-batch: 15 sisa Batch 1 (@ 8k) + 5 dari Batch 2 (@ 8.5k)
        [
            'kode_barang' => 'BRG-MWR-003',
            'jumlah_keluar' => 20,
            'harga_jual' => 20000,
            'days_ago' => 1,
            'time' => '16:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Ibu Clara',
            'lokasi_tujuan' => 'Perumahan BDS Balikpapan',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Buket anniversary pernikahan emas',
        ],

        // 7. Krisan Spray Kuning Golden
        // Batch 1 masuk day -18 (qty 40 @ 12k, Rak Pendingin, Segar)
        [
            'kode_barang' => 'BRG-KRS-001',
            'jumlah_keluar' => 25,
            'harga_jual' => 22000,
            'days_ago' => 12,
            'time' => '09:30:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Papan Bunga Bahagia',
            'lokasi_tujuan' => 'Workshop Karangan Bunga',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Bahan karangan bunga ucapan',
        ],
        [
            'kode_barang' => 'BRG-KRS-001',
            'jumlah_keluar' => 15,
            'harga_jual' => 24000,
            'days_ago' => 2,
            'time' => '11:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Ibu Kartika',
            'lokasi_tujuan' => 'Ambil di Toko',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Dekorasi arisan keluarga',
        ],

        // 8. Lili Casablanca Putih
        // Batch 1 masuk day -22 (qty 25 @ 22k, Rak Pendingin, Segar)
        [
            'kode_barang' => 'BRG-LIL-001',
            'jumlah_keluar' => 15,
            'harga_jual' => 45000,
            'days_ago' => 16,
            'time' => '10:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Boutique Hotel Blue Sky',
            'lokasi_tujuan' => 'Suite Room Presiden',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Rangkaian bunga meja tamu VIP kenegaraan',
        ],
        [
            'kode_barang' => 'BRG-LIL-001',
            'jumlah_keluar' => 10,
            'harga_jual' => 48000,
            'days_ago' => 4,
            'time' => '14:30:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Ibu Veronica',
            'lokasi_tujuan' => 'Balikpapan Regency',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Hiasan meja makan wangi',
        ],

        // 9. Baby's Breath Million Star
        // Batch 1 masuk day -24 (qty 30 @ 35k, Rak Pendingin, Segar)
        [
            'kode_barang' => 'BRG-BBY-001',
            'jumlah_keluar' => 18,
            'harga_jual' => 65000,
            'days_ago' => 18,
            'time' => '11:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Kafe Kopi Kembang',
            'lokasi_tujuan' => 'Area Meja Barista',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Dekorasi vas mungil meja kafe',
        ],
        // Multi-batch: 12 sisa Batch 1 (@ 35k) + 3 dari Batch 2 (@ 38k)
        [
            'kode_barang' => 'BRG-BBY-001',
            'jumlah_keluar' => 15,
            'harga_jual' => 70000,
            'days_ago' => 5,
            'time' => '15:00:00',
            'lokasi' => 'Rak Pendingin',
            'kondisi' => 'Segar',
            'penerima' => 'Florist Cantika',
            'lokasi_tujuan' => 'Jl. MT Haryono Balikpapan',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Campuran buket mawar wisuda',
        ],

        // 10. Buket Bunga Wisuda
        // Batch 1 masuk day -15 (qty 15 @ 95k, Etalase Depan, Segar)
        [
            'kode_barang' => 'BRG-BKT-001',
            'jumlah_keluar' => 10,
            'harga_jual' => 175000,
            'days_ago' => 10,
            'time' => '10:00:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Keluarga Wisudawan ITK',
            'lokasi_tujuan' => 'Gedung BSCC Dome',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Pesanan buket wisuda kelulusan',
        ],
        [
            'kode_barang' => 'BRG-BKT-001',
            'jumlah_keluar' => 5,
            'harga_jual' => 185000,
            'days_ago' => 2,
            'time' => '13:00:00',
            'lokasi' => 'Etalase Depan',
            'kondisi' => 'Segar',
            'penerima' => 'Pelanggan Walk-in Toko',
            'lokasi_tujuan' => 'Toko Bakoel Kembang',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Beli langsung di etalase toko',
        ],

        // 11. Pupuk Khusus Anggrek
        // Batch 1 masuk day -28 (qty 30 @ 25k, Gudang Media Tanam, Baik)
        [
            'kode_barang' => 'BRG-PUP-001',
            'jumlah_keluar' => 12,
            'harga_jual' => 45000,
            'days_ago' => 14,
            'time' => '14:00:00',
            'lokasi' => 'Gudang Media Tanam',
            'kondisi' => 'Baik',
            'penerima' => 'Komunitas Pecinta Anggrek Bpn',
            'lokasi_tujuan' => 'Greenhouse Sepinggan',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Pembelian pupuk organik kolektif',
        ],

        // 12. Pita Satin Hijau Emerald
        // Batch 1 masuk day -30 (qty 100 @ 3k, Rak Aksesoris, Baik)
        [
            'kode_barang' => 'BRG-AKS-001',
            'jumlah_keluar' => 40,
            'harga_jual' => 6000,
            'days_ago' => 15,
            'time' => '16:00:00',
            'lokasi' => 'Rak Aksesoris',
            'kondisi' => 'Baik',
            'penerima' => 'Hampers Lebaran Idaman',
            'lokasi_tujuan' => 'Balikpapan Baru',
            'jenis_transaksi' => 'Penjualan',
            'catatan' => 'Bahan pita hiasan kemasan hampers',
        ],
    ];

    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command?->warn('Tidak ada user ditemukan. Jalankan UserSeeder & BarangMasukSeeder terlebih dahulu.');

            return;
        }

        $baseDate = Carbon::now()->startOfDay();

        foreach ($users as $user) {
            // Hapus data keluar seeder sebelumnya milik user agar rapi dan idempotent
            BarangKeluar::where('user_id', $user->id)
                ->where('jenis_transaksi', 'Penjualan')
                ->delete();

            $items = Item::where('user_id', $user->id)->get()->keyBy('kode_barang');
            $lokasis = Lokasi::where('user_id', $user->id)->get()->keyBy('nama_lokasi');
            $kondisis = Kondisi::where('user_id', $user->id)->get()->keyBy('nama_kondisi');

            $defaultLokasi = $lokasis->first();
            $defaultKondisi = $kondisis->first();

            foreach (self::$keluarTemplates as $template) {
                $item = $items->get($template['kode_barang']);
                if (! $item) {
                    continue;
                }

                $lokasi = $lokasis->get($template['lokasi']) ?? $defaultLokasi;
                $kondisi = $kondisis->get($template['kondisi']) ?? $defaultKondisi;

                $tgl = $baseDate->copy()->subDays($template['days_ago']);
                [$h, $m, $s] = explode(':', $template['time']);
                $tgl->setTime((int) $h, (int) $m, (int) $s);

                BarangKeluar::create([
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'id_lokasi' => $lokasi->id,
                    'id_kondisi' => $kondisi->id,
                    'jumlah_keluar' => $template['jumlah_keluar'],
                    'harga_jual' => $template['harga_jual'],
                    'total_harga_jual' => $template['jumlah_keluar'] * $template['harga_jual'],
                    'tanggal_keluar' => $tgl,
                    'penerima' => $template['penerima'],
                    'lokasi_tujuan' => $template['lokasi_tujuan'],
                    'jenis_transaksi' => $template['jenis_transaksi'],
                    'catatan' => $template['catatan'],
                ]);
            }
        }

        $this->command?->info('BarangKeluarSeeder: Data transaksi barang keluar berhasil dibuat.');
    }
}
