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
                    ->select('kode_barang', DB::raw('SUM(jumlah) AS total_masuk'))
                    ->where('user_id', $userId)
                    ->groupBy('kode_barang'),
                'm',
                'm.kode_barang',
                '=',
                'i.kode_barang'
            )
            ->leftJoinSub(
                DB::table('barang_keluars')
                    ->select('kode_barang', DB::raw('SUM(jumlah_keluar) AS total_keluar'))
                    ->where('user_id', $userId)
                    ->groupBy('kode_barang'),
                'k',
                'k.kode_barang',
                '=',
                'i.kode_barang'
            )
            ->select(
                'i.nama_barang',
                'i.stok_minimum',
                DB::raw('GREATEST(COALESCE(m.total_masuk,0) - COALESCE(k.total_keluar,0), 0) AS stok_akhir')
            )
            // PAKAI WHERE, BUKAN HAVING
            ->whereRaw('GREATEST(COALESCE(m.total_masuk,0) - COALESCE(k.total_keluar,0), 0) <= i.stok_minimum')
            ->orderBy('stok_akhir', 'asc')
            ->orderBy('i.nama_barang')
            ->get();


        // 2. Barang kadaluarsa dalam 30 hari
        $kadaluarsa = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
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
        // 5 stok terendah, dihitung dari total_masuk - total_keluar
        $stokTerendah = DB::table('items as i')
            ->leftJoinSub(
                DB::table('barang_masuks')
                    ->select('kode_barang', DB::raw('SUM(jumlah) as total_masuk'))
                    ->where('user_id', $userId)
                    ->groupBy('kode_barang'),
                'm',
                'm.kode_barang',
                '=',
                'i.kode_barang'
            )
            ->leftJoinSub(
                DB::table('barang_keluars')
                    ->select('kode_barang', DB::raw('SUM(jumlah_keluar) as total_keluar'))
                    ->where('user_id', $userId)
                    ->groupBy('kode_barang'),
                'k',
                'k.kode_barang',
                '=',
                'i.kode_barang'
            )
            ->select(
                'i.kode_barang',
                'i.nama_barang',
                DB::raw('GREATEST(COALESCE(m.total_masuk,0) - COALESCE(k.total_keluar,0), 0) as stok_akhir')
            )
            // Kalau mau sembunyikan barang yang belum pernah ada pergerakan, buka komentar di bawah:
            // ->havingRaw('(COALESCE(m.total_masuk,0) + COALESCE(k.total_keluar,0)) > 0')
            ->orderBy('stok_akhir', 'asc')
            ->orderBy('i.nama_barang')
            ->limit(5)
            ->get();


        // 5) Idle Stock: barang yang tidak bergerak > 30 hari
        $idleStock = DB::table('items as i')
            // last_in per barang
            ->leftJoinSub(
                DB::table('barang_masuks')
                    ->select('kode_barang', DB::raw('MAX(tanggal_masuk) AS last_in'))
                    ->where('user_id', $userId)
                    ->groupBy('kode_barang'),
                'm',
                'm.kode_barang',
                '=',
                'i.kode_barang'
            )
            // last_out per barang
            ->leftJoinSub(
                DB::table('barang_keluars')
                    ->select('kode_barang', DB::raw('MAX(tanggal_keluar) AS last_out'))
                    ->where('user_id', $userId)
                    ->groupBy('kode_barang'),
                'k',
                'k.kode_barang',
                '=',
                'i.kode_barang'
            )
            // ambil lokasi dari transaksi masuk TERAKHIR
            ->leftJoin('barang_masuks as bm_latest', function ($join) use ($userId) {
                $join->on('bm_latest.kode_barang', '=', 'i.kode_barang')
                    ->where('bm_latest.user_id', '=', $userId);
            })
            // join tabel lokasi untuk nama lokasi (dari bm_latest)
            ->leftJoin('lokasis as l', 'l.id', '=', 'bm_latest.id_lokasi')
            ->select(
                'i.nama_barang',
                DB::raw('COALESCE(l.nama_lokasi, "-") AS nama_lokasi'),
                // opsional: berapa hari mengendap
                DB::raw("DATEDIFF(NOW(), GREATEST(COALESCE(m.last_in, '1970-01-01'), COALESCE(k.last_out, '1970-01-01'))) AS hari_idle"),
                // tanggal pergerakan terakhir (buat referensi/tampilan)
                DB::raw("GREATEST(COALESCE(m.last_in, '1970-01-01'), COALESCE(k.last_out, '1970-01-01')) AS last_move")
            )
            // pastikan bm_latest adalah baris last_in agar lokasinya sesuai
            ->whereColumn('bm_latest.tanggal_masuk', '=', DB::raw('m.last_in'))
            // filter idle > 30 hari
            ->whereRaw("DATEDIFF(NOW(), GREATEST(COALESCE(m.last_in, '1970-01-01'), COALESCE(k.last_out, '1970-01-01'))) > 30")
            // urutkan yang paling lama mengendap di atas
            ->orderByDesc('hari_idle')
            ->limit(5)
            ->get();



        // 6. 5 Barang paling banyak masuk
        $topMasuk = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->where('barang_masuks.user_id', $userId)
            ->select(
                'items.nama_barang',
                DB::raw('SUM(jumlah) as total'),
                DB::raw('COUNT(barang_masuks.id) as frekuensi')
            )
            ->groupBy('barang_masuks.kode_barang', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        // 7. 5 Barang paling banyak keluar
        $topKeluar = DB::table('barang_keluars')
            ->join('items', 'barang_keluars.kode_barang', '=', 'items.kode_barang')
            ->where('barang_keluars.user_id', $userId)
            ->select(
                'items.nama_barang',
                DB::raw('SUM(jumlah_keluar) as total'),
                DB::raw('COUNT(barang_keluars.id) as frekuensi')
            )
            ->groupBy('barang_keluars.kode_barang', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        return view('dashboard.gudang', compact(
            'stokMinimum', 'kadaluarsa', 'masukHariIni', 'keluarHariIni',
            'stokTerendah', 'idleStock', 'topMasuk', 'topKeluar'
        ));
    }
}
