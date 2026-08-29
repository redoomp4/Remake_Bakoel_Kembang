<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
            'nama_barang'  => [
                'required','string','max:150',
                // unik per user
                Rule::unique('items', 'nama_barang')->where(fn($q)=>$q->where('user_id', Auth::id())),
            ],
            // pastikan relasi milik user yang sama
            'id_kategori'  => [
                'required',
                Rule::exists('kategoris','id')->where(fn($q)=>$q->where('user_id', Auth::id())),
            ],
            'id_satuan'    => [
                'required',
                Rule::exists('satuans','id')->where(fn($q)=>$q->where('user_id', Auth::id())),
            ],
            'stok_minimum' => ['required','integer','min:0'],
            'harga_dasar'  => ['required','numeric','min:0'],
            'deskripsi'    => ['nullable','string','max:500'],
            'foto'         => ['nullable','image','max:2048'],
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
            $item = new Item();
            $item->kode_barang  = Item::generateKodeBarang(); // pastikan method ini ada di model
            $item->user_id      = Auth::id();
            $item->nama_barang  = trim((string)$request->nama_barang);
            $item->id_kategori  = (int)$request->id_kategori;
            $item->id_satuan    = (int)$request->id_satuan;
            
            $item->stok_minimum = (int)$request->stok_minimum;
            $item->harga_dasar  = (float)$request->harga_dasar;
            $item->deskripsi    = $request->deskripsi;

            if ($request->hasFile('foto')) {
                $item->foto = $request->file('foto')->store('foto_barang', 'public');
            }

            $item->save();

            // Generate QR Code otomatis saat menambahkan bunga
            try {
                Storage::disk('public')->makeDirectory('qrcodes');
                $filename = 'qr_' . $item->kode_barang . '_' . Str::random(6) . '.png';
                $relative = 'qrcodes/' . $filename;
                $qrLink   = route('barang-masuk.qrshow.kode', $item->kode_barang);
                $png      = QrCode::format('png')->size(300)->margin(2)->generate($qrLink);
                Storage::disk('public')->put($relative, $png, 'public');
            } catch (\Throwable $ex) {
                // Ignore if QR generation fails
            }

            return redirect()->route('item.index')->with('success', 'Item varietas bunga berhasil ditambahkan & QR Code di-generate.');
        } catch (QueryException $e) {
            // Duplicate key (SQLSTATE 23000) atau constraint lainnya
            $sqlState = $e->errorInfo[0] ?? null; // 23000
            $mysqlErr = $e->errorInfo[1] ?? null; // 1062
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()->with('error', 'Nama barang sudah digunakan.');
            }
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('item.edit', [
            'item'      => $item,
            'kategoris' => Kategori::where('user_id', Auth::id())->orderBy('kategori')->get(),
            'satuans'   => Satuan::where('user_id', Auth::id())->orderBy('nama_satuan')->get(),
            
        ]);
    }

    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $rules = [
            'nama_barang'  => [
                'required','string','max:150',
                Rule::unique('items','nama_barang')
                    ->where(fn($q)=>$q->where('user_id', Auth::id()))
                    ->ignore($item->kode_barang, 'kode_barang'), // abaikan record sendiri
            ],
            'id_kategori'  => [
                'required',
                Rule::exists('kategoris','id')->where(fn($q)=>$q->where('user_id', Auth::id())),
            ],
            'id_satuan'    => [
                'required',
                Rule::exists('satuans','id')->where(fn($q)=>$q->where('user_id', Auth::id())),
            ],
            'stok_minimum' => ['required','integer','min:0'],
            'harga_dasar'  => ['required','numeric','min:0'],
            'deskripsi'    => ['nullable','string','max:500'],
            'foto'         => ['nullable','image','max:2048'],
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
            // Opsional: pre-check jika punya relasi transaksi
            if (method_exists($item, 'barangMasuks') && $item->barangMasuks()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus karena sudah dipakai pada barang masuk.');
            }
            if (method_exists($item, 'barangKeluars') && $item->barangKeluars()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus karena sudah dipakai pada barang keluar.');
            }

            // Hapus file foto kalau ada
            if ($item->foto) {
                Storage::disk('public')->delete($item->foto);
            }

            $item->delete();

            return redirect()->route('item.index')->with('success', 'Item berhasil dihapus.');
        } catch (QueryException $e) {
            // FK violation (MySQL 1451 / SQLSTATE 23000 / Postgres 23503)
            $mysqlCode = $e->errorInfo[1] ?? null; // 1451
            $sqlState  = $e->errorInfo[0] ?? null; // 23000
            $pgCode    = $e->getCode();           // 23503
            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus karena data sudah dipakai pada transaksi.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function show($kode_barang)
    {
        $item = Item::with(['kategori', 'satuan'])
            ->where('kode_barang', $kode_barang)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('item.show', compact('item'));
    }
}
