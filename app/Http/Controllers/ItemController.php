<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['kategori', 'satuan' /* jika relasi ada */])
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = trim((string)$request->search);
            // Grouping agar tidak mengabaikan filter user_id
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', (int)$request->kategori);
        }

        if ($request->filled('satuan')) {
            $query->where('id_satuan', (int)$request->satuan);
        }

        $items = $query->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->only('search', 'kategori', 'satuan'));

        $kategoris = Kategori::where('user_id', Auth::id())->orderBy('kategori')->get();
        $satuans   = Satuan::where('user_id', Auth::id())->orderBy('nama_satuan')->get();


        return view('item.index', compact('items', 'kategoris', 'satuans'));
    }

    public function create()
    {
        return view('item.create', [
            'kategori' => Kategori::where('user_id', Auth::id())->orderBy('kategori')->get(),
            'satuan'   => Satuan::where('user_id', Auth::id())->orderBy('nama_satuan')->get(),

        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_barang' => [
                'required',
                'string',
                'max:150',
                Rule::unique('items', 'nama_barang')
                    ->where(fn($q) => $q->where('user_id', Auth::id())),
            ],

            'id_kategori' => [
                'required',
                Rule::exists('kategoris', 'id')
                    ->where(fn($q) => $q->where('user_id', Auth::id())),
            ],

            'id_satuan' => [
                'required',
                Rule::exists('satuans', 'id')
                    ->where(fn($q) => $q->where('user_id', Auth::id())),
            ],

            'stok_minimum' => [
                'required',
                'integer',
                'min:0'
            ],

            'harga_dasar' => [
                'required',
                'numeric',
                'min:0'
            ],

            'deskripsi' => [
                'nullable',
                'string',
                'max:500'
            ],

            'foto' => [
                'nullable',
                'image',
                'max:2048'
            ],
        ];

        $messages = [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.unique' => 'Nama barang sudah digunakan.',

            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak valid.',

            'id_satuan.required' => 'Satuan wajib dipilih.',
            'id_satuan.exists' => 'Satuan tidak valid.',

            'stok_minimum.required' => 'Stok minimum wajib diisi.',

            'harga_dasar.required' => 'Harga dasar wajib diisi.',

            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        try {

            $item = new Item();

            $item->user_id = Auth::id();

            // Kode barang berlaku per user
            $item->kode_barang = Item::generateKodeBarang();

            // Token publik TIDAK BOLEH menggunakan kode barang
            $item->public_token = (string) Str::uuid();

            $item->nama_barang = trim((string) $request->nama_barang);

            $item->id_kategori = (int) $request->id_kategori;
            $item->id_satuan = (int) $request->id_satuan;

            $item->stok_minimum = (int) $request->stok_minimum;

            $item->harga_dasar = (float) $request->harga_dasar;

            $item->deskripsi = $request->deskripsi;

            if ($request->hasFile('foto')) {
                $item->foto = $request
                    ->file('foto')
                    ->store('foto_barang', 'public');
            }

            $item->save();

            /*
            |--------------------------------------------------------------------------
            | Generate QR Code ITEM
            |--------------------------------------------------------------------------
            |
            | QR hanya dibuat sekali ketika ITEM dibuat.
            | Barang masuk tidak membuat QR baru.
            |
            */

            try {
                Storage::disk('public')->makeDirectory('qrcodes/items');

                // URL publik menggunakan public_token
                $qrLink = route('item.public', [
                    'public_token' => $item->public_token
                ]);

                $filename = 'qr_item_' . $item->public_token . '.svg';

                $relativePath = 'qrcodes/items/' . $filename;

                $qr = QrCode::format('svg')
                    ->size(300)
                    ->margin(2)
                    ->generate($qrLink);

                Storage::disk('public')->put(
                    $relativePath,
                    $qr
                );

                $item->update([
                    'qr_code' => $relativePath
                ]);
            } catch (\Throwable $qrException) {
                report($qrException);
                // Item tetap tersimpan meskipun QR gagal dibuat.
            }

            return redirect()
                ->route('item.index')
                ->with(
                    'success',
                    'Item berhasil ditambahkan beserta QR Code.'
                );
        } catch (QueryException $e) {

            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;

            if ($sqlState === '23000' || $mysqlErr == 1062) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Kode atau nama barang sudah digunakan.'
                    );
            }

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Terjadi kesalahan saat menyimpan data.'
                );
        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Terjadi kesalahan saat menyimpan data.'
                );
        }
    }

    public function edit(Item $item)
    {
        // Pastikan item milik user yang sedang login
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('item.edit', [
            'item' => $item,

            'kategori' => Kategori::where('user_id', Auth::id())
                ->orderBy('kategori')
                ->get(),

            'satuan' => Satuan::where('user_id', Auth::id())
                ->orderBy('nama_satuan')
                ->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $rules = [
            'nama_barang'  => [
                'required',
                'string',
                'max:150',
                Rule::unique('items', 'nama_barang')
                    ->where(fn($q) => $q->where('user_id', Auth::id()))
                    ->ignore($item->id, 'id'),
            ],
            'id_kategori'  => [
                'required',
                Rule::exists('kategoris', 'id')->where(fn($q) => $q->where('user_id', Auth::id())),
            ],
            'id_satuan'    => [
                'required',
                Rule::exists('satuans', 'id')->where(fn($q) => $q->where('user_id', Auth::id())),
            ],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'harga_dasar'  => ['required', 'numeric', 'min:0'],
            'deskripsi'    => ['nullable', 'string', 'max:500'],
            'foto'         => ['nullable', 'image', 'max:2048'],
        ];

        $messages = [
            'nama_barang.required'  => 'Nama barang wajib diisi.',
            'nama_barang.unique'    => 'Nama barang sudah digunakan.',
            'id_kategori.required'  => 'Kategori wajib dipilih.',
            'id_kategori.exists'    => 'Kategori tidak valid.',
            'id_satuan.required'    => 'Satuan wajib dipilih.',
            'id_satuan.exists'      => 'Satuan tidak valid.',

            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'harga_dasar.required'  => 'Harga dasar wajib diisi.',
            'foto.image'            => 'File foto harus berupa gambar.',
            'foto.max'              => 'Ukuran foto maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        try {
            $item->nama_barang  = trim((string)$request->nama_barang);
            $item->id_kategori  = (int)$request->id_kategori;
            $item->id_satuan    = (int)$request->id_satuan;

            $item->stok_minimum = (int)$request->stok_minimum;
            $item->harga_dasar  = (float)$request->harga_dasar;
            $item->deskripsi    = $request->deskripsi;

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada 
                if ($item->foto) {
                    Storage::disk('public')->delete($item->foto);
                }
                $item->foto = $request->file('foto')->store('foto_barang', 'public');
            }

            $item->save();

            return redirect()->route('item.index')->with('success', 'Item berhasil diperbarui.');
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()->with('error', 'Nama barang sudah digunakan.');
            }
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {

            // ==========================================
            // CEK BARANG MASUK
            // ==========================================
            $sudahAdaBarangMasuk = BarangMasuk::where(
                'item_id',
                $item->id
            )->exists();

            if ($sudahAdaBarangMasuk) {
                return back()->with(
                    'error',
                    'Tidak dapat menghapus karena item sudah dipakai pada barang masuk.'
                );
            }


            // ==========================================
            // CEK BARANG KELUAR
            // ==========================================
            $sudahAdaBarangKeluar = BarangKeluar::where(
                'item_id',
                $item->id
            )->exists();

            if ($sudahAdaBarangKeluar) {
                return back()->with(
                    'error',
                    'Tidak dapat menghapus karena item sudah dipakai pada barang keluar.'
                );
            }


            // ==========================================
            // HAPUS FOTO
            // ==========================================
            if (
                $item->foto &&
                Storage::disk('public')->exists($item->foto)
            ) {
                Storage::disk('public')->delete($item->foto);
            }


            // ==========================================
            // HAPUS QR CODE
            // ==========================================
            if (
                $item->qr_code &&
                Storage::disk('public')->exists($item->qr_code)
            ) {
                Storage::disk('public')->delete($item->qr_code);
            }


            // ==========================================
            // HAPUS ITEM
            // ==========================================
            $item->delete();


            return redirect()
                ->route('item.index')
                ->with(
                    'success',
                    'Item berhasil dihapus.'
                );
        } catch (QueryException $e) {

            $mysqlCode = $e->errorInfo[1] ?? null;
            $sqlState  = $e->errorInfo[0] ?? null;
            $pgCode    = $e->getCode();

            if (
                $sqlState === '23000' ||
                $mysqlCode == 1451 ||
                $pgCode == '23503'
            ) {
                return back()->with(
                    'error',
                    'Tidak dapat menghapus karena data sudah dipakai pada transaksi.'
                );
            }

            return back()->with(
                'error',
                'Terjadi kesalahan saat menghapus data.'
            );
        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Terjadi kesalahan saat menghapus data.'
            );
        }
    }

    public function show(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $item->load(['kategori', 'satuan']);

        return view('item.show', compact('item'));
    }

    public function publicShow(string $public_token)
    {
        $item = Item::with([
            'kategori',
            'satuan',
        ])
            ->where('public_token', $public_token)
            ->firstOrFail();

        return view('item.public', compact('item'));
    }

    public function cetakPDF($id)
    {
        $item = Item::with([
            'kategori',
            'satuan',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView(
            'item.label',
            compact('item')
        )->setPaper(
            [
                0,
                0,
                $this->mmToPt(50),
                $this->mmToPt(30)
            ],
            'portrait'
        );

        return $pdf->stream(
            "label_{$item->kode_barang}_50x30mm.pdf",
            [
                'Attachment' => false
            ]
        );
    }

    // helper untuk konversi mm ke pt (point) untuk PDF
    private function mmToPt($mm)
    {
        return $mm * 2.83465;
    }
}
