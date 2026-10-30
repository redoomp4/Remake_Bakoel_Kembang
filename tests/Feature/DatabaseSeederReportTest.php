<?php

namespace Tests\Feature;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_full_items_and_transactions_for_testing(): void
    {
        $this->seed(DatabaseSeeder::class);

        $penjual = User::where('email', 'penjual@gmail.com')->firstOrFail();

        $this->assertGreaterThan(0, Item::where('user_id', $penjual->id)->count());
        $this->assertGreaterThan(0, BarangMasuk::where('user_id', $penjual->id)->count());
        $this->assertGreaterThan(0, BarangKeluar::where('user_id', $penjual->id)->count());

        $laporanResponse = $this->actingAs($penjual)->get(route('laporan'));
        $laporanResponse->assertOk();
        $laporanResponse->assertSee('Laporan Stok', false);
        $laporanResponse->assertSee('TOTAL NILAI ASET RIIL', false);
        $laporanResponse->assertSee('Bunga Anggrek Bulan Putih', false);
        $laporanResponse->assertDontSee('</style>'.PHP_EOL.'        :root', false);

        $arusResponse = $this->actingAs($penjual)->get(route('laporan.arus'));
        $arusResponse->assertOk();
        $arusResponse->assertSee('Laporan Arus Barang', false);
        $arusResponse->assertSee('TOTAL OMZET RIIL', false);
        $arusResponse->assertSee('TOTAL LABA BERSIH RIIL', false);
        $arusResponse->assertDontSee('</style>'.PHP_EOL.'        :root', false);
    }
}
