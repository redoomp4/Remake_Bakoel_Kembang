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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicSelect2CreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Item $item;

    protected Lokasi $lokasi;

    protected Kondisi $kondisi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Bunga Seller',
            'username' => 'bunga-seller',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $kategori = Kategori::create([
            'user_id' => $this->user->id,
            'kategori' => 'Bunga Potong',
        ]);

        $satuan = Satuan::create([
            'user_id' => $this->user->id,
            'nama_satuan' => 'Tangkai',
        ]);

        $this->item = Item::create([
            'user_id' => $this->user->id,
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Mawar Merah',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 5,
        ]);

        $this->lokasi = Lokasi::create([
            'user_id' => $this->user->id,
            'nama_lokasi' => 'Gudang Utama',
        ]);

        $this->kondisi = Kondisi::create([
            'user_id' => $this->user->id,
            'nama_kondisi' => 'Segar',
        ]);
    }

    public function test_barang_masuk_create_view_has_select2_tags_and_defaults(): void
    {
        $response = $this->actingAs($this->user)->get(route('barang-masuk.create'));

        $response->assertOk();
        $response->assertSee('select2-tags');
        $response->assertSee('Pemasok Umum');
        $response->assertSee('Gudang Utama');
        $response->assertSee('Segar');
        $response->assertSee('Ketik atau pilih nama pemasok (misal: Pak Budi / Pemasok Umum)');
    }

    public function test_dynamic_select2_creates_pemasok_lokasi_and_kondisi_on_the_fly(): void
    {
        $newPemasokName = 'Petani Mawar Lembang';
        $newLokasiName = 'Showcase Pendingin Depan';
        $newKondisiName = 'Kuncup Siap Mekar';

        $response = $this->actingAs($this->user)->post(route('barang-masuk.store'), [
            'item_id' => $this->item->id,
            'jumlah' => 20,
            'harga_satuan' => 12500,
            'tanggal_masuk' => now()->format('Y-m-d H:i:s'),
            'id_pemasok' => $newPemasokName,
            'id_lokasi' => $newLokasiName,
            'id_kondisi' => $newKondisiName,
            'catatan' => 'Restock perdana dari mitra baru',
        ]);

        $response->assertRedirect(route('barang-masuk.index'));

        // Assert dynamic firstOrCreate records in DB
        $pemasok = Pemasok::where('user_id', $this->user->id)->where('nama_pemasok', $newPemasokName)->first();
        $this->assertNotNull($pemasok);

        $lokasi = Lokasi::where('user_id', $this->user->id)->where('nama_lokasi', $newLokasiName)->first();
        $this->assertNotNull($lokasi);

        $kondisi = Kondisi::where('user_id', $this->user->id)->where('nama_kondisi', $newKondisiName)->first();
        $this->assertNotNull($kondisi);

        // Assert BarangMasuk record is persisted with new foreign keys
        $this->assertDatabaseHas('barang_masuks', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'id_pemasok' => $pemasok->id,
            'id_lokasi' => $lokasi->id,
            'id_kondisi' => $kondisi->id,
            'jumlah' => 20,
            'harga_satuan' => 12500,
            'total_harga' => 250000,
            'catatan' => 'Restock perdana dari mitra baru',
        ]);
    }

    public function test_dynamic_select2_persists_custom_jenis_transaksi_in_barang_keluar(): void
    {
        // 1. Masukkan stok terlebih dahulu
        BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'id_pemasok' => Pemasok::firstOrCreate(['user_id' => $this->user->id, 'nama_pemasok' => 'Pemasok Umum'])->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah' => 30,
            'harga_satuan' => 10000,
            'total_harga' => 300000,
            'tanggal_masuk' => now()->subDay(),
        ]);

        // 2. Transaksi keluar dengan jenis transaksi kustom dari Select2 tags
        $customJenis = 'Project Dekorasi Akad';

        $response = $this->actingAs($this->user)->post(route('barang-keluar.store'), [
            'item_id' => $this->item->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah_keluar' => 10,
            'harga_jual' => 25000,
            'penerima' => 'Ibu Sisca Wedding Organizer',
            'jenis_transaksi' => $customJenis,
            'lokasi_tujuan' => 'Gedung Serbaguna Lembang',
            'catatan' => 'Bunga hiasan meja akad nikah',
        ]);

        $response->assertRedirect(route('barang-keluar.index'));

        // Assert BarangKeluar record has the custom transaction type
        $this->assertDatabaseHas('barang_keluars', [
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah_keluar' => 10,
            'harga_jual' => 25000,
            'total_harga_jual' => 250000,
            'penerima' => 'Ibu Sisca Wedding Organizer',
            'jenis_transaksi' => $customJenis,
            'lokasi_tujuan' => 'Gedung Serbaguna Lembang',
        ]);
    }

    public function test_barang_keluar_edit_view_retains_custom_jenis_transaksi(): void
    {
        // Masukkan stok & barang keluar
        BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'id_pemasok' => Pemasok::firstOrCreate(['user_id' => $this->user->id, 'nama_pemasok' => 'Pemasok Umum'])->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah' => 30,
            'harga_satuan' => 10000,
            'total_harga' => 300000,
            'tanggal_masuk' => now()->subDay(),
        ]);

        $barangKeluar = BarangKeluar::create([
            'user_id' => $this->user->id,
            'item_id' => $this->item->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah_keluar' => 5,
            'harga_jual' => 25000,
            'total_harga_jual' => 125000,
            'penerima' => 'Pelanggan Setia',
            'jenis_transaksi' => 'Event Wedding Spesial',
            'lokasi_tujuan' => 'Ballroom Hotel',
            'tanggal_keluar' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('barang-keluar.edit', $barangKeluar->id));

        $response->assertOk();
        $response->assertSee('Event Wedding Spesial');
        $response->assertSee('select2-tags');
    }
}
