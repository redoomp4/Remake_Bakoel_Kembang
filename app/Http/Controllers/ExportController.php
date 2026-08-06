<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response; 
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Lokasi;
use App\Models\Kategori;
use App\Models\Kondisi;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanStokExport;
use App\Exports\LaporanArusExport;
use App\Exports\OmzetExport;
use App\Exports\LaporanAsetExport;
use App\Exports\LaporanKeuanganExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Helper periode:
     * - Jika user mengisi start/end → pakai itu.
     * - Jika tidak, gunakan $fallback() untuk min/max dari dataset.
     * - Jika data kosong → pakai hari ini agar tidak tampil "- s/d -".
     *
     * @param ?string $start
     * @param ?string $end
     * @param \Closure():array{0:?string,1:?string} $fallback
     * @param string $tz
     * @return array{0:Carbon,1:Carbon}
     */

    private function normalizeDate(?string $value): ?string
    {
        if (!$value) return null;
        $value = trim($value);

        if (str_contains($value, '/')) {
            try { return Carbon::createFromFormat('d/m/Y', $value)->toDateString(); }
            catch (\Throwable $e) { /* fallback */ }
        }
        try { return Carbon::parse($value)->toDateString(); }
        catch (\Throwable $e) { return null; }
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

    public function laporan(Request $request)
    {
        $user = auth()->user();

        // Filter teks & lokasi
        $nama     = $request->filled('nama_barang') ? (string)$request->nama_barang : null;
        $kode     = $request->filled('kode_barang') ? (string)$request->kode_barang : null;
        $lokasiId = $request->filled('lokasi')      ? (int)$request->lokasi        : null;

        // Normalisasi tanggal (mendukung dd/mm/YYYY)
        $startDate = $this->normalizeDate($request->input('start_date'));
        $endDate   = $this->normalizeDate($request->input('end_date'));

        // ===== BARANG MASUK =====
        $barangMasuk = BarangMasuk::query()
            ->with(['item', 'lokasi', 'user'])
            ->where('user_id', $user->id)
            ->when($lokasiId, fn ($q) => $q->where('id_lokasi', $lokasiId))
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_masuk', '>=', $startDate))
            ->when($endDate,   fn ($q) => $q->whereDate('tanggal_masuk', '<=', $endDate))
            ->whereHas('item', function ($q) use ($nama, $kode) {
                $q->when($nama, fn ($qq) => $qq->where('nama_barang', 'like', '%'.$nama.'%'))
                ->when($kode, fn ($qq) => $qq->where('kode_barang', 'like', '%'.$kode.'%'));
            })
            ->get()
            ->map(function ($bm) {
                return [
                    'kode_barang' => $bm->item->kode_barang ?? '-',
                    'nama_barang' => $bm->item->nama_barang ?? '-',
                    'harga_dasar' => $bm->item->harga_dasar ?? 0,
                    'lokasi'      => optional($bm->lokasi)->nama_lokasi ?? '-',
                    'kondisi'     => $bm->kondisi ?? null,
                    'username'    => optional($bm->user)->username ?? null,
                    'jenis'       => 'Masuk',
                    'jumlah'      => (float) $bm->jumlah,
                ];
            });

        // ===== BARANG KELUAR =====
        $barangKeluar = BarangKeluar::query()
            ->with(['item', 'lokasi', 'user'])
            ->where('user_id', $user->id)
            ->when($lokasiId, fn ($q) => $q->where('id_lokasi', $lokasiId))
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_keluar', '>=', $startDate))
            ->when($endDate,   fn ($q) => $q->whereDate('tanggal_keluar', '<=', $endDate))
            ->whereHas('item', function ($q) use ($nama, $kode) {
                $q->when($nama, fn ($qq) => $qq->where('nama_barang', 'like', '%'.$nama.'%'))
                ->when($kode, fn ($qq) => $qq->where('kode_barang', 'like', '%'.$kode.'%'));
            })
            ->get()
            ->map(function ($bk) {
                return [
                    'kode_barang' => $bk->item->kode_barang ?? '-',
                    'nama_barang' => $bk->item->nama_barang ?? '-',
                    'harga_dasar' => $bk->item->harga_dasar ?? 0,
                    'lokasi'      => optional($bk->lokasi)->nama_lokasi ?? '-',
                    'kondisi'     => $bk->kondisi ?? null,
                    'username'    => optional($bk->user)->username ?? null,
                    'jenis'       => 'Keluar',
                    'jumlah'      => (float) $bk->jumlah_keluar,
                ];
            });

        // ===== Merge & ringkas per Kode|Lokasi|Kondisi|Username =====
        $merged = $barangMasuk->merge($barangKeluar);

        $grouped = $merged->groupBy(
            fn (array $row) =>
                ($row['kode_barang'] ?? '-') . '|' .
                ($row['lokasi'] ?? '-') . '|' .
                ($row['kondisi'] ?? '-') . '|' .
                ($row['username'] ?? '-')
        );

        $collection = $grouped->map(function (Collection $rows) {
            $first  = $rows->first();
            $masuk  = $rows->where('jenis', 'Masuk')->sum('jumlah');
            $keluar = $rows->where('jenis', 'Keluar')->sum('jumlah');

            return [
                'kode_barang'  => $first['kode_barang'],
                'nama_barang'  => $first['nama_barang'],
                'harga_dasar'  => $first['harga_dasar'] ?? 0,
                'total_masuk'  => $masuk,
                'total_keluar' => $keluar,
                'stok_akhir'   => $masuk - $keluar,
                'lokasi'       => $first['lokasi'],
                'kondisi'      => $first['kondisi'] ?? '-',
                'username'     => $first['username'] ?? '-',
            ];
        })->values();

        // Sorting
        $sortBy  = $request->get('sort_by', 'kode_barang');
        $sortDesc = strtolower($request->get('sort_dir', 'asc')) === 'desc';
        $collection = $collection->sortBy($sortBy, SORT_REGULAR, $sortDesc)->values();

        // Pagination
        $perPage = 15;
        $current = LengthAwarePaginator::resolveCurrentPage();
        $items   = $collection->slice(($current - 1) * $perPage, $perPage)->values();
        $data    = new LengthAwarePaginator(
            $items, $collection->count(), $perPage, $current,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Dropdown lokasi
        $lokasis = Lokasi::orderBy('nama_lokasi')->get(['id','nama_lokasi'])->toArray();

        return view('laporan.index', compact('data', 'lokasis'));
    }



    // =========================
    // === Helper yang dipakai ==
    // =========================

    /**
 * Satukan transaksi Masuk & Keluar (dipakai export), filter di DB dengan whereDate (partial range OK).
 *
 * @return \Illuminate\Support\Collection<int, array{
 *  kode_barang:string, nama_barang:string, harga_dasar:float|int,
 *  lokasi:string, kondisi:?string, username:?string,
 *  jenis:"Masuk"|"Keluar", jumlah:float|int, tanggal:?Carbon
 * }>
 */
private function fetchUnifiedTransactions(
    int $userId,
    ?string $nama,
    ?string $kode,
    ?int $lokasiId,
    ?string $start,
    ?string $end,
    bool $includeDates = false
    ): Collection {
        $startDate = $this->normalizeDate($start);
        $endDate   = $this->normalizeDate($end);

        $masuk = BarangMasuk::query()
            ->with(['item', 'lokasi', 'user'])
            ->where('user_id', $userId)
            ->when($lokasiId, fn ($q) => $q->where('id_lokasi', $lokasiId))
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_masuk', '>=', $startDate))
            ->when($endDate,   fn ($q) => $q->whereDate('tanggal_masuk', '<=', $endDate))
            ->whereHas('item', function ($q) use ($nama, $kode) {
                $q->when($nama, fn ($qq) => $qq->where('nama_barang', 'like', '%'.$nama.'%'))
                ->when($kode, fn ($qq) => $qq->where('kode_barang', 'like', '%'.$kode.'%'));
            })
            ->get()
            ->map(function ($bm) use ($includeDates) {
                return [
                    'kode_barang' => $bm->item->kode_barang ?? '-',
                    'nama_barang' => $bm->item->nama_barang ?? '-',
                    'harga_dasar' => $bm->item->harga_dasar ?? 0,
                    'lokasi'      => optional($bm->lokasi)->nama_lokasi ?? '-',
                    'kondisi'     => $bm->kondisi ?? null,
                    'username'    => optional($bm->user)->username ?? null,
                    'jenis'       => 'Masuk',
                    'jumlah'      => (float) $bm->jumlah,
                    'tanggal'     => $includeDates && $bm->tanggal_masuk
                                    ? Carbon::parse($bm->tanggal_masuk)
                                    : null,
                ];
            });

        $keluar = BarangKeluar::query()
            ->with(['item', 'lokasi', 'user'])
            ->where('user_id', $userId)
            ->when($lokasiId, fn ($q) => $q->where('id_lokasi', $lokasiId))
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_keluar', '>=', $startDate))
            ->when($endDate,   fn ($q) => $q->whereDate('tanggal_keluar', '<=', $endDate))
            ->whereHas('item', function ($q) use ($nama, $kode) {
                $q->when($nama, fn ($qq) => $qq->where('nama_barang', 'like', '%'.$nama.'%'))
                ->when($kode, fn ($qq) => $qq->where('kode_barang', 'like', '%'.$kode.'%'));
            })
            ->get()
            ->map(function ($bk) use ($includeDates) {
                return [
                    'kode_barang' => $bk->item->kode_barang ?? '-',
                    'nama_barang' => $bk->item->nama_barang ?? '-',
                    'harga_dasar' => $bk->item->harga_dasar ?? 0,
                    'lokasi'      => optional($bk->lokasi)->nama_lokasi ?? '-',
                    'kondisi'     => $bk->kondisi ?? null,
                    'username'    => optional($bk->user)->username ?? null,
                    'jenis'       => 'Keluar',
                    'jumlah'      => (float) $bk->jumlah_keluar,
                    'tanggal'     => $includeDates && $bk->tanggal_keluar
                                    ? Carbon::parse($bk->tanggal_keluar)
                                    : null,
                ];
            });

        return $masuk->merge($keluar);
    }


    /**
     * Ringkas stok per kombinasi kode|lokasi|kondisi|username.
     *
     * @param Collection<int, array> $merged
     * @return Collection<int, array{
     *   kode_barang:string, nama_barang:string, harga_dasar:float|int,
     *   total_masuk:float|int, total_keluar:float|int, stok_akhir:float|int,
     *   lokasi:string, kondisi:?string, username:?string
     * }>
     */
    
    // =========================
    // ====== LAPORAN STOK =====
    // =========================

    public function exportPdf(Request $request): Response
    {
        $user = auth()->user();

        $nama     = $request->filled('nama_barang') ? (string)$request->nama_barang : null;
        $kode     = $request->filled('kode_barang') ? (string)$request->kode_barang : null;
        $lokasiId = $request->filled('lokasi') ? (int)$request->lokasi : null;

        $lokasiNama = $lokasiId ? optional(Lokasi::find($lokasiId))->nama_lokasi : null;

        // Satukan transaksi Masuk/Keluar dengan struktur sama, filter di DB
        $merged = $this->fetchUnifiedTransactions(
            userId: $user->id,
            nama: $nama,
            kode: $kode,
            lokasiId: $lokasiId,
            start: $request->start_date,
            end: $request->end_date,
            includeDates: true
        );

        // Ringkas per kode|lokasi|kondisi|username
        $data = $this->summarizeStock($merged)
            ->filter(fn (array $row) => $row['stok_akhir'] > 0)
            ->values();

        // Periode
        [$periodeStart, $periodeEnd] = $this->resolvePeriod(
            $request->start_date,
            $request->end_date,
            function () use ($merged) {
                $min = $merged->pluck('tanggal')->filter()->min();
                $max = $merged->pluck('tanggal')->filter()->max();
                return [$min?->toDateString(), $max?->toDateString()];
            }
        );

        $viewData = [
            'data'         => $data,
            'nama'         => $user->name,
            'username'     => $user->username,
            'tanggalAwal'  => $periodeStart->format('d/m/Y'),
            'tanggalAkhir' => $periodeEnd->format('d/m/Y'),
            'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
            'filters'      => [
                'Nama Barang' => $nama,
                'Kode Barang' => $kode,
                'Lokasi'      => $lokasiNama,
            ],
        ];

        return Pdf::loadView('laporan.print', $viewData)->stream('laporan_stok_barang.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $user = auth()->user();

        $merged = $this->fetchUnifiedTransactions(
            userId: $user->id,
            nama: $request->filled('nama_barang') ? $request->nama_barang : null,
            kode: $request->filled('kode_barang') ? $request->kode_barang : null,
            lokasiId: $request->filled('lokasi') ? (int)$request->lokasi : null,
            start: $request->start_date,
            end: $request->end_date,
            includeDates: false
        );

        $data = $this->summarizeStock($merged)->values();

        return Excel::download(new \App\Exports\ArrayExport($data->toArray()), 'laporan_stok_barang.xlsx');
    }

    // =========================
    // ====== ARUS BARANG ======
    // =========================

    public function exportArusPdf(Request $request): Response
    {
        $user = auth()->user();

        $start     = $request->filled('start_date') ? (string)$request->start_date : null;
        $end       = $request->filled('end_date')   ? (string)$request->end_date   : null;
        $kategori  = $request->filled('kategori')   ? (int)$request->kategori      : null;
        $lokasiId  = $request->filled('lokasi')     ? (int)$request->lokasi        : null;
        $search    = $request->filled('search')     ? (string)$request->search     : null;

        $startDate = $start ? Carbon::parse($start)->startOfDay() : null;
        $endDate   = $end   ? Carbon::parse($end)->endOfDay()     : null;

        // ARUS MASUK
        $arusMasuk = BarangMasuk::query()
            ->with([
                // HAPUS "id" di sini:
                'item:kode_barang,nama_barang,harga_dasar,id_kategori',
                'lokasi:id,nama_lokasi'
            ])
            ->where('user_id', $user->id)
            ->when($lokasiId, fn (Builder $q) => $q->where('id_lokasi', $lokasiId))
            ->when($startDate && $endDate, fn (Builder $q) => $q->whereBetween('tanggal_masuk', [$startDate, $endDate]))
            ->whereHas('item', function (Builder $q) use ($kategori, $search) {
                $q->when($kategori, fn (Builder $qq) => $qq->where('id_kategori', $kategori))
                ->when($search, function (Builder $qq) use ($search) {
                    $qq->where(function (Builder $qqq) use ($search) {
                        $qqq->where('nama_barang', 'like', '%'.$search.'%')
                            ->orWhere('kode_barang', 'like', '%'.$search.'%');
                    });
                });
            })
            ->when($search, fn (Builder $q) =>
                $q->orWhere('pemasok', 'like', '%'.$search.'%')
            )
            ->get()
            ->map(function ($bm) {
                return [
                    'tanggal'     => $bm->tanggal_masuk ? Carbon::parse($bm->tanggal_masuk) : null,
                    'jenis'       => 'Masuk',
                    'kode_barang' => $bm->item->kode_barang ?? '-',
                    'nama_barang' => $bm->item->nama_barang ?? '-',
                    'harga_dasar' => $bm->item->harga_dasar ?? 0,
                    'jumlah'      => (float) $bm->jumlah,
                    'lokasi'      => optional($bm->lokasi)->nama_lokasi ?? '-',
                    'pihak'       => is_object($bm->pemasok) ? ($bm->pemasok->nama_pemasok ?? '-') : ($bm->pemasok ?? '-'),
                ];
            });

        // ARUS KELUAR
        $arusKeluar = BarangKeluar::query()
            ->with([
                // HAPUS "id" di sini juga:
                'item:kode_barang,nama_barang,harga_dasar,id_kategori',
                'lokasi:id,nama_lokasi'
            ])
            ->where('user_id', $user->id)
            ->when($lokasiId, fn (Builder $q) => $q->where('id_lokasi', $lokasiId))
            ->when($startDate && $endDate, fn (Builder $q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate]))
            ->whereHas('item', function (Builder $q) use ($kategori, $search) {
                $q->when($kategori, fn (Builder $qq) => $qq->where('id_kategori', $kategori))
                ->when($search, function (Builder $qq) use ($search) {
                    $qq->where(function (Builder $qqq) use ($search) {
                        $qqq->where('nama_barang', 'like', '%'.$search.'%')
                            ->orWhere('kode_barang', 'like', '%'.$search.'%');
                    });
                });
            })
            ->when($search, fn (Builder $q) =>
                $q->orWhere('penerima', 'like', '%'.$search.'%')
            )
            ->get()
            ->map(function ($bk) {
                return [
                    'tanggal'     => $bk->tanggal_keluar ? Carbon::parse($bk->tanggal_keluar) : null,
                    'jenis'       => 'Keluar',
                    'kode_barang' => $bk->item->kode_barang ?? '-',
                    'nama_barang' => $bk->item->nama_barang ?? '-',
                    'harga_dasar' => $bk->item->harga_dasar ?? 0,
                    'jumlah'      => (float) $bk->jumlah_keluar,
                    'lokasi'      => optional($bk->lokasi)->nama_lokasi ?? '-',
                    'pihak'       => $bk->penerima ?? '-',
                ];
            });


        // Gabungkan, urutkan, dan hitung running stock per kode
        $combined = $arusMasuk->merge($arusKeluar)
            ->sortBy(fn (array $r) => $r['tanggal'] instanceof Carbon ? $r['tanggal']->timestamp : 0)
            ->values();

        $running = [];
        $enhanced = $combined->map(function (array $row) use (&$running) {
            $kode = $row['kode_barang'];
            $running[$kode] = $running[$kode] ?? 0;

            $masuk  = $row['jenis'] === 'Masuk'  ? $row['jumlah'] : 0;
            $keluar = $row['jenis'] === 'Keluar' ? $row['jumlah'] : 0;

            $running[$kode] += $masuk - $keluar;

            return $row + [
                'jumlah_masuk'  => $masuk,
                'jumlah_keluar' => $keluar,
                'total_barang'  => $running[$kode],
            ];
        });

        // Periode
        [$periodeStart, $periodeEnd] = $this->resolvePeriod(
            $start,
            $end,
            function () use ($enhanced) {
                $min = $enhanced->pluck('tanggal')->filter()->min();
                $max = $enhanced->pluck('tanggal')->filter()->max();
                return [$min?->toDateString(), $max?->toDateString()];
            }
        );

        $filters = [
            'Kategori'  => $request->filled('kategori') ? optional(Kategori::find((int)$request->kategori))->nama_kategori : null,
            'Lokasi'    => $request->filled('lokasi')   ? optional(Lokasi::find((int)$request->lokasi))->nama_lokasi     : null,
            'Pencarian' => $search,
        ];

        return Pdf::loadView('laporan.arus_print', [
            'data'         => $enhanced,
            'username'     => $user->username,
            'nama'         => $user->name,
            'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
            'tanggalAwal'  => $periodeStart->format('d/m/Y'),
            'tanggalAkhir' => $periodeEnd->format('d/m/Y'),
            'filters'      => $filters,
        ])->stream('laporan_arus_barang.pdf');
    }

    public function exportArusExcel(Request $request): BinaryFileResponse
    {
        return Excel::download(new LaporanArusExport($request), 'laporan_arus_barang.xlsx');
    }

    // =========================
    // ========= OMZET =========
    // =========================

    public function exportOmzetPdf(Request $request): Response
{
    $user = auth()->user();

    // Normalisasi tanggal agar aman untuk whereDate (partial range OK)
    $startDate = $request->filled('start_date')
        ? Carbon::parse($request->start_date)->toDateString()
        : null;
    $endDate = $request->filled('end_date')
        ? Carbon::parse($request->end_date)->toDateString()
        : null;

    $q = BarangKeluar::query()
        ->with([
            // HAPUS "id" dari eager-load item (tabel items tidak punya kolom id)
            'item:kode_barang,nama_barang',
            'lokasi:id,nama_lokasi',
            'kondisi:id,nama_kondisi',
        ])
        ->where('user_id', $user->id)
        ->when($request->filled('lokasi'),  fn (Builder $qq) => $qq->where('id_lokasi',  (int)$request->lokasi))
        ->when($request->filled('kondisi'), fn (Builder $qq) => $qq->where('id_kondisi', (int)$request->kondisi))
        ->when($request->filled('search'), function (Builder $qq) use ($request) {
            $search = (string)$request->search;
            $qq->whereHas('item', fn (Builder $qi) =>
                $qi->where('nama_barang', 'like', "%{$search}%")
                   ->orWhere('kode_barang', 'like', "%{$search}%")
            );
        })
        // Partial date range: start saja / end saja juga tetap difilter
        ->when($startDate, fn ($qq) => $qq->whereDate('tanggal_keluar', '>=', $startDate))
        ->when($endDate,   fn ($qq) => $qq->whereDate('tanggal_keluar', '<=', $endDate));

    $data = $q->get()->map(function ($row) {
        $omzet = (float)$row->jumlah_keluar * (float)$row->harga_jual;
        return [
            'tanggal'       => $row->tanggal_keluar,
            'nama_barang'   => optional($row->item)->nama_barang ?? '-',
            'lokasi'        => optional($row->lokasi)->nama_lokasi ?? '-',
            'kondisi'       => optional($row->kondisi)->nama_kondisi ?? '-',
            'jumlah_keluar' => $row->jumlah_keluar,
            'harga_jual'    => $row->harga_jual,
            'omzet_item'    => $omzet,
        ];
    });

    $total_omzet = $data->sum('omzet_item');

    // Tentukan periode untuk header laporan (pakai filter; jika kosong, fallback ke min/max hasil query)
    [$periodeStart, $periodeEnd] = $this->resolvePeriod(
        $request->start_date,
        $request->end_date,
        function () use ($q) {
            $qq = clone $q;
            $min = $qq->min('tanggal_keluar');
            $max = (clone $q)->max('tanggal_keluar');
            return [$min, $max];
        }
    );

    $filters = [
        'Tanggal Mulai'   => $periodeStart->format('d/m/Y'),
        'Tanggal Selesai' => $periodeEnd->format('d/m/Y'),
        'Cari Barang'     => $request->search,
        'Lokasi'          => $request->filled('lokasi')
                                ? optional(Lokasi::find((int)$request->lokasi))->nama_lokasi
                                : null,
        'Kondisi'         => $request->filled('kondisi')
                                ? optional(Kondisi::find((int)$request->kondisi))->nama_kondisi
                                : null,
    ];

    return Pdf::loadView('omzet.print', [
        'data'         => $data,
        'total_omzet'  => $total_omzet,
        'username'     => $user->username,
        'nama'         => $user->name,
        'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
        'tanggalAwal'  => $filters['Tanggal Mulai'],
        'tanggalAkhir' => $filters['Tanggal Selesai'],
        'filters'      => $filters,
    ])->stream('laporan_omzet.pdf');
}


    public function exportOmzetExcel(Request $request): BinaryFileResponse
    {
        // Tetap gunakan export class agar konsisten
        return Excel::download(new OmzetExport($request), 'laporan_omzet.xlsx');
    }

    // =========================
    // ========= ASET ==========
    // =========================

    public function exportAsetExcel(Request $request): BinaryFileResponse
    {
        return Excel::download(new LaporanAsetExport($request), 'laporan_aset.xlsx');
    }

    public function exportAsetPdf(Request $request): Response
    {
        $export = new LaporanAsetExport($request);
        $data   = $export->collection();

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
                    $bm->where('id_lokasi', (int)$request->lokasi);
                    $bk->where('id_lokasi', (int)$request->lokasi);
                }
                if ($request->filled('kondisi')) {
                    $bm->where('id_kondisi', (int)$request->kondisi);
                    $bk->where('id_kondisi', (int)$request->kondisi);
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
            'Nama Barang'     => $request->nama_barang,
            'Lokasi'          => $request->filled('lokasi')  ? optional(Lokasi::find((int)$request->lokasi))->nama_lokasi : null,
            'Kondisi'         => $request->filled('kondisi') ? optional(Kondisi::find((int)$request->kondisi))->nama_kondisi : null,
            'Tanggal Mulai'   => $periodeStart->format('d/m/Y'),
            'Tanggal Selesai' => $periodeEnd->format('d/m/Y'),
        ];

        return Pdf::loadView('aset.aset_print', [
            'data'         => $data,
            'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
            'tanggalAwal'  => $filters['Tanggal Mulai'],
            'tanggalAkhir' => $filters['Tanggal Selesai'],
            'nama'         => auth()->user()->name,
            'username'     => auth()->user()->username,
            'filters'      => $filters,
        ])->stream('laporan_aset.pdf');
    }

    // =========================
    // ======= HELPERS =========
    // =========================

    /**
     * Satukan transaksi masuk & keluar ke struktur seragam.
     *
     * @return Collection<int, array{
     *   kode_barang:string, nama_barang:string, harga_dasar:float|int,
     *   lokasi:string, kondisi:string|null, username:string|null,
     *   jenis:"Masuk"|"Keluar", jumlah:float|int, tanggal:?Carbon
     * }>
     */
    
    private function summarizeStock(Collection $merged): Collection
    {
        $grouped = $merged->groupBy(
            fn (array $row) => $row['kode_barang'].'|'.$row['lokasi'].'|'.($row['kondisi'] ?? '-').'|'.($row['username'] ?? '-')
        );

        return $grouped->map(function (Collection $rows) {
            /** @var array $first */
            $first = $rows->first();
            $masuk  = $rows->where('jenis', 'Masuk')->sum('jumlah');
            $keluar = $rows->where('jenis', 'Keluar')->sum('jumlah');

            return [
                'kode_barang'  => $first['kode_barang'],
                'nama_barang'  => $first['nama_barang'],
                'harga_dasar'  => $first['harga_dasar'] ?? 0,
                'total_masuk'  => $masuk,
                'total_keluar' => $keluar,
                'stok_akhir'   => $masuk - $keluar,
                'lokasi'       => $first['lokasi'],
                'kondisi'      => $first['kondisi'] ?? '-',
                'username'     => $first['username'] ?? '-',
            ];
        });
    }

    /**
     * Export Laporan Keuangan (Laba Rugi) ke Excel
     */
    public function exportKeuanganExcel(Request $request)
    {
        $user = auth()->user();

        $startDate = $this->normalizeDate($request->input('start_date'));
        $endDate   = $this->normalizeDate($request->input('end_date'));

        // Barang Masuk (Modal/Pembelian)
        $barangMasukQuery = BarangMasuk::with(['item', 'pemasok'])
            ->where('user_id', $user->id)
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_masuk', '>=', $startDate))
            ->when($endDate,   fn ($q) => $q->whereDate('tanggal_masuk', '<=', $endDate))
            ->get();

        $barangMasukData = $barangMasukQuery->map(function ($bm) {
            return [
                'nama_barang'   => optional($bm->item)->nama_barang ?? '-',
                'kode_barang'   => $bm->kode_barang,
                'jumlah'        => $bm->jumlah,
                'harga_satuan'  => $bm->harga_satuan,
                'total_harga'   => $bm->total_harga,
                'tanggal_masuk' => $bm->tanggal_masuk ? Carbon::parse($bm->tanggal_masuk)->format('d/m/Y') : '-',
                'pemasok'       => optional($bm->pemasok)->nama_pemasok ?? '-',
            ];
        })->toArray();

        // Barang Keluar (Penjualan/Omzet)
        $barangKeluarQuery = BarangKeluar::with(['item'])
            ->where('user_id', $user->id)
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_keluar', '>=', $startDate))
            ->when($endDate,   fn ($q) => $q->whereDate('tanggal_keluar', '<=', $endDate))
            ->get();

        $barangKeluarData = $barangKeluarQuery->map(function ($bk) {
            return [
                'nama_barang'      => optional($bk->item)->nama_barang ?? '-',
                'kode_barang'      => $bk->kode_barang,
                'jumlah'           => $bk->jumlah_keluar,
                'harga_jual'       => $bk->harga_jual ?? 0,
                'total_harga_jual' => $bk->total_harga_jual ?? 0,
                'tanggal_keluar'   => $bk->tanggal_keluar ? Carbon::parse($bk->tanggal_keluar)->format('d/m/Y') : '-',
                'penerima'         => $bk->penerima ?? '-',
            ];
        })->toArray();

        // Nilai Aset Inventaris
        $nilaiAset = Item::where('user_id', $user->id)->sum('harga_dasar');

        $exportData = [
            'total_omzet'        => $barangKeluarQuery->sum('total_harga_jual'),
            'total_modal'        => $barangMasukQuery->sum('total_harga'),
            'nilai_aset'         => $nilaiAset,
            'total_masuk_count'  => $barangMasukQuery->count(),
            'total_keluar_count' => $barangKeluarQuery->count(),
            'barang_masuk'       => $barangMasukData,
            'barang_keluar'      => $barangKeluarData,
        ];

        $periodeStart = $startDate ? Carbon::parse($startDate)->format('d/m/Y') : 'Awal';
        $periodeEnd   = $endDate   ? Carbon::parse($endDate)->format('d/m/Y')   : Carbon::now()->format('d/m/Y');

        return Excel::download(
            new LaporanKeuanganExport($exportData, $periodeStart, $periodeEnd, $user->name),
            'Laporan_Keuangan_Bakoelkembang_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
