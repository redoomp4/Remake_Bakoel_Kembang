<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardGudangController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();


        // 1. Jumlah stok hampir habis
        $stokMinimum = DB::table('items as i')
            ->leftJoinSub(
                DB::table('barang_masuks')
                    ->select('item_id', DB::raw('SUM(jumlah) AS total_masuk'))
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'm',
                'm.item_id',
                '=',
                'i.id'
            )
            ->leftJoinSub(
                DB::table('barang_keluars')
                    ->select('item_id', DB::raw('SUM(jumlah_keluar) AS total_keluar'))
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'k',
                'k.item_id',
                '=',
                'i.id'
            )
            ->where('i.user_id', $userId)
            ->select(
                'i.nama_barang',
                'i.stok_minimum',
                DB::raw('GREATEST(COALESCE(m.total_masuk,0) - COALESCE(k.total_keluar,0), 0) AS stok_akhir')
            )
            ->whereRaw('GREATEST(COALESCE(m.total_masuk,0) - COALESCE(k.total_keluar,0), 0) <= i.stok_minimum')
            ->orderBy('stok_akhir', 'asc')
            ->orderBy('i.nama_barang')
            ->get();


        // 2. Barang kadaluarsa dalam 30 hari
        $kadaluarsa = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.item_id', '=', 'items.id')
            ->where('barang_masuks.user_id', $userId)
            ->whereBetween('barang_masuks.tanggal_kadaluarsa', [$today, $today->copy()->addDays(30)])
            ->select('items.nama_barang', 'barang_masuks.tanggal_kadaluarsa')
            ->get();


        // 3. Transaksi Hari Ini
        $masukHariIni = DB::table('barang_masuks')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();


        $keluarHariIni = DB::table('barang_keluars')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();


        // 4. 5 Barang Stok Terendah
        $stokTerendah = DB::table('items as i')
            ->leftJoinSub(
                DB::table('barang_masuks')
                    ->select('item_id', DB::raw('SUM(jumlah) as total_masuk'))
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'm',
                'm.item_id',
                '=',
                'i.id'
            )
            ->leftJoinSub(
                DB::table('barang_keluars')
                    ->select('item_id', DB::raw('SUM(jumlah_keluar) as total_keluar'))
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'k',
                'k.item_id',
                '=',
                'i.id'
            )
            ->where('i.user_id', $userId)
            ->select(
                'i.kode_barang',
                'i.nama_barang',
                DB::raw('GREATEST(COALESCE(m.total_masuk,0) - COALESCE(k.total_keluar,0), 0) as stok_akhir')
            )
            ->orderBy('stok_akhir', 'asc')
            ->orderBy('i.nama_barang')
            ->limit(5)
            ->get();


        // 5) Idle Stock: barang yang tidak bergerak > 30 hari
        $idleStock = DB::table('items as i')
            ->leftJoinSub(
                DB::table('barang_masuks')
                    ->select('item_id', DB::raw('MAX(tanggal_masuk) AS last_in'))
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'm',
                'm.item_id',
                '=',
                'i.id'
            )
            ->leftJoinSub(
                DB::table('barang_keluars')
                    ->select('item_id', DB::raw('MAX(tanggal_keluar) AS last_out'))
                    ->where('user_id', $userId)
                    ->groupBy('item_id'),
                'k',
                'k.item_id',
                '=',
                'i.id'
            )
            ->leftJoin('barang_masuks as bm_latest', function ($join) use ($userId) {
                $join->on('bm_latest.item_id', '=', 'i.id')
                    ->where('bm_latest.user_id', '=', $userId);
            })
            ->leftJoin('lokasis as l', 'l.id', '=', 'bm_latest.id_lokasi')
            ->where('i.user_id', $userId)
            ->select(
                'i.nama_barang',
                DB::raw('COALESCE(l.nama_lokasi, "-") AS nama_lokasi'),
                DB::raw("DATEDIFF(NOW(), GREATEST(COALESCE(m.last_in, '1970-01-01'), COALESCE(k.last_out, '1970-01-01'))) AS hari_idle"),
                DB::raw("GREATEST(COALESCE(m.last_in, '1970-01-01'), COALESCE(k.last_out, '1970-01-01')) AS last_move")
            )
            ->whereColumn('bm_latest.tanggal_masuk', '=', DB::raw('m.last_in'))
            ->whereRaw("DATEDIFF(NOW(), GREATEST(COALESCE(m.last_in, '1970-01-01'), COALESCE(k.last_out, '1970-01-01'))) > 30")
            ->orderByDesc('hari_idle')
            ->limit(5)
            ->get();



        // 6. 5 Barang paling banyak masuk
        $topMasuk = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.item_id', '=', 'items.id')
            ->where('barang_masuks.user_id', $userId)
            ->select(
                'items.nama_barang',
                DB::raw('SUM(jumlah) as total'),
                DB::raw('COUNT(barang_masuks.id) as frekuensi')
            )
            ->groupBy('barang_masuks.item_id', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();



        // 7. 5 Barang paling banyak keluar
        $topKeluar = DB::table('barang_keluars')
            ->join('items', 'barang_keluars.item_id', '=', 'items.id')
            ->where('barang_keluars.user_id', $userId)
            ->select(
                'items.nama_barang',
                DB::raw('SUM(jumlah_keluar) as total'),
                DB::raw('COUNT(barang_keluars.id) as frekuensi')
            )
            ->groupBy('barang_keluars.item_id', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        return view('dashboard.gudang', compact(
            'stokMinimum', 'kadaluarsa', 'masukHariIni', 'keluarHariIni',
            'stokTerendah', 'idleStock', 'topMasuk', 'topKeluar'
        ));
    }
}
