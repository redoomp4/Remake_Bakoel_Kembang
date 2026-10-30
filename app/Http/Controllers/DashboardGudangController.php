<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Services\BatchInventoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardGudangController extends Controller
{
    public function index(BatchInventoryService $inventory)
    {
        $userId = Auth::id();

        $today = Carbon::today();
        $awalBulan = $today->copy()->startOfMonth();
        $akhirBulan = $today->copy()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | 1. LEDGER KEUANGAN RIIL PER BATCH (FIFO)
        |--------------------------------------------------------------------------
        */

        $ledger = $inventory->outgoings($userId, now());
        $ledgerHariIni = $ledger->filter(fn (array $row) => $row['tanggal'] && $row['tanggal']->isSameDay($today));
        $ledgerBulanIni = $ledger->filter(fn (array $row) => $row['tanggal'] && $row['tanggal']->between($awalBulan, $akhirBulan));

        $omzetHariIni = (float) $ledgerHariIni->sum('omzet');
        $transaksiKeluarHariIni = $ledgerHariIni->count();

        $hppHariIni = (float) $ledgerHariIni->sum('modal_riil');
        $labaKotorHariIni = $omzetHariIni - $hppHariIni;

        $marginHariIni = $omzetHariIni > 0
            ? ($labaKotorHariIni / $omzetHariIni) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 2. NILAI PERSEDIAAN DARI SISA BATCH RIIL
        |--------------------------------------------------------------------------
        */

        $nilaiPersediaan = $inventory->stockValue($userId, now());

        /*
        |--------------------------------------------------------------------------
        | 3. DATA BULAN INI
        |--------------------------------------------------------------------------
        */

        $omzetBulanIni = (float) $ledgerBulanIni->sum('omzet');
        $hppBulanIni = (float) $ledgerBulanIni->sum('modal_riil');

        $labaBulanIni = $omzetBulanIni - $hppBulanIni;

        $marginBulanIni = $omzetBulanIni > 0
            ? ($labaBulanIni / $omzetBulanIni) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 5. JUMLAH TRANSAKSI BARANG MASUK / KELUAR HARI INI
        |--------------------------------------------------------------------------
        */

        $masukHariIni = BarangMasuk::where('user_id', $userId)
            ->whereDate('tanggal_masuk', $today)
            ->count();

        $keluarHariIni = BarangKeluar::where('user_id', $userId)
            ->whereDate('tanggal_keluar', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 6. STOK TERKINI SEMUA ITEM
        |--------------------------------------------------------------------------
        */

        $stokQuery = Item::query()
            ->where('items.user_id', $userId)

            ->leftJoinSub(
                BarangMasuk::query()
                    ->select(
                        'item_id',
                        DB::raw('SUM(jumlah) AS total_masuk')
                    )
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'masuk',
                'masuk.item_id',
                '=',
                'items.id'
            )

            ->leftJoinSub(
                BarangKeluar::query()
                    ->select(
                        'item_id',
                        DB::raw('SUM(jumlah_keluar) AS total_keluar')
                    )
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'keluar',
                'keluar.item_id',
                '=',
                'items.id'
            )

            ->select(
                'items.id',
                'items.nama_barang',
                'items.stok_minimum'
            )

            ->selectRaw(
                'GREATEST(
                    COALESCE(masuk.total_masuk, 0)
                    -
                    COALESCE(keluar.total_keluar, 0),
                    0
                ) AS stok_akhir'
            );

        /*
        |--------------------------------------------------------------------------
        | 7. STOK TERENDAH
        |--------------------------------------------------------------------------
        */

        $stokTerendah = (clone $stokQuery)
            ->orderBy('stok_akhir', 'asc')
            ->orderBy('items.nama_barang')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 8. STOK MINIMUM
        |--------------------------------------------------------------------------
        */

        $stokMinimum = (clone $stokQuery)
            ->whereRaw(
                'GREATEST(
                    COALESCE(masuk.total_masuk, 0)
                    -
                    COALESCE(keluar.total_keluar, 0),
                    0
                ) <= items.stok_minimum'
            )
            ->orderBy('stok_akhir', 'asc')
            ->orderBy('items.nama_barang')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 9. BARANG KADALUARSA DALAM 30 HARI
        |--------------------------------------------------------------------------
        */

        $kadaluarsa = BarangMasuk::query()
            ->where('barang_masuks.user_id', $userId)
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereBetween(
                'tanggal_kadaluarsa',
                [
                    $today,
                    $today->copy()->addDays(30),
                ]
            )
            ->join(
                'items',
                'barang_masuks.item_id',
                '=',
                'items.id'
            )
            ->select(
                'items.nama_barang',
                'barang_masuks.tanggal_kadaluarsa',
                'barang_masuks.jumlah'
            )
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 10. IDLE STOCK > 30 HARI
        |--------------------------------------------------------------------------
        |
        | Kita cari transaksi terakhir masing-masing item.
        |
        */

        $lastMasuk = BarangMasuk::query()
            ->select(
                'item_id',
                DB::raw('MAX(tanggal_masuk) AS last_in')
            )
            ->where('user_id', $userId)
            ->groupBy('item_id');

        $lastKeluar = BarangKeluar::query()
            ->select(
                'item_id',
                DB::raw('MAX(tanggal_keluar) AS last_out')
            )
            ->where('user_id', $userId)
            ->groupBy('item_id');

        $idleStock = Item::query()
            ->where('items.user_id', $userId)

            ->leftJoinSub(
                $lastMasuk,
                'lm',
                'lm.item_id',
                '=',
                'items.id'
            )

            ->leftJoinSub(
                $lastKeluar,
                'lk',
                'lk.item_id',
                '=',
                'items.id'
            )

            ->select(
                'items.nama_barang'
            )

            ->selectRaw(
                'GREATEST(
                    COALESCE(lm.last_in, "1970-01-01"),
                    COALESCE(lk.last_out, "1970-01-01")
                ) AS last_move'
            )

            ->selectRaw(
                'DATEDIFF(
                    NOW(),
                    GREATEST(
                        COALESCE(lm.last_in, "1970-01-01"),
                        COALESCE(lk.last_out, "1970-01-01")
                    )
                ) AS hari_idle'
            )

            ->whereRaw(
                'DATEDIFF(
                    NOW(),
                    GREATEST(
                        COALESCE(lm.last_in, "1970-01-01"),
                        COALESCE(lk.last_out, "1970-01-01")
                    )
                ) > 30'
            )

            ->orderByDesc('hari_idle')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 11. 5 BARANG PALING BANYAK MASUK
        |--------------------------------------------------------------------------
        */

        $topMasuk = BarangMasuk::query()
            ->where('barang_masuks.user_id', $userId)
            ->join(
                'items',
                'barang_masuks.item_id',
                '=',
                'items.id'
            )
            ->select(
                'items.nama_barang'
            )
            ->selectRaw(
                'SUM(barang_masuks.jumlah) AS total'
            )
            ->selectRaw(
                'COUNT(barang_masuks.id) AS frekuensi'
            )
            ->groupBy(
                'barang_masuks.item_id',
                'items.nama_barang'
            )
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 12. 5 BARANG PALING BANYAK KELUAR
        |--------------------------------------------------------------------------
        */

        $topKeluar = BarangKeluar::query()
            ->where('barang_keluars.user_id', $userId)
            ->join(
                'items',
                'barang_keluars.item_id',
                '=',
                'items.id'
            )
            ->select(
                'items.nama_barang'
            )
            ->selectRaw(
                'SUM(barang_keluars.jumlah_keluar) AS total'
            )
            ->selectRaw(
                'COUNT(barang_keluars.id) AS frekuensi'
            )
            ->groupBy(
                'barang_keluars.item_id',
                'items.nama_barang'
            )
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 13. TOTAL ITEM TERDAFTAR
        |--------------------------------------------------------------------------
        */

        $totalItem = Item::where('user_id', $userId)->count();

        /*
        |--------------------------------------------------------------------------
        | 14. KIRIM KE VIEW
        |--------------------------------------------------------------------------
        */

        return view('dashboard.penjual', compact(
            'omzetHariIni',
            'transaksiKeluarHariIni',
            'hppHariIni',
            'labaKotorHariIni',
            'marginHariIni',
            'nilaiPersediaan',

            'omzetBulanIni',
            'hppBulanIni',
            'labaBulanIni',
            'marginBulanIni',

            'masukHariIni',
            'keluarHariIni',

            'stokMinimum',
            'kadaluarsa',
            'stokTerendah',
            'idleStock',
            'topMasuk',
            'topKeluar',

            'totalItem',
            'awalBulan'
        ));
    }
}
