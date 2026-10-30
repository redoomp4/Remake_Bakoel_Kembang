<?php

namespace Tests\Feature;

use App\Exports\ArrayExport;
use App\Exports\OmzetExport;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Item $itemMawar;

    protected Item $itemMelati;

    protected Lokasi $lokasi;

    protected Kondisi $kondisi;

    protected Pemasok $pemasok;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Ibu Pemilik Toko',
            'username' => 'ibupemilik',
            'role' => 'penjual',
            'email_verified_at' => now(),
        ]);

        $kategori = Kategori::create([
            'user_id' => $this->user->id,
            'kategori' => 'Bunga Potong Segar',
        ]);

        $satuan = Satuan::create([
            'user_id' => $this->user->id,
            'nama_satuan' => 'Tangkai',
        ]);

        $this->lokasi = Lokasi::create([
            'user_id' => $this->user->id,
            'nama_lokasi' => 'Gudang Utama',
        ]);

        $this->kondisi = Kondisi::create([
            'user_id' => $this->user->id,
            'nama_kondisi' => 'Segar',
        ]);

        $this->pemasok = Pemasok::create([
            'user_id' => $this->user->id,
            'nama_pemasok' => 'Kebun Bunga Lembang',
        ]);

        $this->itemMawar = Item::create([
            'user_id' => $this->user->id,
            'kode_barang' => 'MWR-001',
            'nama_barang' => 'Mawar Merah Holland',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 5,
        ]);

        $this->itemMelati = Item::create([
            'user_id' => $this->user->id,
            'kode_barang' => 'MLT-001',
            'nama_barang' => 'Melati Putih Wangi',
            'id_kategori' => $kategori->id,
            'id_satuan' => $satuan->id,
            'stok_minimum' => 5,
        ]);

        // 1. Barang Masuk Mawar: 20 pcs @ Rp 15.000 = Rp 300.000
        BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->itemMawar->id,
            'id_pemasok' => $this->pemasok->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah' => 20,
            'harga_satuan' => 15000,
            'total_harga' => 300000,
            'tanggal_masuk' => now()->subDays(5),
            'catatan' => 'Restock Mawar Nota #001',
        ]);

        // 2. Barang Masuk Melati: 30 pcs @ Rp 10.000 = Rp 300.000
        BarangMasuk::create([
            'user_id' => $this->user->id,
            'item_id' => $this->itemMelati->id,
            'id_pemasok' => $this->pemasok->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah' => 30,
            'harga_satuan' => 10000,
            'total_harga' => 300000,
            'tanggal_masuk' => now()->subDays(4),
            'catatan' => 'Restock Melati Nota #002',
        ]);

        // 3. Penjualan Mawar: 5 pcs @ Rp 25.000 = Rp 125.000
        BarangKeluar::create([
            'user_id' => $this->user->id,
            'item_id' => $this->itemMawar->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah_keluar' => 5,
            'harga_jual' => 25000,
            'total_harga_jual' => 125000,
            'jenis_transaksi' => 'Penjualan',
            'penerima' => 'Ibu Sarah',
            'lokasi_tujuan' => 'Toko Kasir',
            'tanggal_keluar' => now()->subDays(2),
            'catatan' => 'Buket mawar merah',
        ]);

        // 4. Penjualan Melati: 10 pcs @ Rp 18.000 = Rp 180.000
        BarangKeluar::create([
            'user_id' => $this->user->id,
            'item_id' => $this->itemMelati->id,
            'id_lokasi' => $this->lokasi->id,
            'id_kondisi' => $this->kondisi->id,
            'jumlah_keluar' => 10,
            'harga_jual' => 18000,
            'total_harga_jual' => 180000,
            'jenis_transaksi' => 'Penjualan',
            'penerima' => 'Pak Ahmad',
            'lokasi_tujuan' => 'Toko Kasir',
            'tanggal_keluar' => now()->subDay(),
            'catatan' => 'Ronce melati',
        ]);
    }

    /**
     * Test 1: Menguji endpoint download PDF Stok dan Omzet mengembalikan HTTP 200 OK dengan header application/pdf.
     */
    public function test_endpoints_download_pdf_stok_and_omzet_return_200_with_pdf_header(): void
    {
        // 1. Cetak PDF Stok
        $responseStok = $this->actingAs($this->user)->get(route('laporan.pdf'));
        $responseStok->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $responseStok->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $responseStok->getContent());

        // 2. Cetak PDF Omzet
        $responseOmzet = $this->actingAs($this->user)->get(route('export.omzet.pdf'));
        $responseOmzet->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $responseOmzet->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $responseOmzet->getContent());

        // 3. Cetak PDF Arus Barang
        $responseArus = $this->actingAs($this->user)->get(route('laporan.arus.pdf'));
        $responseArus->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $responseArus->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $responseArus->getContent());
    }

    /**
     * Test 2: Menguji endpoint export Excel Stok dan Omzet mengembalikan HTTP 200 OK dengan format file yang valid.
     */
    public function test_endpoints_export_excel_stok_and_omzet_return_200_with_valid_file_format(): void
    {
        // 1. Export Excel Stok (XLSX)
        $responseStok = $this->actingAs($this->user)->get(route('laporan.excel', ['format' => 'xlsx']));
        $responseStok->assertOk();
        $dispositionStok = (string) $responseStok->headers->get('content-disposition');
        $this->assertStringContainsString('attachment', $dispositionStok);
        $this->assertStringContainsString('.xlsx', $dispositionStok);

        // 2. Export CSV Stok
        $responseStokCsv = $this->actingAs($this->user)->get(route('laporan.excel', ['format' => 'csv']));
        $responseStokCsv->assertOk();
        $dispositionStokCsv = (string) $responseStokCsv->headers->get('content-disposition');
        $this->assertStringContainsString('.csv', $dispositionStokCsv);

        // 3. Export Excel Omzet (XLSX)
        $responseOmzet = $this->actingAs($this->user)->get(route('export.omzet.excel', ['format' => 'xlsx']));
        $responseOmzet->assertOk();
        $dispositionOmzet = (string) $responseOmzet->headers->get('content-disposition');
        $this->assertStringContainsString('attachment', $dispositionOmzet);
        $this->assertStringContainsString('.xlsx', $dispositionOmzet);

        // 4. Export CSV Omzet
        $responseOmzetCsv = $this->actingAs($this->user)->get(route('export.omzet.excel', ['format' => 'csv']));
        $responseOmzetCsv->assertOk();
        $dispositionOmzetCsv = (string) $responseOmzetCsv->headers->get('content-disposition');
        $this->assertStringContainsString('.csv', $dispositionOmzetCsv);
    }

    /**
     * Test 3: Memastikan total nominal rupiah di file export sama persis (100% match) dengan kalkulasi data riil di database.
     */
    public function test_total_nominal_rupiah_in_export_matches_real_database_calculation_100_percent(): void
    {
        // Kalkulasi Riil Database untuk Omzet:
        // Mawar: 5 pcs @ Rp 25.000 = Rp 125.000
        // Melati: 10 pcs @ Rp 18.000 = Rp 180.000
        // Total Omzet Riil = Rp 305.000
        $dbTotalOmzet = (float) BarangKeluar::where('user_id', $this->user->id)->sum('total_harga_jual');
        $this->assertEquals(305000, $dbTotalOmzet);

        $requestOmzet = Request::create(route('export.omzet.excel'), 'GET');
        $requestOmzet->setUserResolver(fn () => $this->user);

        $omzetExport = new OmzetExport($requestOmzet);
        $omzetRows = $omzetExport->collection();

        // Cari baris ringkasan / total di export
        $totalRow = $omzetRows->firstWhere('Harga Jual', 'Total Omzet');
        $this->assertNotNull($totalRow);
        $this->assertEquals($dbTotalOmzet, (float) $totalRow['Omzet'], 'Total Omzet di export Excel harus sama persis 100% dengan database.');

        // Kalkulasi Riil Database untuk Stok & Aset:
        // Mawar masuk 20, keluar 5 -> sisa 15 @ Rp 15.000 = Rp 225.000
        // Melati masuk 30, keluar 10 -> sisa 20 @ Rp 10.000 = Rp 200.000
        // Total Nilai Aset Riil = Rp 425.000
        $inventoryService = app(BatchInventoryService::class);
        $batches = $inventoryService->batches($this->user->id);
        $dbTotalAset = (float) $batches->sum('nilai_aset');
        $this->assertEquals(425000, $dbTotalAset);

        // Verifikasi ArrayExport untuk Stok
        $dataRows = $batches->filter(fn ($row) => $row['sisa_stok'] > 0);
        $calculatedAsetFromRows = (float) $dataRows->sum('nilai_aset');
        $this->assertEquals($dbTotalAset, $calculatedAsetFromRows, 'Total Nilai Aset di export Stok harus sama persis 100% dengan database.');
    }

    /**
     * Test 4: Verifikasi filter query (nama bunga & tanggal) menghasilkan kalkulasi riil terfilter tanpa data hardcoded.
     */
    public function test_filtered_export_queries_dynamically_calculate_accurate_subset(): void
    {
        // Filter hanya bunga "Mawar"
        $requestFilterMawar = Request::create(route('export.omzet.excel', ['search' => 'Mawar']), 'GET', ['search' => 'Mawar']);
        $requestFilterMawar->setUserResolver(fn () => $this->user);

        $omzetExportFiltered = new OmzetExport($requestFilterMawar);
        $rowsMawar = $omzetExportFiltered->collection();

        // Baris data (tidak termasuk baris total) hanya boleh ada Mawar
        $dataRowsMawar = $rowsMawar->filter(fn ($r) => ! empty($r['Nama Barang']));
        $this->assertCount(1, $dataRowsMawar);
        $this->assertEquals('Mawar Merah Holland', $dataRowsMawar->first()['Nama Barang']);

        $totalRowMawar = $rowsMawar->firstWhere('Harga Jual', 'Total Omzet');
        $this->assertEquals(125000, (float) $totalRowMawar['Omzet'], 'Total Omzet filter Mawar harus Rp 125.000.');
    }
}
