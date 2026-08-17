<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Item;
use App\Models\Lokasi;
use App\Models\Kondisi;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = BarangKeluar::with([
            'item',
            'lokasi',
            'kondisi',
            'user'
        ])
            ->where('user_id', $userId);    // Search
        if ($request->filled('search')) 
        {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', function ($q2) use ($search) {
                    $q2->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                })->orWhereHas('lokasi', function ($q2) use ($search) {
                    $q2->where('nama_lokasi', 'like', "%{$search}%");
                })->orWhereHas('kondisi', function ($q2) use ($search) {
                    $q2->where('nama_kondisi', 'like', "%{$search}%");
                })->orWhere('catatan', 'like', "%{$search}%")
                    ->orWhere('penerima', 'like', "%{$search}%")
                    ->orWhere('lokasi_tujuan', 'like', "%{$search}%")
                    ->orWhere('jenis_transaksi', 'like', "%{$search}%");
            });
        }    // Filter lokasi
        if ($request->filled('lokasi')) 
        {
            $query->where('id_lokasi', $request->lokasi);
        }

        $barangKeluars = $query
            ->orderByDesc('tanggal_keluar')
            ->paginate(10)
            ->withQueryString();    // Lokasi yang memang digunakan user
        $lokasiIds = BarangKeluar::where('user_id', $userId)
            ->whereNotNull('id_lokasi')
            ->distinct()
            ->pluck('id_lokasi');
        $lokasis = Lokasi::where('user_id', $userId)
            ->whereIn('id', $lokasiIds)
            ->orderBy('nama_lokasi')
            ->get();
            
        return view('barangkeluar.index', compact(
            'barangKeluars',
            'lokasis'
        ));
    }

    /**
     * CREATE
     */
    public function create()
    {
        $userId = Auth::id();
        $items = Item::where('user_id', $userId)
            ->orderBy('nama_barang')
            ->get();
        $lokasis = Lokasi::where('user_id', $userId)
            ->orderBy('nama_lokasi')
            ->get();
        $kondisis = Kondisi::where('user_id', $userId)
            ->orderBy('nama_kondisi')
            ->get();
        return view('barangkeluar.create', compact(
            'items',
            'lokasis',
            'kondisis'
        ));
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'id_lokasi' => [
                'required',
                'integer',
                'exists:lokasis,id',
            ],
            'id_kondisi' => [
                'required',
                'integer',
                'exists:kondisis,id',
            ],
            'jumlah_keluar' => [
                'required',
                'integer',
                'min:1',
            ],
            'harga_jual' => [
                'required',
                'numeric',
                'min:0',
            ],
            'penerima' => [
                'required',
                'string',
                'max:255',
            ],
            'lokasi_tujuan' => [
                'required',
                'string',
                'max:255',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'jenis_transaksi' => [
                'required',
                'string',
                'max:50',
            ]
        ]);

        try {
            $userId = Auth::id();

            /*
            |--------------------------------------------------------------------------
            | Pastikan ITEM milik user
            |--------------------------------------------------------------------------
            */
            $item = Item::where('id', $validated['item_id'])
                ->where('user_id', $userId)
                ->firstOrFail();
            /*
            |--------------------------------------------------------------------------
            | Pastikan LOKASI milik user
            |--------------------------------------------------------------------------
            */
            $lokasi = Lokasi::where('id', $validated['id_lokasi'])
                ->where('user_id', $userId)
                ->firstOrFail();
            /*
            |--------------------------------------------------------------------------
            | Pastikan KONDISI milik user
            |--------------------------------------------------------------------------
            */
            $kondisi = Kondisi::where('id', $validated['id_kondisi'])
                ->where('user_id', $userId)
                ->firstOrFail();
            /*
            |--------------------------------------------------------------------------
            | HITUNG STOK
            |--------------------------------------------------------------------------
            |
            | Stok = Barang Masuk - Barang Keluar
            |
            | Berdasarkan:
            | item + lokasi + kondisi + user
            |
            */
            $masuk = BarangMasuk::where('user_id', $userId)
                ->where('item_id', $item->id)
                ->where('id_lokasi', $lokasi->id)
                ->where('id_kondisi', $kondisi->id)
                ->sum('jumlah');

            $keluar = BarangKeluar::where('user_id', $userId)
                ->where('item_id', $item->id)
                ->where('id_lokasi', $lokasi->id)
                ->where('id_kondisi', $kondisi->id)
                ->sum('jumlah_keluar');

            $stok = $masuk - $keluar;

            /*
            |--------------------------------------------------------------------------
            | Validasi stok
            |--------------------------------------------------------------------------
            */
            if ($validated['jumlah_keluar'] > $stok) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Jumlah barang keluar melebihi stok tersedia. Sisa stok: ' . $stok
                    );
            }
            /*
            |--------------------------------------------------------------------------
            | Simpan Barang Keluar
            |--------------------------------------------------------------------------
            */
            BarangKeluar::create([
                'user_id' => $userId,
                'item_id' => $item->id,
                'id_lokasi' => $lokasi->id,
                'id_kondisi' => $kondisi->id,
                'jumlah_keluar' => $validated['jumlah_keluar'],
                'harga_jual' => $validated['harga_jual'],
                'total_harga_jual' => $validated['jumlah_keluar'] * $validated['harga_jual'],
                'tanggal_keluar' => now(),
                'penerima' => $validated['penerima'],
                'lokasi_tujuan' => $validated['lokasi_tujuan'],
                'catatan' => $validated['catatan'] ?? null,
                'jenis_transaksi' => $validated['jenis_transaksi'],
            ]);

            return redirect()
                ->route('barang-keluar.index')
                ->with(
                    'success',
                    'Barang keluar berhasil ditambahkan.'
                );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal menyimpan data: ' . $e->getMessage()
                );
        }
    }

    /**
     * SHOW
     */
    public function show($id)
    {
        $userId = Auth::id();

        $barangKeluar = BarangKeluar::with([
            'item.satuan',
            'lokasi',
            'kondisi',
            'user'
        ])
            ->where('user_id', $userId)
            ->findOrFail($id);

        // Hitung harga rata-rata / WAC dari barang masuk
        $hargaRata = BarangMasuk::where('user_id', $userId)
            ->where('item_id', $barangKeluar->item_id)
            ->where('id_lokasi', $barangKeluar->id_lokasi)
            ->where('id_kondisi', $barangKeluar->id_kondisi)
            ->selectRaw(
                'COALESCE(SUM(harga_satuan * jumlah) / NULLIF(SUM(jumlah), 0), 0) as wac'
            )
            ->value('wac');

        return view('barangkeluar.detail', compact(
            'barangKeluar',
            'hargaRata'
        ));
    }

    public function edit($id)
    {
        $userId = Auth::id();

        $barangKeluar = BarangKeluar::with([
            'item',
            'lokasi',
            'kondisi'
        ])
            ->where('user_id', $userId)
            ->findOrFail($id);

        return view('barangkeluar.edit', [
            'barangKeluar' => $barangKeluar,

            'items' => Item::where('user_id', $userId)
                ->orderBy('nama_barang')
                ->get(),

            'lokasis' => Lokasi::where('user_id', $userId)
                ->orderBy('nama_lokasi')
                ->get(),

            'kondisis' => Kondisi::where('user_id', $userId)
                ->orderBy('nama_kondisi')
                ->get(),
        ]);
    }

    /**
     * UPDATE  
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $barangKeluar = BarangKeluar::where('user_id', $userId)
            ->findOrFail($id);
        $validated = $request->validate([
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'id_lokasi' => [
                'required',
                'integer',
                'exists:lokasis,id',
            ],
            'id_kondisi' => [
                'required',
                'integer',
                'exists:kondisis,id',
            ],
            'jumlah_keluar' => [
                'required',
                'integer',
                'min:1',
            ],
            'harga_jual' => [
                'required',
                'numeric',
                'min:0',
            ],
            'penerima' => [
                'required',
                'string',
                'max:255',
            ],
            'lokasi_tujuan' => [
                'required',
                'string',
                'max:255',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'jenis_transaksi' => [
                'required',
                'string',
                'max:50',
            ],
        ]);
        try {        /*
            |--------------------------------------------------------------------------
            | Validasi ownership
            |--------------------------------------------------------------------------
            */
            $item = Item::where('id', $validated['item_id'])
                ->where('user_id', $userId)
                ->firstOrFail();
            $lokasi = Lokasi::where('id', $validated['id_lokasi'])
                ->where('user_id', $userId)
                ->firstOrFail();
            $kondisi = Kondisi::where('id', $validated['id_kondisi'])
                ->where('user_id', $userId)
                ->firstOrFail();
            /*
            |--------------------------------------------------------------------------
            | Hitung stok
            |--------------------------------------------------------------------------
            |
            | Saat edit, transaksi lama harus dikurangi dari
            | total barang keluar agar tidak menghitung dirinya sendiri.
            |
            */
            $masuk = BarangMasuk::where('user_id', $userId)
                ->where('item_id', $item->id)
                ->where('id_lokasi', $lokasi->id)
                ->where('id_kondisi', $kondisi->id)
                ->sum('jumlah');
            $keluar = BarangKeluar::where('user_id', $userId)
                ->where('item_id', $item->id)
                ->where('id_lokasi', $lokasi->id)
                ->where('id_kondisi', $kondisi->id)
                ->where('id', '!=', $barangKeluar->id)
                ->sum('jumlah_keluar');
            $stok = $masuk - $keluar;
            if ($validated['jumlah_keluar'] > $stok) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Jumlah barang keluar melebihi stok tersedia. Sisa stok: ' . $stok
                    );
            }
            $barangKeluar->update([
                'item_id' => $item->id,
                'id_lokasi' => $lokasi->id,
                'id_kondisi' => $kondisi->id,
                'jumlah_keluar' => $validated['jumlah_keluar'],
                'harga_jual' => $validated['harga_jual'],
                'total_harga_jual' =>
                $validated['jumlah_keluar']
                    * $validated['harga_jual'],

                'penerima' => $validated['penerima'],

                'lokasi_tujuan' => $validated['lokasi_tujuan'],

                'catatan' => $validated['catatan'] ?? null,
                'jenis_transaksi' => $validated['jenis_transaksi'],
            ]);


            return redirect()
                ->route('barang-keluar.index')
                ->with(
                    'success',
                    'Barang keluar berhasil diperbarui.'
                );
        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal memperbarui data: ' . $e->getMessage()
                );
        }
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $userId = Auth::id();

        try {
            $barangKeluar = BarangKeluar::where('user_id', $userId)
                ->findOrFail($id);

            $barangKeluar->delete();

            return redirect()
                ->route('barang-keluar.index')
                ->with(
                    'success',
                    'Barang keluar berhasil dihapus.'
                );
        } catch (\Throwable $e) {

            return back()
                ->with(
                    'error',
                    'Gagal menghapus data barang keluar.'
                );
        }
    }

    /**
     * CETAK BERITA ACARA  
     */
    public function cetakBA($id)
    {
        \Carbon\Carbon::setLocale('id');

        $barangKeluar = BarangKeluar::with([
            'item',
            'lokasi',
            'kondisi',
            'user'
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView(
            'barangkeluar.cetak_ba',
            compact('barangKeluar')
        )->setPaper('a4', 'portrait');

        return $pdf->stream(
            'berita-acara-barang-keluar.pdf'
        );
    }

    /**
     * CETAK DETAIL BARANG KELUAR
     */
    public function cetakDetail($id)
    {
        \Carbon\Carbon::setLocale('id');

        $userId = Auth::id();

        $barangKeluar = BarangKeluar::with([
            'item.satuan',
            'lokasi',
            'kondisi',
            'user'
        ])
            ->where('user_id', $userId)
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Weighted Average Cost (WAC)
        |--------------------------------------------------------------------------
        |
        | Harga rata-rata dihitung berdasarkan seluruh transaksi
        | barang masuk dengan kombinasi:
        | user + item + lokasi + kondisi
        |
        */

        $hargaRata = (float) BarangMasuk::where('user_id', $userId)
            ->where('item_id', $barangKeluar->item_id)
            ->where('id_lokasi', $barangKeluar->id_lokasi)
            ->where('id_kondisi', $barangKeluar->id_kondisi)
            ->selectRaw(
                'COALESCE(
                SUM(harga_satuan * jumlah) /
                NULLIF(SUM(jumlah), 0),
                0
            ) as wac'
            )
            ->value('wac');

        $pdf = Pdf::loadView(
            'barangkeluar.cetak_detail',
            compact(
                'barangKeluar',
                'hargaRata'
            )
        )->setPaper('a4', 'portrait');

        return $pdf->stream(
            'detail-barang-keluar-' . $barangKeluar->id . '.pdf'
        );
    }

    /**
     * API CEK STOK
     */
    public function cekStok(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'item_id' => 'required|integer',
            'id_lokasi' => 'required|integer',
            'id_kondisi' => 'required|integer',
        ]);

        $masuk = BarangMasuk::where('user_id', $userId)
            ->where('item_id', $request->item_id)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('id_kondisi', $request->id_kondisi)
            ->sum('jumlah');

        $keluar = BarangKeluar::where('user_id', $userId)
            ->where('item_id', $request->item_id)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('id_kondisi', $request->id_kondisi)
            ->sum('jumlah_keluar');

        return response()->json([
            'stok' => max(0, $masuk - $keluar)
        ]);
    }

    /**
     * API DETAIL BARANG
     */
    public function getDetailBarang(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'item_id' => 'required|integer',
            'id_lokasi' => 'required|integer',
            'id_kondisi' => 'required|integer',
        ]);

        $item = Item::with('satuan')
            ->where('id', $request->item_id)
            ->where('user_id', $userId)
            ->first();

        if (!$item) {
            return response()->json([
                'stok' => 0,
                'nama_barang' => '-',
                'satuan' => '-',
                'lokasi' => '-',
                'kondisi' => '-',
                'harga_dasar' => 0,
            ]);
        }


        $lokasi = Lokasi::where('id', $request->id_lokasi)
            ->where('user_id', $userId)
            ->first();

        $kondisi = Kondisi::where('id', $request->id_kondisi)
            ->where('user_id', $userId)
            ->first();


        $masuk = BarangMasuk::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('id_kondisi', $request->id_kondisi)
            ->sum('jumlah');

        $keluar = BarangKeluar::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('id_kondisi', $request->id_kondisi)
            ->sum('jumlah_keluar');


        /*
        |--------------------------------------------------------------------------
        | Weighted Average Cost
        |--------------------------------------------------------------------------
        */
        $wac = (float) BarangMasuk::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('id_kondisi', $request->id_kondisi)
            ->selectRaw(
                'COALESCE(SUM(harga_satuan * jumlah) / NULLIF(SUM(jumlah), 0), 0) as wac'
            )
            ->value('wac');


        return response()->json([

            'stok' => max(0, $masuk - $keluar),

            'nama_barang' => $item->nama_barang ?? '-',

            'satuan' => optional($item->satuan)->nama_satuan ?? '-',

            'lokasi' => $lokasi->nama_lokasi ?? '-',

            'kondisi' => $kondisi->nama_kondisi ?? '-',

            'id_lokasi' => $request->id_lokasi,

            'id_kondisi' => $request->id_kondisi,

            'harga_dasar' => (int) round($wac),
        ]);
    }

    /**
     * API PILIHAN BARANG UNIK
     */
    public function getPilihanBarangUnik()
    {
        $userId = Auth::id();

        $barangMasuks = BarangMasuk::with([
            'item.satuan',
            'lokasi',
            'kondisi'
        ])
            ->where('user_id', $userId)
            ->get();

        $grouped = $barangMasuks
            ->groupBy(function ($barang) {
                return $barang->item_id
                    . '|' .
                    $barang->id_lokasi
                    . '|' .
                    $barang->id_kondisi;
            })
            ->map(function ($group) use ($userId) {

                $latest = $group
                    ->sortByDesc('tanggal_masuk')
                    ->sortByDesc('id')
                    ->first();

                $itemId = $latest->item_id;
                $lokasiId = $latest->id_lokasi;
                $kondisiId = $latest->id_kondisi;


                $masuk = $group->sum('jumlah');

                $keluar = BarangKeluar::where('user_id', $userId)
                    ->where('item_id', $itemId)
                    ->where('id_lokasi', $lokasiId)
                    ->where('id_kondisi', $kondisiId)
                    ->sum('jumlah_keluar');


                $stok = $masuk - $keluar;

                if ($stok <= 0) {
                    return null;
                }


                $wac = (float) $group
                    ->sum(function ($row) {
                        return $row->harga_satuan * $row->jumlah;
                    });

                $totalJumlah = $group->sum('jumlah');

                $wac = $totalJumlah > 0
                    ? $wac / $totalJumlah
                    : 0;


                return [

                    'item_id'     => $latest->item_id,

                    'kode' => $latest->item->kode_barang ?? '-',

                    'nama_barang' =>
                    $latest->item->nama_barang ?? '-',

                    'lokasi_id' => $lokasiId,

                    'lokasi' =>
                    $latest->lokasi->nama_lokasi ?? '-',

                    'kondisi_id' => $kondisiId,

                    'kondisi' =>
                    $latest->kondisi->nama_kondisi ?? '-',

                    'stok' => $stok,

                    'satuan' =>
                    optional($latest->item->satuan)->nama_satuan
                        ?? '-',

                    'harga_dasar' => (int) round($wac),
                ];
            })
            ->filter()
            ->values();

        return response()->json(
            $grouped
        );
    }
}
