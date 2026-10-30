<?php

namespace Database\Seeders;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Pemasok;
use App\Models\Satuan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DynamicReportSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::firstOrCreate(
                ['username' => 'demo-laporan'],
                [
                    'name' => 'Ibu Laporan Demo',
                    'email' => 'demo-laporan@example.test',
                    'password' => Hash::make('password123'),
                    'role' => 'penjual',
                    'status' => 'aktif',
                    'position' => 'penjual',
                    'phone' => '081200000001',
                    'photo' => 'default.jpg',
                ]
            );
            $user->forceFill([
                'email_verified_at' => now(),
                'is_active' => true,
            ])->save();

            BarangKeluar::where('user_id', $user->id)->delete();
            BarangMasuk::where('user_id', $user->id)->delete();
            Item::where('user_id', $user->id)->delete();

            $kategoriPot = Kategori::firstOrCreate(
                ['user_id' => $user->id, 'kategori' => 'Bunga Hias Pot'],
                ['deskripsi' => 'Tanaman bunga hidup dalam pot untuk dekorasi.']
            );
            $kategoriPotong = Kategori::firstOrCreate(
                ['user_id' => $user->id, 'kategori' => 'Bunga Potong'],
                ['deskripsi' => 'Bunga segar yang dijual per tangkai.']
            );
            $satuanPot = Satuan::firstOrCreate(['user_id' => $user->id, 'nama_satuan' => 'Pot']);
            $satuanTangkai = Satuan::firstOrCreate(['user_id' => $user->id, 'nama_satuan' => 'Tangkai']);
            $gudang = Lokasi::firstOrCreate(
                ['user_id' => $user->id, 'nama_lokasi' => 'Gudang Utama'],
                ['deskripsi' => 'Penyimpanan utama bunga.']
            );
            $rak = Lokasi::firstOrCreate(
                ['user_id' => $user->id, 'nama_lokasi' => 'Rak Pendingin'],
                ['deskripsi' => 'Rak pendingin untuk menjaga kesegaran bunga.']
            );
            $kondisiSegar = Kondisi::firstOrCreate(
                ['user_id' => $user->id, 'nama_kondisi' => 'Segar'],
                ['deskripsi' => 'Bunga segar dan layak jual.']
            );
            $pakBudi = Pemasok::firstOrCreate(
                ['user_id' => $user->id, 'nama_pemasok' => 'Pak Budi'],
                [
                    'jenis' => 'Petani Bunga',
                    'nama_pic' => 'Pak Budi',
                    'no_telepon' => '081200000002',
                ]
            );
            $pemasokUmum = Pemasok::firstOrCreate(
                ['user_id' => $user->id, 'nama_pemasok' => 'Pemasok Umum'],
                [
                    'jenis' => 'Pemasok Lokal',
                    'nama_pic' => 'Admin Pemasok Umum',
                    'no_telepon' => '081200000003',
                ]
            );

            $anggrek = Item::create([
                'user_id' => $user->id,
                'kode_barang' => 'BRG-ANG-001',
                'nama_barang' => 'Bunga Anggrek Bulan',
                'id_kategori' => $kategoriPot->id,
                'id_satuan' => $satuanPot->id,
                'stok_minimum' => 2,
                'deskripsi' => 'Anggrek bulan untuk dekorasi ruangan.',
            ]);
            $mawar = Item::create([
                'user_id' => $user->id,
                'kode_barang' => 'BRG-MWR-001',
                'nama_barang' => 'Mawar Merah Super',
                'id_kategori' => $kategoriPotong->id,
                'id_satuan' => $satuanTangkai->id,
                'stok_minimum' => 10,
                'deskripsi' => 'Mawar merah segar untuk buket.',
            ]);

            $base = Carbon::now()->startOfDay();

            $this->barangMasuk($user->id, $anggrek->id, 10, 15000, $base->copy()->subDays(20)->setTime(8, 0), $pakBudi->id, $gudang->id, $kondisiSegar->id, 'Nota #BM-001');
            $this->barangKeluar($user->id, $anggrek->id, 5, 30000, $base->copy()->subDays(18)->setTime(10, 0), $gudang->id, $kondisiSegar->id, 'Pembeli Anggrek 1');
            $this->barangMasuk($user->id, $anggrek->id, 10, 17000, $base->copy()->subDays(15)->setTime(8, 0), $pemasokUmum->id, $rak->id, $kondisiSegar->id, 'Nota #BM-008');
            $this->barangKeluar($user->id, $anggrek->id, 5, 25000, $base->copy()->subDays(12)->setTime(10, 0), $rak->id, $kondisiSegar->id, 'Pembeli Anggrek 2');

            $this->barangMasuk($user->id, $mawar->id, 50, 3000, $base->copy()->subDays(19)->setTime(8, 0), $pakBudi->id, $gudang->id, $kondisiSegar->id, 'Nota Mawar #1');
            $this->barangKeluar($user->id, $mawar->id, 30, 7000, $base->copy()->subDays(17)->setTime(10, 0), $gudang->id, $kondisiSegar->id, 'Pembeli Mawar 1');
            $this->barangMasuk($user->id, $mawar->id, 20, 3500, $base->copy()->subDays(14)->setTime(8, 0), $pemasokUmum->id, $rak->id, $kondisiSegar->id, 'Nota Mawar #2');
            $this->barangKeluar($user->id, $mawar->id, 10, 6000, $base->copy()->subDays(11)->setTime(10, 0), $rak->id, $kondisiSegar->id, 'Pembeli Mawar 2');

            $this->command?->info('DynamicReportSeeder: data demo multi-harga berhasil dibuat untuk user demo-laporan / password123.');
        });
    }

    private function barangMasuk(
        int $userId,
        int $itemId,
        int $jumlah,
        float $harga,
        Carbon $tanggal,
        int $pemasokId,
        int $lokasiId,
        int $kondisiId,
        string $catatan
    ): void {
        BarangMasuk::create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'jumlah' => $jumlah,
            'harga_satuan' => $harga,
            'total_harga' => $jumlah * $harga,
            'tanggal_masuk' => $tanggal,
            'id_pemasok' => $pemasokId,
            'id_lokasi' => $lokasiId,
            'id_kondisi' => $kondisiId,
            'catatan' => $catatan,
        ]);
    }

    private function barangKeluar(
        int $userId,
        int $itemId,
        int $jumlah,
        float $harga,
        Carbon $tanggal,
        int $lokasiId,
        int $kondisiId,
        string $penerima
    ): void {
        BarangKeluar::create([
            'user_id' => $userId,
            'item_id' => $itemId,
            'jumlah_keluar' => $jumlah,
            'harga_jual' => $harga,
            'total_harga_jual' => $jumlah * $harga,
            'tanggal_keluar' => $tanggal,
            'penerima' => $penerima,
            'lokasi_tujuan' => 'Toko',
            'jenis_transaksi' => 'Penjualan',
            'id_lokasi' => $lokasiId,
            'id_kondisi' => $kondisiId,
        ]);
    }
}
