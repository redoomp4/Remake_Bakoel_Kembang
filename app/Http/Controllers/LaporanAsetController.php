<?php

namespace App\Http\Controllers;

use App\Services\BatchInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class LaporanAsetController extends Controller
{
    public function index(Request $request, BatchInventoryService $inventory)
    {
        $userId = Auth::id();

        $start = $this->parseDate($request->start_date, true);
        $end = $this->parseDate($request->end_date, false);

        $rows = $inventory->batches($userId, $end ?: now())
            ->filter(fn (array $row) => (int) $row['sisa_stok'] > 0)
            ->when($start, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal_masuk'] && $row['tanggal_masuk']->gte($start)
            ))
            ->when($request->filled('lokasi'), fn ($rows) => $rows->where('lokasi', $request->lokasi))
            ->when($request->filled('kondisi'), fn ($rows) => $rows->where('kondisi', $request->kondisi))
            ->when($request->filled('nama_barang'), fn ($rows) => $rows->filter(
                fn (array $row) => stripos($row['nama_barang'], (string) $request->nama_barang) !== false
            ))
            ->map(fn (array $row) => [
                'barang_masuk_id' => $row['barang_masuk_id'],
                'batch_code' => $row['batch_code'],
                'catatan' => $row['catatan'] ?? null,
                'tanggal_masuk' => $row['tanggal_masuk'],
                'nama_barang' => $row['nama_barang'],
                'kode_barang' => $row['kode_barang'],
                'satuan' => $row['satuan'],
                'lokasi' => $row['lokasi'],
                'kondisi' => $row['kondisi'],
                'pemasok' => $row['pemasok'],
                'jumlah_masuk' => $row['jumlah_masuk'],
                'jumlah_keluar' => $row['jumlah_keluar'],
                'stok_akhir' => $row['sisa_stok'],
                'sisa_stok' => $row['sisa_stok'],
                'harga_beli' => $row['harga_beli'],
                'jumlah_aset' => $row['nilai_aset'],
                'nilai_aset' => $row['nilai_aset'],
            ]);

        $groups = $rows
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
                    'kondisi' => $batches->pluck('kondisi')->filter()->unique()->implode(', '),
                    'total_stok' => (int) $batches->sum('stok_akhir'),
                    'total_aset' => (float) $batches->sum('jumlah_aset'),
                    'batch_count' => $batches->count(),
                    'batches' => $ordered->all(),
                ];
            })
            ->values();

        $sortBy = $request->sort_by ?? 'nama_barang';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $allowedSort = ['kode_barang', 'nama_barang', 'total_stok', 'total_aset', 'batch_count'];

        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'nama_barang';
        }

        $groups = $groups->sortBy(function (array $row) use ($sortBy) {
            $value = $row[$sortBy] ?? null;

            return is_string($value) ? mb_strtolower($value) : $value;
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        $totalAset = (float) $groups->sum('total_aset');
        $totalBatches = (int) $groups->sum('batch_count');

        $allBatches = $inventory->batches($userId, $end ?: now())
            ->filter(fn (array $row) => (int) $row['sisa_stok'] > 0);

        $listLokasi = $allBatches->pluck('lokasi')->unique()->sort()->values();
        $listKondisi = $allBatches->pluck('kondisi')->unique()->sort()->values();

        $perPage = (int) $request->get('per_page', 10);
        $page = LengthAwarePaginator::resolveCurrentPage();

        $paginated = new LengthAwarePaginator(
            $groups->forPage($page, $perPage)->values(),
            $groups->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $filters = collect([
            'Nama Barang' => $request->nama_barang,
            'Lokasi' => $request->lokasi,
            'Kondisi' => $request->kondisi,
        ]);

        if ($start) {
            $filters->put('Tanggal Mulai', $start->format('d/m/Y'));
        }
        if ($end) {
            $filters->put('Tanggal Selesai', $end->format('d/m/Y'));
        }

        return view('aset.index', [
            'grouped' => $paginated,
            'totalAset' => $totalAset,
            'totalBatches' => $totalBatches,
            'listLokasi' => $listLokasi,
            'listKondisi' => $listKondisi,
            'start' => $request->start_date,
            'end' => $request->end_date,
            'filters' => $filters,
        ]);
    }

    private function parseDate(?string $value, bool $startOfDay = true): ?Carbon
    {
        if (! $value) {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                return $startOfDay ? $date->startOfDay() : $date->endOfDay();
            } catch (\Throwable $e) {
                // Coba format berikutnya.
            }
        }

        return null;
    }
}
