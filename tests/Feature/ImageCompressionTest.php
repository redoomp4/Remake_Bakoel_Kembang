<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImageCompressionTest extends TestCase
{
    use RefreshDatabase;

    private User $penjual;

    private Kategori $kategori;

    private Satuan $satuan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->penjual = User::factory()->create([
            'name' => 'Ibu Penjual Bunga',
            'username' => 'ibu-bunga',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $this->kategori = Kategori::create([
            'user_id' => $this->penjual->id,
            'kategori' => 'Bunga Segar',
        ]);

        $this->satuan = Satuan::create([
            'user_id' => $this->penjual->id,
            'nama_satuan' => 'Tangkai',
        ]);
    }

    public function test_item_create_page_includes_compressor_script_and_config(): void
    {
        $response = $this->actingAs($this->penjual)->get(route('item.create'));

        $response->assertOk();
        // Verifikasi keberadaan Compressor.js CDN
        $response->assertSee('compressor.min.js', false);
        // Verifikasi konfigurasi kompresi otomatis client-side
        $response->assertSee('quality: 0.6', false);
        $response->assertSee('maxWidth: 1200', false);
        $response->assertSee('maxHeight: 1200', false);
        // Verifikasi input file foto
        $response->assertSee('id="foto"', false);
        $response->assertSee('name="foto"', false);
    }

    public function test_master_item_form_accepts_compressed_image_under_server_limit(): void
    {
        Storage::fake('public');

        // Simulasi foto kamera yang sudah dikompresi client-side menjadi ukuran wajar (~1.5 MB < 2 MB)
        $compressedFile = UploadedFile::fake()->image('mawar_kompres.jpg', 1200, 900)->size(1500);

        $payload = [
            'nama_barang' => 'Mawar Holland Segar',
            'id_kategori' => $this->kategori->id,
            'id_satuan' => $this->satuan->id,
            'stok_minimum' => 5,
            'deskripsi' => 'Bunga mawar holland segar kualitas ekspor.',
            'buat_stok_awal' => 0,
            'foto' => $compressedFile,
        ];

        $response = $this->actingAs($this->penjual)
            ->post(route('item.store'), $payload);

        $response->assertRedirect(route('item.index'));

        $item = Item::where('nama_barang', 'Mawar Holland Segar')->firstOrFail();
        $this->assertNotNull($item->foto);

        // Verifikasi file tersimpan di storage public/foto_barang
        Storage::disk('public')->assertExists($item->foto);
    }

    public function test_master_item_edit_can_update_photo_with_compressor_support(): void
    {
        Storage::fake('public');

        $initialFile = UploadedFile::fake()->image('awal.jpg', 800, 600)->size(500);
        $item = Item::create([
            'user_id' => $this->penjual->id,
            'kode_barang' => 'BRG-TES-01',
            'public_token' => (string) Str::uuid(),
            'nama_barang' => 'Tulip Kuning',
            'id_kategori' => $this->kategori->id,
            'id_satuan' => $this->satuan->id,
            'stok_minimum' => 2,
            'foto' => $initialFile->store('foto_barang', 'public'),
        ]);

        $oldPhotoPath = $item->foto;
        Storage::disk('public')->assertExists($oldPhotoPath);

        // Upload foto baru (terkompresi client-side)
        $newCompressedFile = UploadedFile::fake()->image('tulip_baru.jpg', 1200, 1000)->size(1200);

        $payload = [
            'nama_barang' => 'Tulip Kuning Premium',
            'id_kategori' => $this->kategori->id,
            'id_satuan' => $this->satuan->id,
            'stok_minimum' => 3,
            'deskripsi' => 'Tulip kuning segar.',
            'foto' => $newCompressedFile,
        ];

        $response = $this->actingAs($this->penjual)
            ->put(route('item.update', $item->id), $payload);

        $response->assertRedirect(route('item.index'));

        $item->refresh();
        $this->assertSame('Tulip Kuning Premium', $item->nama_barang);
        $this->assertNotSame($oldPhotoPath, $item->foto);

        // Foto baru tersimpan, foto lama dibersihkan
        Storage::disk('public')->assertExists($item->foto);
        Storage::disk('public')->assertMissing($oldPhotoPath);
    }
}
