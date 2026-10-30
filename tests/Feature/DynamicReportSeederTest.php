<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BatchInventoryService;
use Database\Seeders\DynamicReportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicReportSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_builds_multi_price_batches_and_reports_render(): void
    {
        $this->seed(DynamicReportSeeder::class);

        $user = User::where('username', 'demo-laporan')->firstOrFail();
        $snapshot = app(BatchInventoryService::class)->snapshot($user->id);

        $anggrek = $snapshot['batches']->where('nama_barang', 'Bunga Anggrek Bulan')->values();
        $mawar = $snapshot['batches']->where('nama_barang', 'Mawar Merah Super')->values();

        $this->assertCount(2, $anggrek);
        $this->assertSame([5, 5], $anggrek->pluck('sisa_stok')->all());
        $this->assertSame([15000.0, 17000.0], $anggrek->pluck('harga_beli')->map(fn ($v) => (float) $v)->all());
        $this->assertSame(['Pak Budi', 'Pemasok Umum'], $anggrek->pluck('pemasok')->all());

        $this->assertCount(2, $mawar);
        $this->assertSame([20, 10], $mawar->pluck('sisa_stok')->all());
        $this->assertSame([3000.0, 3500.0], $mawar->pluck('harga_beli')->map(fn ($v) => (float) $v)->all());

        $outgoings = $snapshot['outgoings'];
        $this->assertCount(4, $outgoings);
        $this->assertSame(545000.0, (float) $outgoings->sum('omzet'));
        $this->assertSame(285000.0, (float) $outgoings->sum('modal_riil'));
        $this->assertSame(260000.0, (float) $outgoings->sum('laba_riil'));

        $this->actingAs($user)->get(route('laporan'))
            ->assertOk()
            ->assertSee('Bunga Anggrek Bulan')
            ->assertSee('Mawar Merah Super')
            ->assertSee('Total Nilai Aset Riil');

        $this->actingAs($user)->get(route('omzet.index'))
            ->assertOk()
            ->assertSee('Total Omzet Riil')
            ->assertSee('Total Laba Bersih Riil')
            ->assertSee('Bunga Anggrek Bulan');
    }
}
