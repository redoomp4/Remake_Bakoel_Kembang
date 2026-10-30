<?php

namespace Tests\Feature;

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Tests\TestCase;

class OmzetPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Item $item;

    private Lokasi $lokasi;

    private Kondisi $kondisi;

    private Pemasok $pemasok;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Owner Omzet',
            'username' => 'owner-omzet',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $kategori = Kategori::create(['user_id' => $this->user->id, 'kategori' => 'Bunga Potong']);
        $satuan = Satuan::create(['user_id' => $this->user->id, 'nama_satuan' => 'Tangkai']);
        $this->pemasok = Pemasok::create(['user_id' => $this->user->id, 'nama_pemasok' => 'Pemasok Utama']);
        $this->lokasi = Lokasi::create(['user_id' => $this->user->id, 'nama_lokasi' => 'Gudang Utama']);
        $this->kondisi = Kondisi::create(['user_id' => $this->user->id, 'nama_kondisi' => 'Segar']);

        $this->item = Item::create([
            'user_id' => $this->user->id,
            'kode_barang' => 'BRG-OMZ-01',
            'public_token' => (string) Str::uuid(),
            'nama_barang' => 'Mawar Merah Omzet',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 2,
        ]);
    }

    public function test_omzet_report_paginates_and_summary_cards_calculate_full_filtered_totals(): void
    {
        // 1 Batch masuk besar: 100 tangkai @ 10.000 (total modal = 1.000.000)
        BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'jumlah' => 100,
            'harga_satuan' => 10000,
            'total_harga' => 1000000,
            'tanggal_masuk' => '2026-02-01 08:00:00',
            'id_pemasok' => $this->pemasok->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
        ]);

        // Buat 20 transaksi barang keluar, masing-masing 2 tangkai @ 25.000
        // Per transaksi: Omzet = 50.000, Modal Beli Riil = 20.000, Laba Bersih = 30.000
        // Total dari 20 transaksi:
        // Total Omzet = 20 * 50.000 = 1.000.000
        // Total Modal = 20 * 20.000 = 400.000
        // Total Laba Bersih = 20 * 30.000 = 600.000
        $baseDate = Carbon::parse('2026-02-02 09:00:00');
        for ($i = 1; $i <= 20; $i++) {
            BarangKeluar::create([
                'user_id' => $this->user->id,
                'item_id' => $this->item->id,
                'id_lokasi' => $this->lokasi->id,
                'id_kondisi' => $this->kondisi->id,
                'jumlah_keluar' => 2,
                'harga_jual' => 25000,
                'total_harga_jual' => 50000,
                'tanggal_keluar' => $baseDate->copy()->addHours($i),
                'penerima' => "Pelanggan {$i}",
                'lokasi_tujuan' => 'Toko',
                'jenis_transaksi' => 'Penjualan',
            ]);
        }

        // Akses halaman 1 omzet
        $responsePage1 = $this->actingAs($this->user)->get(route('omzet.index'));

        $responsePage1->assertOk();

        // 1. Verifikasi Ringkasan Card Atas MENGHITUNG KESELURUHAN 20 transaksi, bukan hanya 15 data di page 1
        $responsePage1->assertSee('Rp 1.000.000'); // Total Omzet Keseluruhan
        $responsePage1->assertSee('Rp 600.000');   // Total Laba Bersih Keseluruhan
        $responsePage1->assertSee('Rp 400.000');   // Total Modal Riil Keseluruhan

        // 2. Verifikasi Data Terpaginasi: Ada 15 data di page 1
        $omzetData = $responsePage1->viewData('omzetData');
        $this->assertInstanceOf(LengthAwarePaginator::class, $omzetData);
        $this->assertSame(20, $omzetData->total());
        $this->assertSame(15, $omzetData->perPage());
        $this->assertSame(15, $omzetData->count());
        $this->assertSame(2, $omzetData->lastPage());

        // 3. Verifikasi Tautan Paginasi Ditampilkan di View
        $responsePage1->assertSee('Menampilkan data transaksi ke-1 sampai 15 dari total 20 transaksi', false);
        $responsePage1->assertSee('page=2', false);

        // Akses halaman 2 omzet
        $responsePage2 = $this->actingAs($this->user)->get(route('omzet.index', ['page' => 2]));

        $responsePage2->assertOk();
        // Halaman 2 tetap menampilkan Ringkasan Keseluruhan yang sama
        $responsePage2->assertSee('Rp 1.000.000');
        $responsePage2->assertSee('Rp 600.000');
        // Dan data di halaman 2 ada 5 item sisa
        $omzetDataPage2 = $responsePage2->viewData('omzetData');
        $this->assertSame(5, $omzetDataPage2->count());
        $responsePage2->assertSee('Menampilkan data transaksi ke-16 sampai 20 dari total 20 transaksi', false);
    }

    public function test_real_profit_calculation_strictly_matches_real_batch_price(): void
    {
        // Skenario Multi-Batch:
        // Masuk 1: 5 tangkai @ 10.000
        // Masuk 2: 5 tangkai @ 14.000
        // Dijual 7 tangkai @ 20.000
        // Perhitungan Riil FIFO (Bukan average):
        // 5 tangkai dari Masuk 1: (20.000 - 10.000) * 5 = 50.000
        // 2 tangkai dari Masuk 2: (20.000 - 14.000) * 2 = 12.000
        // Total Omzet: 7 * 20.000 = 140.000
        // Total Modal Riil: (5 * 10.000) + (2 * 14.000) = 50.000 + 28.000 = 78.000
        // Total Laba Bersih Riil: 140.000 - 78.000 = 62.000

        BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'jumlah' => 5,
            'harga_satuan' => 10000,
            'total_harga' => 50000,
            'tanggal_masuk' => '2026-03-01 08:00:00',
            'id_pemasok' => $this->pemasok->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'catatan' => 'Nota Batch A',
        ]);

        BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'jumlah' => 5,
            'harga_satuan' => 14000,
            'total_harga' => 70000,
            'tanggal_masuk' => '2026-03-02 08:00:00',
            'id_pemasok' => $this->pemasok->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'catatan' => 'Nota Batch B',
        ]);

        BarangKeluar::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah_keluar' => 7,
            'harga_jual' => 20000,
            'total_harga_jual' => 140000,
            'tanggal_keluar' => '2026-03-03 10:00:00',
            'penerima' => 'Pembeli Murni Riil',
            'lokasi_tujuan' => 'Toko',
            'jenis_transaksi' => 'Penjualan',
        ]);

        $response = $this->actingAs($this->user)->get(route('omzet.index'));

        $response->assertOk();
        $response->assertSee('Rp 140.000'); // Omzet
        $response->assertSee('Rp 78.000');  // Modal Riil (5*10k + 2*14k)
        $response->assertSee('Rp 62.000');  // Laba Bersih Riil (140k - 78k)
        $response->assertSee('Nota Batch A');
        $response->assertSee('Nota Batch B');
    }
}
