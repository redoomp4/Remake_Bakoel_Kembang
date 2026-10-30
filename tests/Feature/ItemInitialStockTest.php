<?php

namespace Tests\Feature;

use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ItemInitialStockTest extends TestCase
{
    use RefreshDatabase;

    private function dataMaster(): array
    {
        $user = User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-stok-awal',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $kategori = Kategori::create([
            'user_id' => $user->id,
            'kategori' => 'Bunga Potong',
        ]);

        $satuan = Satuan::create([
            'user_id' => $user->id,
            'nama_satuan' => 'Tangkai',
        ]);

        return [$user, $kategori, $satuan];
    }

    public function test_create_page_shows_initial_stock_toggle_and_default_supplier(): void
    {
        [$user] = $this->dataMaster();

        $response = $this->actingAs($user)->get(route('item.create'));

        $response->assertOk();
        $response->assertSee('Masukkan Sebagai Stok Awal', false);
        $response->assertSee('Pemasok Umum (Default)', false);
        $response->assertSee('Tambah Item Baru', false);
        $response->assertSee('Pilih atau ketik kategori baru...', false);
        $response->assertSee('Pilih atau ketik satuan baru...', false);
        $response->assertSee('Pilih atau ketik lokasi baru...', false);
        $response->assertSee('Pilih atau ketik kondisi baru...', false);
    }

    public function test_item_can_be_saved_with_initial_stock_in_one_transaction(): void
    {
        [$user, $kategori, $satuan] = $this->dataMaster();

        $response = $this->actingAs($user)->post(route('item.store'), [
            'nama_barang' => 'Mawar Stok Awal',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 3,
            'buat_stok_awal' => 1,
            'stok_awal_jumlah' => 12,
            'stok_awal_harga' => 7500,
            'stok_awal_pemasok' => 'Pemasok Umum',
        ]);

        $response->assertRedirect(route('item.index'));
        $response->assertSessionHas('success');

        $item = Item::where('user_id', $user->id)
            ->where('nama_barang', 'Mawar Stok Awal')
            ->firstOrFail();

        $this->assertNotNull($item->qr_code);

        $this->assertDatabaseHas('barang_masuks', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'jumlah' => 12,
            'harga_satuan' => 7500,
            'total_harga' => 90000,
        ]);

        $this->assertDatabaseHas('pemasoks', [
            'user_id' => $user->id,
            'nama_pemasok' => 'Pemasok Umum',
        ]);

        $this->assertDatabaseHas('lokasis', [
            'user_id' => $user->id,
            'nama_lokasi' => 'Gudang Utama',
        ]);

        $this->assertDatabaseHas('kondisis', [
            'user_id' => $user->id,
            'nama_kondisi' => 'Segar',
        ]);
    }

    public function test_item_can_create_category_unit_location_and_condition_on_the_fly(): void
    {
        [$user] = $this->dataMaster();

        $response = $this->actingAs($user)->post(route('item.store'), [
            'nama_barang' => 'Bunga Papan Duka',
            'id_kategori' => 'Bunga Papan',
            'id_satuan' => 'Ikat Besar',
            'stok_minimum' => 1,
            'buat_stok_awal' => 1,
            'stok_awal_jumlah' => 4,
            'stok_awal_harga' => 125000,
            'stok_awal_pemasok' => 'Pemasok Umum',
            'stok_awal_lokasi' => 'Toko Depan',
            'stok_awal_kondisi' => 'Setengah Mekar',
        ]);

        $response->assertRedirect(route('item.index'));

        $item = Item::where('user_id', $user->id)
            ->where('nama_barang', 'Bunga Papan Duka')
            ->firstOrFail();

        $this->assertSame('Bunga Papan', $item->kategori->kategori);
        $this->assertSame('Ikat Besar', $item->satuan->nama_satuan);

        $this->assertDatabaseHas('kategoris', [
            'user_id' => $user->id,
            'kategori' => 'Bunga Papan',
        ]);
        $this->assertDatabaseHas('satuans', [
            'user_id' => $user->id,
            'nama_satuan' => 'Ikat Besar',
        ]);
        $this->assertDatabaseHas('lokasis', [
            'user_id' => $user->id,
            'nama_lokasi' => 'Toko Depan',
        ]);
        $this->assertDatabaseHas('kondisis', [
            'user_id' => $user->id,
            'nama_kondisi' => 'Setengah Mekar',
        ]);

        $barangMasuk = BarangMasuk::where('item_id', $item->id)->firstOrFail();
        $this->assertSame('Toko Depan', $barangMasuk->lokasi->nama_lokasi);
        $this->assertSame('Setengah Mekar', $barangMasuk->kondisi->nama_kondisi);
    }

    public function test_item_without_initial_stock_does_not_create_barang_masuk(): void
    {
        [$user, $kategori, $satuan] = $this->dataMaster();

        $this->actingAs($user)->post(route('item.store'), [
            'nama_barang' => 'Melati Tanpa Stok',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 0,
        ])->assertRedirect(route('item.index'));

        $item = Item::where('user_id', $user->id)
            ->where('nama_barang', 'Melati Tanpa Stok')
            ->firstOrFail();

        $this->assertSame(0, BarangMasuk::where('item_id', $item->id)->count());
        $this->assertFalse(Schema::hasColumn('items', 'harga_dasar'));
    }

    public function test_initial_stock_requires_quantity_and_price(): void
    {
        [$user, $kategori, $satuan] = $this->dataMaster();

        $response = $this->actingAs($user)
            ->from(route('item.create'))
            ->post(route('item.store'), [
                'nama_barang' => 'Anggrek Tanpa Jumlah',
                'id_kategori' => $kategori->id,
                'id_satuan' => $satuan->id,
                'stok_minimum' => 0,
                'buat_stok_awal' => 1,
            ]);

        $response->assertRedirect(route('item.create'));
        $response->assertSessionHasErrors(['stok_awal_jumlah', 'stok_awal_harga']);

        $this->assertSame(0, Item::count());
        $this->assertSame(0, BarangMasuk::count());
    }
}
