<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Exports\LaporanArusExport;
use App\Exports\LaporanAsetExport;
use App\Exports\LaporanKeuanganExport;
use App\Exports\OmzetExport;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Services\BatchInventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Helper periode:
     * - Jika user mengisi start/end → pakai itu.
     * - Jika tidak, gunakan $fallback() untuk min/max dari dataset.
     * - Jika data kosong → pakai hari ini agar tidak tampil "- s/d -".
     *
     * @param  ?string  $start
     * @param  ?string  $end
     * @param  \Closure():array{0:?string,1:?string}  $fallback
     * @param  string  $tz
     * @return array{0:Carbon,1:Carbon}
     */
    private function normalizeDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        $value = trim($value);

        if (str_contains($value, '/')) {
            try {
                return Carbon::createFromFormat('d/m/Y', $value)->toDateString();
            } catch (\Throwable $e) { /* fallback */
            }
        }
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function resolvePeriod(?string $start, ?string $end, \Closure $fallback, string $tz = 'Asia/Makassar'): array
    {
        if ($start && $end) {
            return [
                Carbon::parse($start, $tz)->startOfDay(),
                Carbon::parse($end, $tz)->endOfDay(),
            ];
        }

        [$min, $max] = $fallback();

        $minC = $min ? Carbon::parse($min, $tz) : Carbon::now($tz);
        $maxC = $max ? Carbon::parse($max, $tz) : Carbon::now($tz);

        return [$minC, $maxC];
    }

    public function laporan(Request $request, BatchInventoryService $inventory)
    {
        $user = $request->user();

        $nama = $request->filled('nama_barang') ? (string) $request->nama_barang : null;
        $kode = $request->filled('kode_barang') ? (string) $request->kode_barang : null;
        $lokasiId = $request->filled('lokasi') ? (int) $request->lokasi : null;

        $startDate = $this->normalizeDate($request->input('start_date'));
        $endDate = $this->normalizeDate($request->input('end_date'));
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();

        $collection = $inventory->batches($user->id, $end)
            ->filter(fn (array $row) => (int) $row['sisa_stok'] > 0)
            ->when($startDate, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal_masuk'] && $row['tanggal_masuk']->gte(Carbon::parse($startDate)->startOfDay())
            ))
            ->when($nama, fn ($rows) => $rows->filter(
                fn (array $row) => stripos($row['nama_barang'], $nama) !== false
            ))
            ->when($kode, fn ($rows) => $rows->filter(
                fn (array $row) => stripos($row['kode_barang'], $kode) !== false
            ))
            ->when($lokasiId, fn ($rows) => $rows->where('lokasi_id', $lokasiId))
            ->values();

        $totalAset = (float) $collection->sum('nilai_aset');
        $totalBatches = $collection->count();

        $groups = $collection
            ->groupBy('kode_barang')
            ->map(function ($batches, $kode) {
                $ordered = $batches
                    ->sortBy(fn (array $row) => $row['tanggal_masuk']?->timestamp ?? 0)
                    ->values();
                $first = $ordered->first();

                return [
                    'kode_barang' => $kode,
                    'nama_barang' => $first['nama_barang'] ?? '-',
                    'satuan' => $first['satuan'] ?? '-',
                    'lokasi' => $batches->pluck('lokasi')->filter()->unique()->implode(', '),
                    'total_stok' => (int) $batches->sum('sisa_stok'),
                    'total_aset' => (float) $batches->sum('nilai_aset'),
                    'batch_count' => $batches->count(),
                    'batches' => $ordered->all(),
                ];
            })
            ->values();

        $sortBy = $request->get('sort_by', 'nama_barang');
        $sortDesc = strtolower($request->get('sort_dir', 'asc')) === 'desc';
        $allowedSort = ['kode_barang', 'nama_barang', 'total_stok', 'total_aset', 'batch_count'];

        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'nama_barang';
        }

        $groups = $groups->sortBy(function (array $row) use ($sortBy) {
            $value = $row[$sortBy] ?? null;

            return is_string($value) ? mb_strtolower($value) : $value;
        }, SORT_REGULAR, $sortDesc)->values();

        $perPage = 10;
        $current = LengthAwarePaginator::resolveCurrentPage();
        $data = new LengthAwarePaginator(
            $groups->slice(($current - 1) * $perPage, $perPage)->values(),
            $groups->count(),
            $perPage,
            $current,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $lokasis = Lokasi::where('user_id', $user->id)->orderBy('nama_lokasi')->get(['id', 'nama_lokasi'])->toArray();

        return view('laporan.index', [
            'data' => $data,
            'lokasis' => $lokasis,
            'totalAset' => $totalAset,
            'totalBatches' => $totalBatches,
        ]);
    }

    // =========================
    // === Helper yang dipakai ==
    // =========================

    private function stockBatchRows(Request $request, int $userId): Collection
    {
        $startDate = $this->normalizeDate($request->input('start_date'));
        $endDate = $this->normalizeDate($request->input('end_date'));
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : now();

        return app(BatchInventoryService::class)
            ->batches($userId, $end)
            ->filter(fn (array $row) => (int) $row['sisa_stok'] > 0)
            ->when($request->filled('nama_barang'), fn ($rows) => $rows->filter(
                fn (array $row) => stripos($row['nama_barang'], (string) $request->nama_barang) !== false
            ))
            ->when($request->filled('kode_barang'), fn ($rows) => $rows->filter(
                fn (array $row) => stripos($row['kode_barang'], (string) $request->kode_barang) !== false
            ))
            ->when($request->filled('lokasi'), fn ($rows) => $rows->where('lokasi_id', (int) $request->lokasi))
            ->when($startDate, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal_masuk'] && $row['tanggal_masuk']->gte(Carbon::parse($startDate)->startOfDay())
            ))
            ->values();
    }

    // =========================
    // ====== LAPORAN STOK =====
    // =========================

    public function exportPdf(Request $request): Response
    {
        $user = $request->user();
        $data = $this->stockBatchRows($request, $user->id);

        [$periodeStart, $periodeEnd] = $this->resolvePeriod(
            $request->start_date,
            $request->end_date,
            function () use ($data) {
                $min = $data->pluck('tanggal_masuk')->filter()->min();
                $max = $data->pluck('tanggal_masuk')->filter()->max();

                return [
                    $min instanceof Carbon ? $min->toDateString() : ($min ? (string) $min : null),
                    $max instanceof Carbon ? $max->toDateString() : ($max ? (string) $max : null),
                ];
            }
        );

        $viewData = [
            'data' => $data,
            'nama' => $user->name,
            'username' => $user->username,
            'tanggalAwal' => $periodeStart->format('d/m/Y'),
            'tanggalAkhir' => $periodeEnd->format('d/m/Y'),
            'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
            'totalAset' => (float) $data->sum('nilai_aset'),
            'filters' => [
                'Nama Barang' => $request->nama_barang,
                'Kode Barang' => $request->kode_barang,
                'Lokasi' => $request->filled('lokasi') ? optional(Lokasi::find((int) $request->lokasi))->nama_lokasi : null,
            ],
        ];

        return Pdf::loadView('laporan.print', $viewData)->stream('laporan_stok_barang.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $rows = $this->stockBatchRows($request, $request->user()->id);
        $totalAset = (float) $rows->sum('nilai_aset');
        $totalStok = (int) $rows->sum('sisa_stok');

        $data = $rows->map(fn (array $row, int $idx) => [
            'No' => $idx + 1,
            'No. Nota / Batch' => $row['batch_code'],
            'Tanggal Masuk' => $row['tanggal_masuk'] instanceof Carbon ? $row['tanggal_masuk']->format('d/m/Y') : ($row['tanggal_masuk'] ? date('d/m/Y', strtotime((string) $row['tanggal_masuk'])) : '-'),
            'Kode Barang' => $row['kode_barang'],
            'Nama Bunga' => $row['nama_barang'],
            'Lokasi' => $row['lokasi'],
            'Kondisi' => $row['kondisi'],
            'Sisa Stok' => (int) $row['sisa_stok'],
            'Satuan' => $row['satuan'] ?? 'pcs',
            'Harga Beli (Rp)' => (float) $row['harga_beli'],
            'Nilai Aset (Rp)' => (float) $row['nilai_aset'],
        ]);

        if ($data->isNotEmpty()) {
            $data->push([
                'No' => '',
                'No. Nota / Batch' => '',
                'Tanggal Masuk' => '',
                'Kode Barang' => '',
                'Nama Bunga' => '',
                'Lokasi' => '',
                'Kondisi' => 'TOTAL',
                'Sisa Stok' => $totalStok,
                'Satuan' => '',
                'Harga Beli (Rp)' => 'Total Nilai Aset',
                'Nilai Aset (Rp)' => $totalAset,
            ]);
        }

        $headings = [
            'No',
            'No. Nota / Batch',
            'Tanggal Masuk',
            'Kode Barang',
            'Nama Bunga',
            'Lokasi',
            'Kondisi',
            'Sisa Stok',
            'Satuan',
            'Harga Beli (Rp)',
            'Nilai Aset (Rp)',
        ];

        $format = strtolower($request->input('format', 'xlsx'));
        $filename = 'laporan_stok_barang_'.now()->format('Ymd_His');

        if ($format === 'csv') {
            return Excel::download(new ArrayExport($data->toArray(), $headings), $filename.'.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new ArrayExport($data->toArray(), $headings), $filename.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    // =========================
    // ====== ARUS BARANG ======
    // =========================

    public function exportArusPdf(Request $request, BatchInventoryService $inventory): Response
    {
        $user = $request->user();
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now();
        $kategori = $request->filled('kategori') ? (int) $request->kategori : null;
        $lokasiId = $request->filled('lokasi') ? (int) $request->lokasi : null;
        $search = $request->filled('search') ? mb_strtolower((string) $request->search) : null;

        $snapshot = $inventory->snapshot($user->id, $end);

        $arusMasuk = $snapshot['batches']
            ->when($start, fn ($rows) => $rows->filter(fn (array $row) => $row['tanggal_masuk'] && $row['tanggal_masuk']->gte($start)))
            ->when($kategori, fn ($rows) => $rows->where('kategori_id', $kategori))
            ->when($lokasiId, fn ($rows) => $rows->where('lokasi_id', $lokasiId))
            ->when($search, fn ($rows) => $rows->filter(
                fn (array $row) => str_contains(mb_strtolower($row['nama_barang']), $search)
                    || str_contains(mb_strtolower($row['kode_barang']), $search)
                    || str_contains(mb_strtolower($row['pemasok']), $search)
            ))
            ->map(fn (array $row) => [
                'tanggal' => $row['tanggal_masuk'],
                'jenis' => 'Masuk',
                'kode_barang' => $row['kode_barang'],
                'nama_barang' => $row['nama_barang'],
                'batch_refs' => $row['batch_code'],
                'harga_satuan' => $row['harga_beli'],
                'harga_beli' => $row['harga_beli'],
                'harga_jual' => null,
                'jumlah' => $row['jumlah_masuk'],
                'omzet' => null,
                'modal_riil' => null,
                'laba_riil' => null,
                'lokasi' => $row['lokasi'],
                'pihak' => $row['pemasok'],
            ]);

        $arusKeluar = $snapshot['outgoings']
            ->when($start, fn ($rows) => $rows->filter(fn (array $row) => $row['tanggal'] && $row['tanggal']->gte($start)))
            ->when($kategori, fn ($rows) => $rows->where('kategori_id', $kategori))
            ->when($lokasiId, fn ($rows) => $rows->where('lokasi_id', $lokasiId))
            ->when($search, fn ($rows) => $rows->filter(
                fn (array $row) => str_contains(mb_strtolower($row['nama_barang']), $search)
                    || str_contains(mb_strtolower($row['kode_barang']), $search)
                    || str_contains(mb_strtolower($row['batch_refs']), $search)
            ))
            ->map(fn (array $row) => [
                'tanggal' => $row['tanggal'],
                'jenis' => 'Keluar',
                'kode_barang' => $row['kode_barang'],
                'nama_barang' => $row['nama_barang'],
                'batch_refs' => $row['batch_refs'] ?: '-',
                'harga_satuan' => $row['harga_jual'],
                'harga_beli' => null,
                'harga_jual' => $row['harga_jual'],
                'jumlah' => $row['jumlah_keluar'],
                'omzet' => $row['omzet'],
                'modal_riil' => $row['modal_riil'],
                'laba_riil' => $row['laba_riil'],
                'lokasi' => $row['lokasi'],
                'pihak' => $row['penerima'] ?? '-',
            ]);

        $combined = $arusMasuk->merge($arusKeluar)
            ->sortBy(fn (array $row) => $row['tanggal']?->timestamp ?? 0)
            ->values();

        $running = [];
        $enhanced = $combined->map(function (array $row) use (&$running) {
            $kode = $row['kode_barang'];
            $running[$kode] = $running[$kode] ?? 0;
            $masuk = $row['jenis'] === 'Masuk' ? $row['jumlah'] : 0;
            $keluar = $row['jenis'] === 'Keluar' ? $row['jumlah'] : 0;
            $running[$kode] += $masuk - $keluar;

            return $row + [
                'jumlah_masuk' => $masuk,
                'jumlah_keluar' => $keluar,
                'total_barang' => $running[$kode],
            ];
        });

        [$periodeStart, $periodeEnd] = $this->resolvePeriod(
            $request->start_date,
            $request->end_date,
            function () use ($enhanced) {
                $min = $enhanced->pluck('tanggal')->filter()->min();
                $max = $enhanced->pluck('tanggal')->filter()->max();

                return [
                    $min instanceof Carbon ? $min->toDateString() : ($min ? (string) $min : null),
                    $max instanceof Carbon ? $max->toDateString() : ($max ? (string) $max : null),
                ];
            }
        );

        $filters = [
            'Kategori' => $request->filled('kategori') ? optional(Kategori::find((int) $request->kategori))->kategori : null,
            'Lokasi' => $request->filled('lokasi') ? optional(Lokasi::find((int) $request->lokasi))->nama_lokasi : null,
            'Pencarian' => $request->search,
        ];

        return Pdf::loadView('laporan.arus_print', [
            'data' => $enhanced,
            'username' => $user->username,
            'nama' => $user->name,
            'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
            'tanggalAwal' => $periodeStart->format('d/m/Y'),
            'tanggalAkhir' => $periodeEnd->format('d/m/Y'),
            'filters' => $filters,
            'totalOmzet' => (float) $enhanced->where('jenis', 'Keluar')->sum('omzet'),
            'totalModal' => (float) $enhanced->where('jenis', 'Keluar')->sum('modal_riil'),
            'totalLaba' => (float) $enhanced->where('jenis', 'Keluar')->sum('laba_riil'),
        ])->stream('laporan_arus_barang.pdf');
    }

    public function exportArusExcel(Request $request): BinaryFileResponse
    {
        $format = strtolower($request->input('format', 'xlsx'));
        if ($format === 'csv') {
            return Excel::download(new LaporanArusExport($request), 'laporan_arus_barang.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new LaporanArusExport($request), 'laporan_arus_barang.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    // =========================
    // ========= OMZET =========
    // =========================

    public function exportOmzetPdf(Request $request, BatchInventoryService $inventory): Response
    {
        $user = $request->user();
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now();
        $search = $request->filled('search') ? mb_strtolower((string) $request->search) : null;

        $data = $inventory->outgoings($user->id, $end)
            ->when($start, fn ($rows) => $rows->filter(fn (array $row) => $row['tanggal'] && $row['tanggal']->gte($start)))
            ->when($request->filled('lokasi'), fn ($rows) => $rows->where('lokasi_id', (int) $request->lokasi))
            ->when($request->filled('kondisi'), fn ($rows) => $rows->where('kondisi_id', (int) $request->kondisi))
            ->when($search, fn ($rows) => $rows->filter(
                fn (array $row) => str_contains(mb_strtolower($row['nama_barang']), $search)
                    || str_contains(mb_strtolower($row['kode_barang']), $search)
            ))
            ->values()
            ->map(fn (array $row) => [
                'tanggal' => $row['tanggal'],
                'nama_barang' => $row['nama_barang'],
                'kode_barang' => $row['kode_barang'],
                'lokasi' => $row['lokasi'],
                'kondisi' => $row['kondisi'],
                'jumlah_keluar' => $row['jumlah_keluar'],
                'harga_jual' => $row['harga_jual'],
                'omzet_item' => $row['omzet'],
                'batch_refs' => $row['batch_refs'] ?: '-',
                'modal_riil' => $row['modal_riil'],
                'laba_riil' => $row['laba_riil'],
            ]);

        $total_omzet = (float) $data->sum('omzet_item');
        $total_modal = (float) $data->sum('modal_riil');
        $total_laba = (float) $data->sum('laba_riil');

        [$periodeStart, $periodeEnd] = $this->resolvePeriod(
            $request->start_date,
            $request->end_date,
            function () use ($data) {
                $min = $data->pluck('tanggal')->filter()->min();
                $max = $data->pluck('tanggal')->filter()->max();

                return [
                    $min instanceof Carbon ? $min->toDateString() : ($min ? (string) $min : null),
                    $max instanceof Carbon ? $max->toDateString() : ($max ? (string) $max : null),
                ];
            }
        );

        $filters = [
            'Tanggal Mulai' => $periodeStart->format('d/m/Y'),
            'Tanggal Selesai' => $periodeEnd->format('d/m/Y'),
            'Cari Barang' => $request->search,
            'Lokasi' => $request->filled('lokasi') ? optional(Lokasi::find((int) $request->lokasi))->nama_lokasi : null,
            'Kondisi' => $request->filled('kondisi') ? optional(Kondisi::find((int) $request->kondisi))->nama_kondisi : null,
        ];

        return Pdf::loadView('omzet.print', [
            'data' => $data,
            'total_omzet' => $total_omzet,
            'total_modal' => $total_modal,
            'total_laba' => $total_laba,
            'username' => $user->username,
            'nama' => $user->name,
            'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
            'tanggalAwal' => $filters['Tanggal Mulai'],
            'tanggalAkhir' => $filters['Tanggal Selesai'],
            'filters' => $filters,
        ])->stream('laporan_omzet.pdf');
    }

    public function exportOmzetExcel(Request $request): BinaryFileResponse
    {
        $format = strtolower($request->input('format', 'xlsx'));
        if ($format === 'csv') {
            return Excel::download(new OmzetExport($request), 'laporan_omzet.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new OmzetExport($request), 'laporan_omzet.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    // =========================
    // ========= ASET ==========
    // =========================

    public function exportAsetExcel(Request $request): BinaryFileResponse
    {
        $format = strtolower($request->input('format', 'xlsx'));
        if ($format === 'csv') {
            return Excel::download(new LaporanAsetExport($request), 'laporan_aset.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new LaporanAsetExport($request), 'laporan_aset.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportAsetPdf(Request $request): Response
    {
        $export = new LaporanAsetExport($request);
        $data = $export->collection();

        [$periodeStart, $periodeEnd] = $this->resolvePeriod(
            $request->start_date,
            $request->end_date,
            function () use ($request) {
                $bm = BarangMasuk::query();
                $bk = BarangKeluar::query();

                if ($request->filled('nama_barang')) {
                    $keyword = strtolower($request->nama_barang);
                    $bm->whereHas('item', fn (Builder $q) => $q->whereRaw('LOWER(nama_barang) LIKE ?', ["%{$keyword}%"]));
                    $bk->whereHas('item', fn (Builder $q) => $q->whereRaw('LOWER(nama_barang) LIKE ?', ["%{$keyword}%"]));
                }
                if ($request->filled('lokasi')) {
                    $bm->where('id_lokasi', (int) $request->lokasi);
                    $bk->where('id_lokasi', (int) $request->lokasi);
                }
                if ($request->filled('kondisi')) {
                    $bm->where('id_kondisi', (int) $request->kondisi);
                    $bk->where('id_kondisi', (int) $request->kondisi);
                }

                $minMasuk = $bm->min('tanggal_masuk');
                $maxMasuk = $bm->max('tanggal_masuk');
                $minKeluar = $bk->min('tanggal_keluar');
                $maxKeluar = $bk->max('tanggal_keluar');

                $min = $minMasuk && $minKeluar ? min($minMasuk, $minKeluar) : ($minMasuk ?: $minKeluar);
                $max = $maxMasuk && $maxKeluar ? max($maxMasuk, $maxKeluar) : ($maxMasuk ?: $maxKeluar);

                return [$min, $max];
            }
        );

        $filters = [
            'Nama Barang' => $request->nama_barang,
            'Lokasi' => $request->filled('lokasi') ? optional(Lokasi::find((int) $request->lokasi))->nama_lokasi : null,
            'Kondisi' => $request->filled('kondisi') ? optional(Kondisi::find((int) $request->kondisi))->nama_kondisi : null,
            'Tanggal Mulai' => $periodeStart->format('d/m/Y'),
            'Tanggal Selesai' => $periodeEnd->format('d/m/Y'),
        ];

        return Pdf::loadView('aset.aset_print', [
            'data' => $data,
            'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
            'tanggalAwal' => $filters['Tanggal Mulai'],
            'tanggalAkhir' => $filters['Tanggal Selesai'],
            'nama' => auth()->user()->name,
            'username' => auth()->user()->username,
            'filters' => $filters,
        ])->stream('laporan_aset.pdf');
    }

    // =========================
    // ======= HELPERS =========
    // =========================

    /**
     * Export Laporan Keuangan (Laba Rugi) ke Excel
     */
    public function exportKeuanganExcel(Request $request)
    {
        $user = $request->user();

        $startDate = $this->normalizeDate($request->input('start_date'));
        $endDate = $this->normalizeDate($request->input('end_date'));

        // Barang Masuk (Modal/Pembelian)
        $barangMasukQuery = BarangMasuk::with(['item', 'pemasok'])
            ->where('user_id', $user->id)
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_masuk', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('tanggal_masuk', '<=', $endDate))
            ->get();

        $barangMasukData = $barangMasukQuery->map(function ($bm) {
            return [
                'nama_barang' => optional($bm->item)->nama_barang ?? '-',
                'kode_barang' => $bm->kode_barang,
                'jumlah' => $bm->jumlah,
                'harga_satuan' => $bm->harga_satuan,
                'total_harga' => $bm->total_harga,
                'tanggal_masuk' => $bm->tanggal_masuk ? Carbon::parse($bm->tanggal_masuk)->format('d/m/Y') : '-',
                'pemasok' => optional($bm->pemasok)->nama_pemasok ?? '-',
            ];
        })->toArray();

        // Barang Keluar (Penjualan/Omzet)
        $barangKeluarQuery = BarangKeluar::with(['item'])
            ->where('user_id', $user->id)
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_keluar', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('tanggal_keluar', '<=', $endDate))
            ->get();

        // Nilai aset dan modal riil dari batch (FIFO), bukan rata-rata.
        $inventory = app(BatchInventoryService::class);
        $outgoingLedger = $inventory->outgoings($user->id, $endDate ? Carbon::parse($endDate)->endOfDay() : now())
            ->when($startDate, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal'] && $row['tanggal']->gte(Carbon::parse($startDate)->startOfDay())
            ))
            ->keyBy('id');

        $barangKeluarData = $barangKeluarQuery->map(function ($bk) use ($outgoingLedger) {
            $ledger = $outgoingLedger->get($bk->id);

            return [
                'nama_barang' => optional($bk->item)->nama_barang ?? '-',
                'kode_barang' => $bk->kode_barang,
                'jumlah' => $bk->jumlah_keluar,
                'harga_jual' => $bk->harga_jual ?? 0,
                'total_harga_jual' => $bk->total_harga_jual ?? 0,
                'tanggal_keluar' => $bk->tanggal_keluar ? Carbon::parse($bk->tanggal_keluar)->format('d/m/Y') : '-',
                'penerima' => $bk->penerima ?? '-',
                'batch_refs' => $ledger['batch_refs'] ?? '-',
                'modal_riil' => (float) ($ledger['modal_riil'] ?? 0),
                'laba_riil' => (float) ($ledger['laba_riil'] ?? 0),
            ];
        })->toArray();

        $nilaiAset = $inventory->stockValue($user->id, $endDate ? Carbon::parse($endDate)->endOfDay() : now());

        $exportData = [
            'total_omzet' => (float) $outgoingLedger->sum('omzet'),
            'total_modal' => (float) $outgoingLedger->sum('modal_riil'),
            'total_pembelian' => (float) $barangMasukQuery->sum('total_harga'),
            'nilai_aset' => $nilaiAset,
            'total_masuk_count' => $barangMasukQuery->count(),
            'total_keluar_count' => $barangKeluarQuery->count(),
            'barang_masuk' => $barangMasukData,
            'barang_keluar' => $barangKeluarData,
        ];

        $periodeStart = $startDate ? Carbon::parse($startDate)->format('d/m/Y') : 'Awal';
        $periodeEnd = $endDate ? Carbon::parse($endDate)->format('d/m/Y') : Carbon::now()->format('d/m/Y');

        $format = strtolower($request->input('format', 'xlsx'));
        $filename = 'Laporan_Keuangan_Bakoelkembang_'.now()->format('Ymd_His');

        if ($format === 'csv') {
            return Excel::download(
                new LaporanKeuanganExport($exportData, $periodeStart, $periodeEnd, $user->name),
                $filename.'.csv',
                \Maatwebsite\Excel\Excel::CSV
            );
        }

        return Excel::download(
            new LaporanKeuanganExport($exportData, $periodeStart, $periodeEnd, $user->name),
            $filename.'.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
