<?php


namespace App\Http\Controllers;


use App\Models\{BarangMasuk, BarangKeluar, Item, Lokasi, Kondisi};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangKeluar::with(['item', 'lokasi', 'kondisi', 'user'])
            ->where('user_id', auth()->id());


        // Filter pencarian umum
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', fn($q2) => $q2->where('nama_barang', 'like', "%{$search}%"))
                  ->orWhereHas('lokasi', fn($q2) => $q2->where('nama_lokasi', 'like', "%{$search}%"))
                  ->orWhereHas('kondisi', fn($q2) => $q2->where('nama_kondisi', 'like', "%{$search}%"))
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhere('penerima', 'like', "%{$search}%")
                  ->orWhere('lokasi_tujuan', 'like', "%{$search}%");
            });
        }


        // Filter lokasi
        if ($request->lokasi) {
            $query->where('id_lokasi', $request->lokasi);
        }


        $barangKeluars = $query->orderBy('tanggal_keluar', 'desc')->paginate(10);


        // ✅ Hanya lokasi yang memang muncul pada data Barang Keluar (user ini)
        $lokasiIds = BarangKeluar::where('user_id', auth()->id())
            ->distinct()
            ->pluck('id_lokasi');


        $lokasis = Lokasi::whereIn('id', $lokasiIds)
            ->orderBy('nama_lokasi')
            ->get();


        return view('barangkeluar.index', compact('barangKeluars', 'lokasis'));
    }


    public function create()
    {
        return view('barangkeluar.create');
    }


    public function store(Request $request)
    {
        // Support either item_id or kode_barang input
        if (!$request->filled('item_id') && $request->filled('kode_barang')) {
            $matchedItem = Item::where('kode_barang', $request->kode_barang)->first();
            if ($matchedItem) {
                $request->merge(['item_id' => $matchedItem->id]);
            }
        }

        $request->validate([
            'item_id'        => 'required|exists:items,id',
            'id_lokasi'      => 'required|exists:lokasis,id',
            'id_kondisi'     => 'required|exists:kondisis,id',
            'jumlah_keluar'  => 'required|integer|min:1',
            'harga_jual'     => 'required|numeric|min:0',
            'catatan'        => 'required|string|max:255',
            'penerima'       => 'required|string|max:255',
            'lokasi_tujuan'  => 'required|string|max:255',
        ]);

        $itemId = (int) $request->item_id;

        $masuk = BarangMasuk::where('item_id', $itemId)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('id_kondisi', $request->id_kondisi)
            ->sum('jumlah');

        $keluar = BarangKeluar::where('item_id', $itemId)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('id_kondisi', $request->id_kondisi)
            ->sum('jumlah_keluar');

        $stok = $masuk - $keluar;

        if ($request->jumlah_keluar > $stok) {
            return back()->withInput()->with('error', 'Jumlah keluar melebihi stok tersedia. Sisa stok: ' . $stok);
        }

        BarangKeluar::create([
            'item_id'          => $itemId,
            'id_lokasi'        => $request->id_lokasi,
            'id_kondisi'       => $request->id_kondisi,
            'jumlah_keluar'    => $request->jumlah_keluar,
            'harga_jual'       => $request->harga_jual,
            'total_harga_jual' => $request->jumlah_keluar * $request->harga_jual,
            'tanggal_keluar'   => now(),
            'user_id'          => auth()->id(),
            'penerima'         => $request->penerima,
            'lokasi_tujuan'    => $request->lokasi_tujuan,
            'catatan'          => $request->catatan,
            'jenis_transaksi'  => $request->input('jenis_transaksi', 'Penjualan'),
        ]);

        return redirect()->route('barang-keluar.index')->with('success', 'Barang keluar berhasil ditambahkan.');
    }

    public function show($id)
    {
        $barangKeluar = BarangKeluar::with(['item', 'lokasi', 'kondisi', 'user'])->findOrFail($id);
        return view('barangkeluar.detail', compact('barangKeluar'));
    }

    public function cetakBA($id)
    {
        \Carbon\Carbon::setLocale('id');

        $barangKeluar = BarangKeluar::with(['item', 'lokasi', 'kondisi', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('barangkeluar.cetak_ba', compact('barangKeluar'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('berita-acara-barang-keluar.pdf');
    }

    public function cetakDetail($id)
    {
        $barangKeluar = BarangKeluar::with(['item.satuan', 'kondisi', 'lokasi', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('barangkeluar.cetak_detail', compact('barangKeluar'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('detail-barang-keluar.pdf');
    }

    /**
     * API: hitung stok berdasarkan item_id + lokasi + kondisi
     */
    public function cekStok(Request $request)
    {
        $itemId = $this->resolveItemId($request);
        $lokasiId   = $request->id_lokasi;
        $kondisiId  = $request->id_kondisi;

        $masuk = BarangMasuk::where('user_id', auth()->id())
            ->where('item_id', $itemId)
            ->where('id_lokasi', $lokasiId)
            ->where('id_kondisi', $kondisiId)
            ->sum('jumlah');

        $keluar = BarangKeluar::where('user_id', auth()->id())
            ->where('item_id', $itemId)
            ->where('id_lokasi', $lokasiId)
            ->where('id_kondisi', $kondisiId)
            ->sum('jumlah_keluar');

        return response()->json(['stok' => $masuk - $keluar]);
    }

    /**
     * API: detail barang untuk pengisian otomatis di form
     * -> 'harga_dasar' diisi dari rata-rata harga masuk terbaru
     */
    public function getDetailBarang(Request $request)
    {
        $itemId    = $this->resolveItemId($request);
        $idLokasi  = $request->id_lokasi;
        $idKondisi = $request->id_kondisi;
        $userId    = auth()->id();

        $barangMasuk = BarangMasuk::with(['item.satuan', 'lokasi', 'kondisi'])
            ->where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('id_lokasi', $idLokasi)
            ->where('id_kondisi', $idKondisi)
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->first();

        if (!$barangMasuk) {
            return response()->json([
                'stok'         => 0,
                'lokasi'       => '-',
                'kondisi'      => '-',
                'nama_barang'  => '-',
                'satuan'       => '-',
                'id_lokasi'    => null,
                'id_kondisi'   => null,
                'harga_dasar'  => 0,
            ]);
        }

        // stok
        $masuk = BarangMasuk::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('id_lokasi', $idLokasi)
            ->where('id_kondisi', $idKondisi)
            ->sum('jumlah');

        $keluar = BarangKeluar::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('id_lokasi', $idLokasi)
            ->where('id_kondisi', $idKondisi)
            ->sum('jumlah_keluar');

        // ❗ harga_dasar = RATA-RATA harga_satuan
        $wac = (float) BarangMasuk::where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where('id_lokasi', $idLokasi)
            ->where('id_kondisi', $idKondisi)
            ->selectRaw('COALESCE(SUM(harga_satuan * jumlah) / NULLIF(SUM(jumlah), 0), 0) as wac')
            ->value('wac');

        return response()->json([
            'stok'         => $masuk - $keluar,
            'lokasi'       => $barangMasuk->lokasi->nama_lokasi ?? '-',
            'kondisi'      => $barangMasuk->kondisi->nama_kondisi ?? '-',
            'nama_barang'  => $barangMasuk->item->nama_barang ?? '-',
            'satuan'       => optional($barangMasuk->item->satuan)->nama_satuan ?? '-',
            'id_lokasi'    => $idLokasi,
            'id_kondisi'   => $idKondisi,
            'harga_dasar'  => (int) round($wac),
        ]);
    }

    /**
     * API: opsi barang unik untuk dropdown
     * -> sertakan 'harga_dasar' dari BarangMasuk.harga_satuan terbaru per kombinasi
     */
    public function getPilihanBarangUnik()
    {
        $userId = auth()->id();

        $grouped = BarangMasuk::with(['item.satuan', 'lokasi', 'kondisi'])
            ->where('user_id', $userId)
            ->get()
            ->groupBy(fn($item) => $item->item_id . '|' . $item->id_lokasi . '|' . $item->id_kondisi)
            ->map(function ($group) use ($userId) {
                $latest = $group->sortByDesc('tanggal_masuk')
                                ->sortByDesc('id')
                                ->first();

                if (!$latest || !$latest->item) return null;

                $itemId  = $latest->item_id;
                $lokasi  = $latest->id_lokasi;
                $kondisi = $latest->id_kondisi;

                // stok
                $masuk = $group->sum('jumlah');
                $keluar = BarangKeluar::where('user_id', $userId)
                    ->where('item_id', $itemId)
                    ->where('id_lokasi', $lokasi)
                    ->where('id_kondisi', $kondisi)
                    ->sum('jumlah_keluar');

                $stok = $masuk - $keluar;
                if ($stok <= 0) return null;

                // ❗ harga_dasar = RATA-RATA harga_satuan pada grup ini
                $wac = (float) $group->avg('harga_satuan');

                return [
                    'item_id'      => $itemId,
                    'kode'         => $latest->item->kode_barang ?? '-',
                    'lokasi_id'    => $lokasi,
                    'kondisi_id'   => $kondisi,
                    'nama_barang'  => $latest->item->nama_barang ?? '-',
                    'lokasi'       => $latest->lokasi->nama_lokasi ?? '-',
                    'kondisi'      => $latest->kondisi->nama_kondisi ?? '-',
                    'stok'         => $stok,
                    'satuan'       => optional($latest->item->satuan)->nama_satuan ?? '-',
                    'harga_dasar'  => (int) round($wac),
                ];
            })
            ->filter();

        return response()->json(array_values($grouped->toArray()));
    }

    public function getLokasiByKode(Request $request)
    {
        $itemId = $this->resolveItemId($request);

        $lokasiList = BarangMasuk::with('lokasi')
            ->where('item_id', $itemId)
            ->groupBy('id_lokasi')
            ->get()
            ->pluck('lokasi')
            ->unique('id')
            ->values();

        return response()->json($lokasiList);
    }

    public function getKondisiByKodeLokasi(Request $request)
    {
        $itemId   = $this->resolveItemId($request);
        $lokasiId = $request->id_lokasi;

        $kondisiList = BarangMasuk::with('kondisi')
            ->where('item_id', $itemId)
            ->where('id_lokasi', $lokasiId)
            ->groupBy('id_kondisi')
            ->get()
            ->pluck('kondisi')
            ->unique('id')
            ->values();

        return response()->json($kondisiList);
    }

    private function resolveItemId(Request $request)
    {
        if ($request->filled('item_id')) {
            return (int) $request->item_id;
        }

        $kode = $request->kode_barang ?: $request->kode;
        if ($kode) {
            $item = Item::where('kode_barang', $kode)->orWhere('id', $kode)->first();
            return $item ? $item->id : null;
        }

        return null;
    }
}