<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InlineItemCreationTest extends TestCase
{
    use RefreshDatabase;

    private function penjual(): User
    {
        return User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-inline-item',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);
    }

    public function test_barang_masuk_page_has_inline_item_button_and_modal(): void
    {
        $response = $this->actingAs($this->penjual())->get(route('form.index'));

        $response->assertOk();
        $response->assertSee('Tambah Item Baru', false);
        $response->assertSee('Tambah Bunga / Item Baru', false);
        $response->assertSee('Simpan & Pilih Item', false);
    }

    public function test_quick_item_store_creates_item_with_selected_unit(): void
    {
        $user = $this->penjual();

        $response = $this->actingAs($user)->postJson(route('items.quick-store'), [
            'nama_item' => 'Anggrek Bulan Baru',
            'satuan' => 'Pot',
        ]);

        $response->assertOk()->assertJson([
            'status' => true,
            'item' => [
                'nama_item' => 'Anggrek Bulan Baru',
                'satuan' => 'Pot',
            ],
        ]);

        $item = Item::where('user_id', $user->id)
            ->where('nama_barang', 'Anggrek Bulan Baru')
            ->firstOrFail();

        $this->assertSame('Pot', $item->satuan->nama_satuan);
        $this->assertSame('Bunga & Tanaman', $item->kategori->kategori);
        $this->assertSame(0, (int) $item->total_stok);
    }

    public function test_form_item_store_accepts_existing_ids_or_new_names(): void
    {
        $user = $this->penjual();
        $kategori = Kategori::create([
            'user_id' => $user->id,
            'kategori' => 'Bunga Meja',
        ]);
        $satuan = Satuan::create([
            'user_id' => $user->id,
            'nama_satuan' => 'Tangkai',
        ]);

        $response = $this->actingAs($user)->post(route('form.item.store'), [
            'nama_barang' => 'Mawar Meja',
            'kategori_input' => (string) $kategori->id,
            'satuan_input' => 'Ikat Baru',
            'stok_minimum' => 0,
        ]);

        $response->assertRedirect(route('item.index'));

        $item = Item::where('user_id', $user->id)
            ->where('nama_barang', 'Mawar Meja')
            ->firstOrFail();

        $this->assertSame($kategori->id, $item->id_kategori);
        $this->assertSame('Ikat Baru', $item->satuan->nama_satuan);
        $this->assertSame(1, Kategori::where('user_id', $user->id)->count());
        $this->assertSame(2, Satuan::where('user_id', $user->id)->count());
    }

    public function test_quick_item_store_reuses_existing_item_instead_of_duplicating(): void
    {
        $user = $this->penjual();
        $kategori = Kategori::create([
            'user_id' => $user->id,
            'kategori' => 'Bunga & Tanaman',
        ]);
        $satuan = Satuan::create([
            'user_id' => $user->id,
            'nama_satuan' => 'Tangkai',
        ]);
        $item = Item::create([
            'user_id' => $user->id,
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Mawar Lama',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 0,
        ]);

        $this->actingAs($user)->postJson(route('items.quick-store'), [
            'nama_item' => 'mawar lama',
            'satuan' => 'Tangkai',
        ])->assertOk()->assertJson([
            'status' => true,
            'item' => ['id' => $item->id],
        ]);

        $this->assertSame(1, Item::where('user_id', $user->id)->count());
    }
}
