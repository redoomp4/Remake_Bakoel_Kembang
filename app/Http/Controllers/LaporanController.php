<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Lokasi;
use App\Services\BatchInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request, BatchInventoryService $inventory)
    {
        $userId = Auth::id();

        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now();

        $rows = $inventory->batches($userId, $end)
            ->filter(fn (array $row) => (int) $row['sisa_stok'] > 0)
            ->when($start, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal_masuk'] && $row['tanggal_masuk']->gte($start)
            ))
            ->when($request->filled('nama_barang'), fn ($rows) => $rows->filter(
                fn (array $row) => stripos($row['nama_barang'], (string) $request->nama_barang) !== false
            ))
            ->when($request->filled('kode_barang'), fn ($rows) => $rows->filter(
                fn (array $row) => stripos($row['kode_barang'], (string) $request->kode_barang) !== false
            ))
            ->when($request->filled('lokasi'), fn ($rows) => $rows->where('lokasi_id', (int) $request->lokasi))
            ->values();

        $sortBy = $request->sort_by ?? 'nama_barang';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $allowedSort = ['batch_code', 'tanggal_masuk', 'kode_barang', 'nama_barang', 'harga_beli', 'jumlah_masuk', 'jumlah_keluar', 'sisa_stok', 'nilai_aset', 'lokasi', 'username'];

        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'nama_barang';
        }

        $rows = $rows->sortBy(function (array $row) use ($sortBy) {
            $value = $row[$sortBy] ?? null;

            if ($value instanceof Carbon) {
                return $value->timestamp;
            }

            return is_string($value) ? mb_strtolower($value) : $value;
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage('page');

        $paginator = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $lokasis = $inventory->batches($userId, $end)
            ->pluck('lokasi_id', 'lokasi')
            ->map(fn ($id, $name) => ['id' => $id, 'nama_lokasi' => $name])
            ->values();

        return view('laporan.index', [
            'data' => $paginator,
            'lokasis' => $lokasis,
            'totalAset' => (float) $rows->sum('nilai_aset'),
        ]);
    }

    public function arus(Request $request, BatchInventoryService $inventory)
    {
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now();
        $kategori = $request->filled('kategori') ? (int) $request->kategori : null;
        $lokasi = $request->filled('lokasi') ? (int) $request->lokasi : null;
        $search = $request->filled('search') ? mb_strtolower((string) $request->search) : null;
        $userId = Auth::id();

        $snapshot = $inventory->snapshot($userId, $end);

        $arusMasuk = $snapshot['batches']
            ->when($start, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal_masuk'] && $row['tanggal_masuk']->gte($start)
            ))
            ->when($kategori, fn ($rows) => $rows->where('kategori_id', $kategori))
            ->when($lokasi, fn ($rows) => $rows->where('lokasi_id', $lokasi))
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
                'satuan' => $row['satuan'] ?? '-',
                'batch_refs' => $row['batch_code'],
                'harga_beli' => $row['harga_beli'],
                'harga_jual' => null,
                'jumlah' => $row['jumlah_masuk'],
                'omzet' => null,
                'total_nilai' => (float) ($row['jumlah_masuk'] * $row['harga_beli']),
                'modal_riil' => null,
                'laba_riil' => null,
                'lokasi' => $row['lokasi'],
                'pihak' => $row['pemasok'],
                'allocations' => [[
                    'batch_code' => $row['batch_code'],
                    'tanggal_masuk' => $row['tanggal_masuk'],
                    'jumlah' => $row['jumlah_masuk'],
                    'harga_beli' => $row['harga_beli'],
                    'subtotal' => (float) ($row['jumlah_masuk'] * $row['harga_beli']),
                ]],
            ]);

        $arusKeluar = $snapshot['outgoings']
            ->when($start, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal'] && $row['tanggal']->gte($start)
            ))
            ->when($kategori, fn ($rows) => $rows->where('kategori_id', $kategori))
            ->when($lokasi, fn ($rows) => $rows->where('lokasi_id', $lokasi))
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
                'satuan' => $row['satuan'] ?? '-',
                'batch_refs' => $row['batch_refs'] ?: '-',
                'harga_beli' => null,
                'harga_jual' => $row['harga_jual'],
                'jumlah' => $row['jumlah_keluar'],
                'omzet' => $row['omzet'],
                'total_nilai' => (float) $row['omzet'],
                'modal_riil' => $row['modal_riil'],
                'laba_riil' => $row['laba_riil'],
                'lokasi' => $row['lokasi'],
                'pihak' => $row['penerima'] ?? '-',
                'allocations' => $row['allocations'] ?? [],
            ]);

        $combined = $arusMasuk->merge($arusKeluar)
            ->sortBy(fn (array $row) => $row['tanggal']?->timestamp ?? 0)
            ->values();

        $stokPerBarang = [];
        $enhanced = collect();

        foreach ($combined as $row) {
            $kode = $row['kode_barang'];

            if (! isset($stokPerBarang[$kode])) {
                $stokPerBarang[$kode] = 0;
            }

            $jumlahMasuk = $row['jenis'] === 'Masuk' ? $row['jumlah'] : 0;
            $jumlahKeluar = $row['jenis'] === 'Keluar' ? $row['jumlah'] : 0;

            $stokPerBarang[$kode] += $jumlahMasuk - $jumlahKeluar;

            $enhanced->push(array_merge($row, [
                'jumlah_masuk' => $jumlahMasuk,
                'jumlah_keluar' => $jumlahKeluar,
                'total_barang' => $stokPerBarang[$kode],
            ]));
        }

        $totalOmzet = (float) $enhanced->where('jenis', 'Keluar')->sum('omzet');
        $totalModal = (float) $enhanced->where('jenis', 'Keluar')->sum('modal_riil');
        $totalLaba = (float) $enhanced->where('jenis', 'Keluar')->sum('laba_riil');

        $sortBy = $request->get('sort_by', 'tanggal');
        $sortDir = $request->get('sort_dir', 'asc');
        $allowedSort = ['tanggal', 'jenis', 'batch_refs', 'kode_barang', 'nama_barang', 'harga_beli', 'harga_jual', 'jumlah_masuk', 'jumlah_keluar', 'total_barang', 'omzet', 'modal_riil', 'laba_riil', 'lokasi', 'pihak'];

        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'tanggal';
        }

        $enhanced = $enhanced->sortBy(function (array $row) use ($sortBy) {
            $value = $row[$sortBy] ?? null;

            return $value instanceof Carbon ? $value->timestamp : $value;
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginated = new LengthAwarePaginator(
            $enhanced->slice(($page - 1) * $perPage, $perPage)->values()->all(),
            $enhanced->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $kategoris = Kategori::where('user_id', $userId)->orderBy('kategori')->get();
        $lokasis = Lokasi::whereIn('id', $snapshot['batches']->pluck('lokasi_id')->merge($snapshot['outgoings']->pluck('lokasi_id'))->unique())
            ->orderBy('nama_lokasi')
            ->get();

        return view('laporan.arus', compact(
            'paginated',
            'kategoris',
            'lokasis',
            'search',
            'kategori',
            'lokasi',
            'totalOmzet',
            'totalModal',
            'totalLaba'
        ) + [
            'start' => $request->start_date,
            'end' => $request->end_date,
        ]);
    }

    public function stokViewer(Request $request, BatchInventoryService $inventory)
    {
        return $this->stokSummary($request, $inventory, 'views.index');
    }

    public function stokAdmin(Request $request, BatchInventoryService $inventory)
    {
        return $this->stokSummary($request, $inventory, 'views.admin');
    }

    private function stokSummary(Request $request, BatchInventoryService $inventory, string $view)
    {
        $search = $request->filled('search') ? mb_strtolower((string) $request->search) : null;

        $rows = $inventory->batches(Auth::id())
            ->groupBy('kode_barang')
            ->map(function ($batches, $kode) {
                $latest = $batches->sortByDesc('tanggal_masuk')->first();

                return (object) [
                    'kode_barang' => $kode,
                    'nama_barang' => $latest['nama_barang'],
                    'stok_akhir' => (int) $batches->sum('sisa_stok'),
                    'username' => $latest['username'],
                ];
            })
            ->filter(fn ($row) => $row->stok_akhir > 0)
            ->when($search, fn ($rows) => $rows->filter(
                fn ($row) => str_contains(mb_strtolower($row->kode_barang), $search)
                    || str_contains(mb_strtolower($row->nama_barang), $search)
                    || str_contains(mb_strtolower($row->username), $search)
            ))
            ->values();

        $allowedSort = ['kode_barang', 'nama_barang', 'stok_akhir', 'username'];
        $sortBy = $request->get('sort_by', 'nama_barang');
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'nama_barang';
        }

        $rows = $rows->sortBy(function ($row) use ($sortBy) {
            return is_string($row->{$sortBy}) ? mb_strtolower($row->{$sortBy}) : $row->{$sortBy};
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $data = new LengthAwarePaginator(
            $rows->slice(($page - 1) * $perPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view($view, compact('data', 'sortBy', 'sortDir'));
    }
}
