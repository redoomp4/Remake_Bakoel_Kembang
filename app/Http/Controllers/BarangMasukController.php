<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Pemasok;
use App\Models\Lokasi;
use App\Models\Kondisi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $user     = Auth::user();
        $search   = $request->input('search');
        $lokasiId = $request->input('lokasi');

        $barangMasuks = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])
            ->where('user_id', $user->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('item', fn ($qi) => $qi->where('nama_barang', 'like', "%{$search}%"))
                      ->orWhereHas('lokasi', fn ($ql) => $ql->where('nama_lokasi', 'like', "%{$search}%"))
                      ->orWhereHas('kondisi', fn ($qk) => $qk->where('nama_kondisi', 'like', "%{$search}%"))
                      ->orWhereHas('pemasok', fn ($qp) => $qp->where('nama_pemasok', 'like', "%{$search}%"));
                });
            })
            ->when($lokasiId, fn ($q) => $q->where('id_lokasi', $lokasiId))
            ->latest()
            ->paginate(10);

        // Hanya lokasi yang muncul pada data Barang Masuk milik user ini
        $lokasiIds = BarangMasuk::where('user_id', auth()->id())
            ->distinct()
            ->pluck('id_lokasi');

        $lokasis = Lokasi::whereIn('id', $lokasiIds)
            ->orderBy('nama_lokasi')
            ->get();

        return view('barangmasuk.index', compact('barangMasuks', 'lokasis'));
    }

    public function create()
    {
        return view('barangmasuk.create', [
            'items'    => Item::where('user_id', Auth::id())->get(),
            'pemasoks' => Pemasok::where('user_id', Auth::id())->get(),
            'lokasis'  => Lokasi::where('user_id', Auth::id())->get(),
            'kondisis' => Kondisi::where('user_id', Auth::id())->get(),
            'users'    => User::all(),
        ]);
    }

    public function qrShow($id)
    {
        $barangMasuk = BarangMasuk::with([
            'item',
            'kondisi',
            'lokasi',
            'pemasok',
            'user',
        ])->findOrFail($id);

        return view('barangmasuk.detailbarang', compact('barangMasuk'));
    }

    public function qrShowByKode($kode_barang)
    {
        $item = Item::where('kode_barang', $kode_barang)->orWhere('id', $kode_barang)->first();

        $barangMasuk = null;
        if ($item) {
            $barangMasuk = BarangMasuk::with([
                'item',
                'kondisi',
                'lokasi',
            ])->where('item_id', $item->id)->latest('tanggal_masuk')->first();
        }

        if (!$barangMasuk) {
            if (!$item) {
                abort(404, 'Data barang tidak ditemukan.');
            }
            $barangMasuk = new BarangMasuk();
            $barangMasuk->item_id = $item->id;
            $barangMasuk->jumlah = 0;
            $barangMasuk->harga_satuan = $item->harga_dasar ?? 0;
            $barangMasuk->total_harga = 0;
            $barangMasuk->tanggal_masuk = $item->created_at ?? now();
            $barangMasuk->setRelation('item', $item);
        }

        return view('barangmasuk.qr_show_generate', compact('barangMasuk'));
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

        $validated = $request->validate([
            'item_id'            => 'required|exists:items,id',
            'jumlah'             => 'required|integer|min:1',
            'harga_satuan'       => 'required|numeric|min:0',
            'total_harga'        => 'nullable|numeric',
            'tanggal_masuk'      => 'required|date',
            'tanggal_kadaluarsa' => 'nullable|date|after_or_equal:tanggal_masuk',
            'id_pemasok'         => 'required|exists:pemasoks,id',
            'id_lokasi'          => 'required|exists:lokasis,id',
            'id_kondisi'         => 'required|exists:kondisis,id',
            'catatan'            => 'nullable|string|max:1000',
        ]);

        try {
            $total = $validated['jumlah'] * $validated['harga_satuan'];

            $barang = BarangMasuk::create([
                'item_id'            => $validated['item_id'],
                'jumlah'             => $validated['jumlah'],
                'harga_satuan'       => $validated['harga_satuan'],
                'total_harga'        => $total,
                'tanggal_masuk'      => $validated['tanggal_masuk'],
                'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'] ?? null,
                'id_pemasok'         => $validated['id_pemasok'],
                'id_lokasi'          => $validated['id_lokasi'],
                'id_kondisi'         => $validated['id_kondisi'],
                'catatan'            => $validated['catatan'] ?? null,
                'user_id'            => Auth::id(),
            ]);

            // Generate & simpan QR
            $this->generateAndSaveQr($barang);

            return redirect()
                ->route('barang-masuk.index')
                ->with('success', 'Barang masuk berhasil ditambahkan beserta QR Code.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        return view('barangmasuk.edit', [
            'barangMasuk' => BarangMasuk::findOrFail($id),
            'items'       => Item::where('user_id', Auth::id())->get(),
            'pemasoks'    => Pemasok::where('user_id', Auth::id())->get(),
            'lokasis'     => Lokasi::where('user_id', Auth::id())->get(),
            'kondisis'    => Kondisi::where('user_id', Auth::id())->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!$request->filled('item_id') && $request->filled('kode_barang')) {
            $matchedItem = Item::where('kode_barang', $request->kode_barang)->first();
            if ($matchedItem) {
                $request->merge(['item_id' => $matchedItem->id]);
            }
        }

        $validated = $request->validate([
            'item_id'            => 'required|exists:items,id',
            'jumlah'             => 'required|integer|min:1',
            'harga_satuan'       => 'required|numeric|min:0',
            'tanggal_masuk'      => 'required|date',
            'tanggal_kadaluarsa' => 'nullable|date|after_or_equal:tanggal_masuk',
            'id_pemasok'         => 'required|exists:pemasoks,id',
            'id_lokasi'          => 'required|exists:lokasis,id',
            'id_kondisi'         => 'required|exists:kondisis,id',
            'catatan'            => 'nullable|string',
        ]);

        $barang = BarangMasuk::findOrFail($id);

        $oldItemId = $barang->item_id;

        $barang->update([
            'item_id'            => $validated['item_id'],
            'jumlah'             => $validated['jumlah'],
            'harga_satuan'       => $validated['harga_satuan'],
            'total_harga'        => $validated['jumlah'] * $validated['harga_satuan'],
            'tanggal_masuk'      => $validated['tanggal_masuk'],
            'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'] ?? null,
            'id_pemasok'         => $validated['id_pemasok'],
            'id_lokasi'          => $validated['id_lokasi'],
            'id_kondisi'         => $validated['id_kondisi'],
            'catatan'            => $validated['catatan'] ?? null,
        ]);

        // Jika item_id berubah, regenerasi QR
        if ($oldItemId !== $barang->item_id) {
            // Hapus file QR lama jika ada
            if ($barang->qr_code && Storage::disk('public')->exists($barang->qr_code)) {
                Storage::disk('public')->delete($barang->qr_code);
            }
            $this->generateAndSaveQr($barang);
        }

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Data barang masuk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        if ($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code)) {
            Storage::disk('public')->delete($barangMasuk->qr_code);
        }

        $barangMasuk->delete();

        return redirect()
            ->route('barang-masuk.index')
            ->with('success', 'Data barang masuk berhasil dihapus.');
    }

    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])->findOrFail($id);
        return view('barangmasuk.show', compact('barangMasuk'));
    }

    public function qrCard($id)
    {
        $barangMasuk = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])->findOrFail($id);
        return view('barangmasuk.detailbarang', compact('barangMasuk'));
    }

    public function cetakPDF($id)
    {
        $barangMasuk = BarangMasuk::with(['item.kategori','lokasi','kondisi','user'])
                        ->findOrFail($id);
                        

        // 50 x 30 mm (ubah sesuai labelmu)
        $wPt = $this->mmToPt(50);
        $hPt = $this->mmToPt(30);

        $pdf = Pdf::loadView('barangmasuk.label', compact('barangMasuk'))
          ->setPaper([0,0,$this->mmToPt(50), $this->mmToPt(30)], 'portrait');

        return $pdf->stream("label_{$barangMasuk->kode_barang}_50x30mm.pdf", ['Attachment' => false]);
    }

    /** 1 mm ≈ 2.83465 pt */
    protected function mmToPt($mm)
    {
        return $mm * 2.83465;
    }


    public function cetakBeritaAcara($id)
    {
        $barangMasuk = BarangMasuk::with(['item', 'lokasi', 'kondisi', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('barangmasuk.berita_acara_pdf', [
            'barangMasuk'     => $barangMasuk,
            'tanggal_lengkap' => now()->translatedFormat('d F Y'),
            'hari'            => now()->translatedFormat('l'),
            'bulan'           => now()->format('m'),
            'tahun'           => now()->format('Y'),
            'nomor'           => str_pad($barangMasuk->id, 3, '0', STR_PAD_LEFT),
            'lokasi'          => $barangMasuk->lokasi->nama_lokasi ?? '-',
        ])->setPaper('A4', 'portrait');

        // Sudah inline
        return $pdf->stream('berita_acara_barang_masuk.pdf', ['Attachment' => false]);
    }

    public function cetakQRKecil($id)
    {
        $barangMasuk = BarangMasuk::with(['item'])->findOrFail($id);

        $qrBase64 = null;
        if ($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code)) {
            $qrContent = Storage::disk('public')->get($barangMasuk->qr_code);
            $qrBase64  = 'data:image/png;base64,' . base64_encode($qrContent);
        }

        $pdf = Pdf::loadView('barangmasuk.qr_only_pdf', compact('barangMasuk', 'qrBase64'))
                ->setPaper('A7', 'portrait');

        // Inline viewer
        return $pdf->stream('qr_kecil_' . $barangMasuk->kode_barang . '.pdf', ['Attachment' => false]);
    }


    /**
     * Helper: Generate QR dari route publik dan simpan ke disk 'public'.
     * Menyimpan path relatif ke kolom qr_code.
     */
    protected function generateAndSaveQr(BarangMasuk $barang): void
    {
        // Data yang diencode dalam QR → link publik berdasarkan kode barang
        $qrData = route('barang-masuk.qrshow.kode', $barang->kode_barang);

        // Pastikan folder ada
        Storage::disk('public')->makeDirectory('qrcodes');

        // Nama file (gunakan kode + random agar aman jika ada duplikasi)
        $filename   = 'qr_' . $barang->kode_barang . '_' . Str::random(6) . '.svg';
        $relative   = 'qrcodes/' . $filename;

        // Render QR → simpan
        $svg = QrCode::format('svg')->size(300)->margin(2)->generate($qrData);
        Storage::disk('public')->put($relative, $svg, 'public');

        // Simpan ke DB
        $barang->update(['qr_code' => $relative]);
    }
}
