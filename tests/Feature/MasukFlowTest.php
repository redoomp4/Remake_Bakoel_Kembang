<?php

namespace Tests\Feature;

use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Pemasok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasukFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_inline_item_and_supplier_creation_via_ajax(): void
    {
        $user = User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-penjual',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('form.barang-masuk.store'), [
            'id_item' => 'NEW:Mawar Test',
            'jumlah' => 5,
            'harga_beli' => 10000,
            'pemasok_id' => 'Pemasok Baru Test',
        ]);

        $response->assertOk()->assertJson([
            'status' => true,
            'success' => true,
        ]);

        $item = Item::where('user_id', $user->id)->where('nama_barang', 'Mawar Test')->first();
        $this->assertNotNull($item);
        $this->assertSame('BRG-001', $item->kode_barang);

        $pemasok = Pemasok::where('user_id', $user->id)->where('nama_pemasok', 'Pemasok Baru Test')->first();
        $this->assertNotNull($pemasok);

        $this->assertDatabaseHas('barang_masuks', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'id_pemasok' => $pemasok->id,
            'jumlah' => 5,
            'harga_satuan' => 10000,
            'total_harga' => 50000,
        ]);

        $this->assertSame(1, BarangMasuk::where('user_id', $user->id)->count());
        $this->assertDatabaseHas('lokasis', ['user_id' => $user->id, 'nama_lokasi' => 'Gudang Utama']);
        $this->assertDatabaseHas('kondisis', ['user_id' => $user->id, 'nama_kondisi' => 'Segar']);
    }

    public function test_default_supplier_is_pemasok_umum_and_existing_item_id_is_used(): void
    {
        $user = User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-penjual',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->postJson(route('form.barang-masuk.store'), [
            'id_item' => 'NEW:Melati Test',
            'jumlah' => 2,
            'harga_beli' => 5000,
        ])->assertOk();

        $item = Item::where('user_id', $user->id)->where('nama_barang', 'Melati Test')->firstOrFail();

        $this->actingAs($user)->postJson(route('form.barang-masuk.store'), [
            'id_item' => (string) $item->id,
            'jumlah' => 3,
            'harga_beli' => 6000,
        ])->assertOk();

        $this->assertDatabaseHas('pemasoks', [
            'user_id' => $user->id,
            'nama_pemasok' => 'Pemasok Umum',
        ]);
        $this->assertSame(2, BarangMasuk::where('user_id', $user->id)->count());
    }

    public function test_friendly_validation_message_when_quantity_missing(): void
    {
        $user = User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-penjual',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('form.barang-masuk.store'), [
            'id_item' => 'NEW:Anggrek Test',
            'harga_beli' => 10000,
        ]);

        $response->assertStatus(422)->assertJson([
            'status' => false,
            'success' => false,
            'message' => 'Harap isi jumlah bunga terlebih dahulu ya Bu/Pak.',
        ]);

        $this->assertSame(0, Item::where('user_id', $user->id)->count());
        $this->assertSame(0, BarangMasuk::where('user_id', $user->id)->count());
    }

    public function test_form_masuk_page_renders_simplified_ui(): void
    {
        $user = User::factory()->create([
            'name' => 'Ibu Penjual',
            'username' => 'ibu-penjual-view',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('form.index'));

        $response->assertOk();
        $response->assertSee('Pemasok Umum (Default)', false);
        $response->assertSee('SIMPAN &amp; TAMBAH LAGI', false);
        $response->assertSee('Bunga Baru (Otomatis Didaftarkan)', false);
    }
}
