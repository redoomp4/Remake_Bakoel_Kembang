<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Pemasok;
use App\Models\Lokasi;
use App\Models\Kondisi;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; 

class BarangMasukController extends Controller
{
    /**
     * INDEX
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search   = $request->input('search');
        $lokasiId = $request->input('lokasi');
        $barangMasuks = BarangMasuk::with([
            'item',
            'pemasok',
            'lokasi',
            'kondisi',
        ])
            ->where('user_id', $user->id)
            // Pencarian
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                                    $q->whereHas('item', function ($qi) use ($search) {
                        $qi->where('nama_barang', 'like', "%{$search}%")
                            ->orWhere('kode_barang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('lokasi', function ($ql) use ($search) {
                            $ql->where('nama_lokasi', 'like', "%{$search}%");
                        })
                        ->orWhereHas('kondisi', function ($qk) use ($search) {
                            $qk->where('nama_kondisi', 'like', "%{$search}%");
                        })
                        ->orWhereHas('pemasok', function ($qp) use ($search) {
                            $qp->where('nama_pemasok', 'like', "%{$search}%");
                        });
                });
            })
            // Filter lokasi
            ->when($lokasiId, function ($query) use ($lokasiId) {
                $query->where('id_lokasi', $lokasiId);
            })
            ->latest('tanggal_masuk')
            ->paginate(10)
            ->withQueryString();
            /*        | Hanya tampilkan lokasi yang digunakan oleh user yang login        */
        $lokasiIds = BarangMasuk::where('user_id', $user->id)
            ->distinct()
            ->pluck('id_lokasi');
            $lokasis = Lokasi::whereIn('id', $lokasiIds)
            ->where('user_id', $user->id)
            ->orderBy('nama_lokasi')
            ->get();
            return view(
            'barangmasuk.index',
            compact('barangMasuks', 'lokasis')
        );
    }
    
    /**
     * CREATE
     */
    public function create()
    {
        $userId = Auth::id();
        return view('barangmasuk.create', [
            'items' => Item::where('user_id', $userId)
                ->orderBy('nama_barang')
                ->get(),
                'pemasoks' => Pemasok::where('user_id', $userId)
                ->orderBy('nama_pemasok')
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
            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'harga_satuan' => [
                'required',
                'numeric',
                'min:0',
            ],
            'tanggal_masuk' => [
                'required',
                'date',
            ],
            'tanggal_kadaluarsa' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_masuk',
            ],
            'id_pemasok' => [
                'required',
                'exists:pemasoks,id',
            ],
            'id_lokasi' => [
                'required',
                'exists:lokasis,id',
            ],
            'id_kondisi' => [
                'required',
                'exists:kondisis,id',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);
        try {
                    $userId = Auth::id();
            /*
            | Pastikan ITEM milik user yang sedang login
            */
            $item = Item::where('id', $validated['item_id'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*
            | Pastikan pemasok milik user
            */
            $pemasok = Pemasok::where('id', $validated['id_pemasok'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*
            | Pastikan lokasi milik user
            */
            $lokasi = Lokasi::where('id', $validated['id_lokasi'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*
            | Pastikan kondisi milik user
            */
            $kondisi = Kondisi::where('id', $validated['id_kondisi'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*
            | Hitung total harga
            */
            $totalHarga =
                $validated['jumlah'] *
                $validated['harga_satuan'];

                /*
            | Simpan transaksi barang masuk
            */
            BarangMasuk::create([
                'user_id' => $userId,
                'item_id' => $item->id,
                'jumlah' => $validated['jumlah'],
                'harga_satuan' => $validated['harga_satuan'],
                'total_harga' => $totalHarga,
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_kadaluarsa' =>
                $validated['tanggal_kadaluarsa'] ?? null,
                'id_pemasok' => $pemasok->id,
                'id_lokasi' => $lokasi->id,
                'id_kondisi' => $kondisi->id,
                'catatan' => $validated['catatan'] ?? null,
            ]);
            return redirect()
                ->route('barang-masuk.index')
                ->with(
                    'success',
                    'Barang masuk berhasil ditambahkan.'
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
        $barangMasuk = BarangMasuk::with([
            'item',
            'pemasok',
            'lokasi',
            'kondisi',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            return view(
            'barangmasuk.show',
            compact('barangMasuk')
        );
    }

    /**
     * DETAIL 
     * Halaman ini hanya menampilkan detail transaksi barang masuk.
     */
    public function detail($id)
    {
        $barangMasuk = BarangMasuk::with([
            'item',
            'pemasok',
            'lokasi',
            'kondisi',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            return view(
            'barangmasuk.detailbarang',
            compact('barangMasuk')
        );
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $barangMasuk = BarangMasuk::with([
            'item',
            'pemasok',
            'lokasi',
            'kondisi',
        ])
            ->where('user_id', $userId)
            ->findOrFail($id);
            return view('barangmasuk.edit', [
            'barangMasuk' => $barangMasuk,
            'items' => Item::where('user_id', $userId)
                ->orderBy('nama_barang')
                ->get(),
                'pemasoks' => Pemasok::where('user_id', $userId)
                ->orderBy('nama_pemasok')
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
        $validated = $request->validate([
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'harga_satuan' => [
                'required',
                'numeric',
                'min:0',
            ],
            'tanggal_masuk' => [
                'required',
                'date',
            ],
            'tanggal_kadaluarsa' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_masuk',
            ],
            'id_pemasok' => [
                'required',
                'exists:pemasoks,id',
            ],
            'id_lokasi' => [
                'required',
                'exists:lokasis,id',
            ],
            'id_kondisi' => [
                'required',
                'exists:kondisis,id',
            ],
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);
        try {
                    $userId = Auth::id();
            /*        | Ambil Barang Masuk milik user yang sedang login        */
            $barangMasuk = BarangMasuk::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

                /*        | Pastikan ITEM milik user        */
            $item = Item::where('id', $validated['item_id'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*        | Pastikan PEMASOK milik user        */
            $pemasok = Pemasok::where('id', $validated['id_pemasok'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*        | Pastikan LOKASI milik user        */
            $lokasi = Lokasi::where('id', $validated['id_lokasi'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*        | Pastikan KONDISI milik user        */
            $kondisi = Kondisi::where('id', $validated['id_kondisi'])
                ->where('user_id', $userId)
                ->firstOrFail();

                /*        | Hitung ulang total harga        */
            $totalHarga =
                $validated['jumlah'] *
                $validated['harga_satuan'];

                /*        | Update data Barang Masuk        */
            $barangMasuk->update([
                'item_id' => $item->id,
                'jumlah' => $validated['jumlah'],
                'harga_satuan' => $validated['harga_satuan'],
                'total_harga' => $totalHarga,
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_kadaluarsa' =>
                $validated['tanggal_kadaluarsa'] ?? null,
                'id_pemasok' => $pemasok->id,
                'id_lokasi' => $lokasi->id,
                'id_kondisi' => $kondisi->id,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            return redirect()
                ->route('barang-masuk.index')
                ->with(
                    'success',
                    'Data barang masuk berhasil diperbarui.'
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
     * DELETE
     */
    public function destroy($id)
    {
        try {
                    $userId = Auth::id();
            $barangMasuk = BarangMasuk::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();
                $barangMasuk->delete();
            return redirect()
                ->route('barang-masuk.index')
                ->with(
                    'success',
                    'Data barang masuk berhasil dihapus.'
                );
        } catch (\Throwable $e) {
                    return back()
                ->with(
                    'error',
                    'Gagal menghapus data barang masuk.'
                );
        }
    }

    /**
     * CETAK BERITA ACARA
     */
    public function cetakBeritaAcara($id)
    {
        \Carbon\Carbon::setLocale('id');
        $barangMasuk = BarangMasuk::with([
            'item.satuan',
            'lokasi',
            'kondisi',
            'pemasok',
            'user',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            $pdf = Pdf::loadView('barangmasuk.berita_acara_pdf', [
            'barangMasuk'     => $barangMasuk,
            'tanggal_lengkap' => $barangMasuk->tanggal_masuk->translatedFormat('d F Y'),
            'hari'            => $barangMasuk->tanggal_masuk->translatedFormat('l'),
            'bulan'           => $barangMasuk->tanggal_masuk->format('m'),
            'tahun'           => $barangMasuk->tanggal_masuk->format('Y'),
            'nomor'           => str_pad($barangMasuk->id, 3, '0', STR_PAD_LEFT),
            'lokasi'          => $barangMasuk->lokasi->nama_lokasi ?? '-',
        ])->setPaper('A4', 'portrait');
        return $pdf->stream(
            'berita_acara_barang_masuk_' . $barangMasuk->id . '.pdf',
            ['Attachment' => false]
        );
    }

    /**
     * CETAK BERITA ACARA
     */
    public function cetakDetail($id)
    {
        \Carbon\Carbon::setLocale('id');
        $barangMasuk = BarangMasuk::with([
            'item.satuan',
            'lokasi',
            'kondisi',
            'pemasok',
            'user',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            $pdf = Pdf::loadView('barangmasuk.berita_acara_cetak_warna', [
            'barangMasuk'     => $barangMasuk,
            'tanggal_lengkap' => $barangMasuk->tanggal_masuk->translatedFormat('d F Y'),
            'hari'            => $barangMasuk->tanggal_masuk->translatedFormat('l'),
            'bulan'           => $barangMasuk->tanggal_masuk->format('m'),
            'tahun'           => $barangMasuk->tanggal_masuk->format('Y'),
            'nomor'           => str_pad($barangMasuk->id, 3, '0', STR_PAD_LEFT),
            'lokasi'          => $barangMasuk->lokasi->nama_lokasi ?? '-',
        ])->setPaper('A4', 'portrait');
        return $pdf->stream(
            'berita_acara_barang_masuk_' . $barangMasuk->id . '.pdf',
            ['Attachment' => false]
        );
    }
}
