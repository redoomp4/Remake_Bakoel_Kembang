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
use Tests\TestCase;

class InventoriIndexTest extends TestCase
{
    use RefreshDatabase;

    private function penjual(): User
    {
        return User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-inventori',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);
    }

    public function test_item_page_has_cross_navigation_to_barang_masuk(): void
    {
        $response = $this->actingAs($this->penjual())->get(route('item.index'));

        $response->assertOk();
        $response->assertSee('Daftar Item', false);
        $response->assertSee('Barang Masuk', false);
        $response->assertSee('Tambah Item Baru', false);
        $response->assertSee(route('barang-masuk.index'), false);
        $response->assertSee(route('barang-masuk.create'), false);
    }

    public function test_item_and_barang_masuk_pages_show_transaction_data(): void
    {
        $user = $this->penjual();

        $kategori = Kategori::create([
            'user_id' => $user->id,
            'kategori' => 'Bunga Potong',
        ]);

        $satuan = Satuan::create([
            'user_id' => $user->id,
            'nama_satuan' => 'Tangkai',
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Mawar Merah',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 2,
        ]);

        $pemasok = Pemasok::create([
            'user_id' => $user->id,
            'nama_pemasok' => 'Toko Bunga Melati',
        ]);

        $lokasi = Lokasi::create([
            'user_id' => $user->id,
            'nama_lokasi' => 'Gudang Utama',
        ]);

        $kondisi = Kondisi::create([
            'user_id' => $user->id,
            'nama_kondisi' => 'Segar',
        ]);

        BarangMasuk::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'id_pemasok' => $pemasok->id,
            'id_lokasi' => $lokasi->id,
            'id_kondisi' => $kondisi->id,
            'jumlah' => 10,
            'harga_satuan' => 10000,
            'total_harga' => 100000,
            'tanggal_masuk' => now(),
        ]);

        $this->actingAs($user)->get(route('item.index'))
            ->assertOk()
            ->assertSee('Mawar Merah', false)
            ->assertSee('Stok Aman', false)
            ->assertSee('10', false)
            ->assertSee('Tangkai', false);

        $this->actingAs($user)->get(route('barang-masuk.index'))
            ->assertOk()
            ->assertSee('Mawar Merah', false)
            ->assertSee('Rp 100.000', false)
            ->assertSee('10', false)
            ->assertSee('Toko Bunga Melati', false)
            ->assertSee('Gudang Utama', false)
            ->assertSee('Segar', false);
    }

    public function test_barang_masuk_page_has_cross_navigation_to_item(): void
    {
        $response = $this->actingAs($this->penjual())->get(route('barang-masuk.index'));

        $response->assertOk();
        $response->assertSee('Barang Masuk', false);
        $response->assertSee('Daftar Item', false);
        $response->assertSee('Catat Barang Masuk', false);
        $response->assertSee(route('item.index'), false);
        $response->assertSee(route('item.create'), false);
    }
}
