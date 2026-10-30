<?php

namespace Tests\Feature;

use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Pemasok;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchableDropdownTest extends TestCase
{
    use RefreshDatabase;

    private User $penjual;

    private Item $item1;

    private Item $item2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->penjual = User::factory()->create([
            'name' => 'Ibu Toko Bunga',
            'username' => 'ibu-toko',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $kategori = Kategori::create(['user_id' => $this->penjual->id, 'kategori' => 'Bunga Potong']);
        $satuan = Satuan::create(['user_id' => $this->penjual->id, 'nama_satuan' => 'pcs']);
        $lokasi = Lokasi::create(['user_id' => $this->penjual->id, 'nama_lokasi' => 'Gudang Utama']);
        $kondisi = Kondisi::create(['user_id' => $this->penjual->id, 'nama_kondisi' => 'Segar']);
        $pemasok = Pemasok::create(['user_id' => $this->penjual->id, 'nama_pemasok' => 'Pemasok Utama']);

        $this->item1 = Item::create([
            'user_id' => $this->penjual->id,
            'kode_barang' => 'BRG-MWR-01',
            'public_token' => (string) Str::uuid(),
            'nama_barang' => 'Mawar Merah',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 2,
        ]);

        $this->item2 = Item::create([
            'user_id' => $this->penjual->id,
            'kode_barang' => 'BRG-MLT-01',
            'public_token' => (string) Str::uuid(),
            'nama_barang' => 'Melati Putih',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 2,
        ]);

        // Berikan stok 15 pcs ke Mawar Merah
        BarangMasuk::create([
            'user_id' => $this->penjual->id,
            'item_id' => $this->item1->id,
            'jumlah' => 15,
            'harga_satuan' => 5000,
            'total_harga' => 75000,
            'tanggal_masuk' => now(),
            'id_pemasok' => $pemasok->id,
            'id_lokasi' => $lokasi->id,
            'id_kondisi' => $kondisi->id,
        ]);
    }

    public function test_barang_masuk_create_renders_custom_searchable_dropdown_with_concise_stock_info(): void
    {
        $response = $this->actingAs($this->penjual)->get(route('barang-masuk.create'));

        $response->assertOk();

        // 1. Dropdown Item memiliki penanda data-searchable="true"
        $response->assertSee('data-searchable="true"', false);
        $response->assertSee('id="item_id"', false);

        // 2. Tampilkan info ringkas pada tiap pilihan: misal "Mawar Merah — Stok: 15 pcs"
        $response->assertSee('Mawar Merah — Stok: 15 pcs', false);
        $response->assertSee('Melati Putih — Stok: 0 pcs', false);

        // 3. Script custom search select di-include dan tidak menggunakan select2 tagging/inline creation
        $response->assertSee('makeSearchableSelect', false);
        $response->assertSee('min-height: 52px', false); // touch target >= 48px
        $response->assertSee('css-search-input', false); // kotak pencarian
    }

    public function test_barang_keluar_create_renders_searchable_dropdown(): void
    {
        $response = $this->actingAs($this->penjual)->get(route('barang-keluar.create'));

        $response->assertOk();

        // Dropdown barang keluar bertanda data-searchable="true"
        $response->assertSee('data-searchable="true"', false);
        $response->assertSee('id="kode_lokasi_kondisi"', false);

        // Memuat custom search dropdown script
        $response->assertSee('makeSearchableSelect', false);
    }
}
