<?php

namespace App\Http\Controllers;

use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Services\BatchInventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OmzetController extends Controller
{
    public function index(Request $request, BatchInventoryService $inventory)
    {
        $userId = auth()->id();
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        $data = $inventory->outgoings($userId, $end ?: now())
            ->when($start, fn ($rows) => $rows->filter(
                fn (array $row) => $row['tanggal'] && $row['tanggal']->gte($start)
            ))
            ->when($request->filled('lokasi'), fn ($rows) => $rows->where('lokasi_id', (int) $request->lokasi))
            ->when($request->filled('kondisi'), fn ($rows) => $rows->where('kondisi_id', (int) $request->kondisi))
            ->when($request->filled('search'), function ($rows) use ($request) {
                $search = mb_strtolower((string) $request->search);

                return $rows->filter(fn (array $row) => str_contains(mb_strtolower($row['nama_barang']), $search)
                    || str_contains(mb_strtolower($row['kode_barang']), $search));
            })
            ->map(fn (array $row) => [
                'tanggal' => $row['tanggal'],
                'nama_barang' => $row['nama_barang'],
                'kode_barang' => $row['kode_barang'],
                'lokasi' => $row['lokasi'],
                'kondisi' => $row['kondisi'],
                'satuan' => $row['satuan'] ?? '-',
                'jumlah_keluar' => $row['jumlah_keluar'],
                'harga_jual' => $row['harga_jual'],
                'omzet_item' => $row['omzet'],
                'batch_refs' => $row['batch_refs'],
                'allocations' => $row['allocations'] ?? [],
                'modal_riil' => $row['modal_riil'],
                'laba_riil' => $row['laba_riil'],
            ]);

        $sortBy = $request->get('sort_by', 'tanggal');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSort = ['tanggal', 'nama_barang', 'lokasi', 'kondisi', 'jumlah_keluar', 'harga_jual', 'omzet_item', 'modal_riil', 'laba_riil'];

        if (! in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'tanggal';
        }

        $data = $data->sortBy(function (array $row) use ($sortBy) {
            $value = $row[$sortBy] ?? null;

            return $value instanceof Carbon ? $value->timestamp : (is_string($value) ? mb_strtolower($value) : $value);
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        $total_omzet = (float) $data->sum('omzet_item');
        $total_modal = (float) $data->sum('modal_riil');
        $total_laba = (float) $data->sum('laba_riil');

        $lokasiIds = $inventory->outgoings($userId, $end ?: now())->pluck('lokasi_id')->unique();
        $kondisiIds = $inventory->outgoings($userId, $end ?: now())->pluck('kondisi_id')->unique();

        $lokasis = Lokasi::whereIn('id', $lokasiIds)->orderBy('nama_lokasi')->get();
        $kondisis = Kondisi::whereIn('id', $kondisiIds)->orderBy('nama_kondisi')->get();

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage('page');
        $omzetData = new LengthAwarePaginator(
            $data->forPage($page, $perPage)->values(),
            $data->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('omzet.index', [
            'data' => $omzetData,
            'omzetData' => $omzetData,
            'lokasis' => $lokasis,
            'kondisis' => $kondisis,
            'total_omzet' => $total_omzet,
            'total_modal' => $total_modal,
            'total_laba' => $total_laba,
        ]);
    }
}
