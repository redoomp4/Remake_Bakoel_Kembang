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
use App\Services\BatchInventoryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportBatchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Item $item;

    private BarangMasuk $bm1;

    private BarangMasuk $bm2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Pemilik Laporan',
            'username' => 'pemilik-laporan',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $kategori = Kategori::create(['user_id' => $this->user->id, 'kategori' => 'Bunga Potong']);
        $satuan = Satuan::create(['user_id' => $this->user->id, 'nama_satuan' => 'Tangkai']);
        $pemasok = Pemasok::create(['user_id' => $this->user->id, 'nama_pemasok' => 'Pemasok Uji']);
        $lokasi = Lokasi::create(['user_id' => $this->user->id, 'nama_lokasi' => 'Gudang Uji']);
        $kondisi = Kondisi::create(['user_id' => $this->user->id, 'nama_kondisi' => 'Segar']);

        $this->item = Item::create([
            'user_id' => $this->user->id,
            'kode_barang' => 'BRG-001',
            'public_token' => (string) Str::uuid(),
            'nama_barang' => 'Mawar Uji',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 0,
        ]);

        $this->bm1 = BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'jumlah' => 10,
            'harga_satuan' => 15000,
            'total_harga' => 150000,
            'tanggal_masuk' => '2026-01-01 08:00:00',
            'id_pemasok' => $pemasok->id,
            'id_lokasi' => $lokasi->id,
            'id_kondisi' => $kondisi->id,
        ]);

        $this->bm2 = BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'jumlah' => 10,
            'harga_satuan' => 17000,
            'total_harga' => 170000,
            'tanggal_masuk' => '2026-01-02 08:00:00',
            'id_pemasok' => $pemasok->id,
            'id_lokasi' => $lokasi->id,
            'id_kondisi' => $kondisi->id,
        ]);

        BarangKeluar::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'id_lokasi' => $lokasi->id,
            'id_kondisi' => $kondisi->id,
            'jumlah_keluar' => 15,
            'harga_jual' => 25000,
            'total_harga_jual' => 375000,
            'tanggal_keluar' => '2026-01-03 10:00:00',
            'penerima' => 'Pembeli Uji',
            'lokasi_tujuan' => 'Toko',
            'jenis_transaksi' => 'Penjualan',
        ]);
    }

    public function test_fifo_uses_real_batch_cost_across_multiple_batches(): void
    {
        $code1 = 'BM-'.str_pad((string) $this->bm1->id, 4, '0', STR_PAD_LEFT);
        $code2 = 'BM-'.str_pad((string) $this->bm2->id, 4, '0', STR_PAD_LEFT);

        $snapshot = app(BatchInventoryService::class)
            ->snapshot($this->user->id, Carbon::parse('2026-01-31'));

        $this->assertCount(1, $snapshot['outgoings']);
        $outgoing = $snapshot['outgoings']->first();

        $this->assertSame(15, $outgoing['jumlah_keluar']);
        $this->assertSame(375000.0, (float) $outgoing['omzet']);
        $this->assertSame(235000.0, (float) $outgoing['modal_riil']);
        $this->assertSame(140000.0, (float) $outgoing['laba_riil']);
        $this->assertCount(2, $outgoing['allocations']);
        $this->assertStringContainsString($code1, $outgoing['batch_refs']);
        $this->assertStringContainsString($code2, $outgoing['batch_refs']);

        $first = $snapshot['batches']->firstWhere('batch_code', $code1);
        $second = $snapshot['batches']->firstWhere('batch_code', $code2);

        $this->assertSame(0, $first['sisa_stok']);
        $this->assertSame(5, $second['sisa_stok']);
        $this->assertSame(85000.0, (float) $second['nilai_aset']);

        $this->assertSame(85000.0, app(BatchInventoryService::class)->stockValue($this->user->id, Carbon::parse('2026-01-31')));
    }

    public function test_report_pages_render_batch_data_without_harga_dasar(): void
    {
        $code1 = 'BM-'.str_pad((string) $this->bm1->id, 4, '0', STR_PAD_LEFT);
        $code2 = 'BM-'.str_pad((string) $this->bm2->id, 4, '0', STR_PAD_LEFT);

        $this->assertFalse(Schema::hasColumn('items', 'harga_dasar'));

        $this->actingAs($this->user)->get(route('laporan'))
            ->assertOk()
            ->assertSee('TOTAL NILAI ASET RIIL')
            ->assertSee($code2)
            ->assertDontSee('Harga Dasar');

        $this->actingAs($this->user)->get(route('laporan.arus'))
            ->assertOk()
            ->assertSee('TOTAL OMZET RIIL')
            ->assertSee('TOTAL MODAL RIIL')
            ->assertSee('TOTAL LABA BERSIH RIIL')
            ->assertSee($code1)
            ->assertDontSee('Harga Dasar');

        $this->actingAs($this->user)->get(route('omzet.index'))
            ->assertOk()
            ->assertSee('TOTAL MODAL RIIL')
            ->assertSee('TOTAL LABA BERSIH RIIL')
            ->assertSee($code1);

        $this->actingAs($this->user)->get(route('laporan.aset'))
            ->assertOk()
            ->assertSee('TOTAL NILAI ASET RIIL')
            ->assertSee($code2);

        $this->actingAs($this->user)->get(route('laporan.stok.viewer'))
            ->assertOk()
            ->assertSee('Mawar Uji')
            ->assertSee('5');

        $this->actingAs($this->user)->get(route('laporan.stok.admin'))
            ->assertOk()
            ->assertSee('Mawar Uji')
            ->assertSee('pemilik-laporan')
            ->assertSee('5');

        $sale = BarangKeluar::firstOrFail();

        $this->actingAs($this->user)->get(route('barang-keluar.detail', $sale))
            ->assertOk()
            ->assertSee('Modal Riil (FIFO)')
            ->assertSee('Laba: Rp 140.000')
            ->assertSee($code1)
            ->assertSee($code2);
    }
}
