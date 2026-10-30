<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Pemasok;
use App\Models\Satuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['kategori', 'satuan'])
            ->withSum('barangMasuk as total_masuk', 'jumlah')
            ->withSum('barangKeluar as total_keluar', 'jumlah_keluar')
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            // Grouping agar tidak mengabaikan filter user_id
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', (int) $request->kategori);
        }

        if ($request->filled('satuan')) {
            $query->where('id_satuan', (int) $request->satuan);
        }

        $items = $query->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->only('search', 'kategori', 'satuan'));

        $kategoris = Kategori::where('user_id', Auth::id())->orderBy('kategori')->get();
        $satuans = Satuan::where('user_id', Auth::id())->orderBy('nama_satuan')->get();

        return view('item.index', compact('items', 'kategoris', 'satuans'));
    }

    public function create()
    {
        return view('item.create', [
            'kategori' => Kategori::where('user_id', Auth::id())->orderBy('kategori')->get(),
            'satuan' => Satuan::where('user_id', Auth::id())->orderBy('nama_satuan')->get(),
            'pemasoks' => Pemasok::where('user_id', Auth::id())->orderBy('nama_pemasok')->get(),
            'lokasis' => Lokasi::where('user_id', Auth::id())->orderBy('nama_lokasi')->get(),
            'kondisis' => Kondisi::where('user_id', Auth::id())->orderBy('nama_kondisi')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Checkbox HTML hanya mengirim nilai saat dicentang. Normalisasi agar
        // aturan required_if di bawah selalu konsisten.
        $request->merge([
            'buat_stok_awal' => $request->boolean('buat_stok_awal') ? 1 : 0,
            'id_kategori' => trim((string) $request->input('id_kategori')),
            'id_satuan' => trim((string) $request->input('id_satuan')),
            'stok_awal_lokasi' => trim((string) $request->input('stok_awal_lokasi')),
            'stok_awal_kondisi' => trim((string) $request->input('stok_awal_kondisi')),
        ]);

        $rules = [
            'nama_barang' => [
                'required',
                'string',
                'max:150',
                Rule::unique('items', 'nama_barang')
                    ->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],

            'id_kategori' => [
                'required',
                'string',
                'max:100',
            ],

            'id_satuan' => [
                'required',
                'string',
                'max:100',
            ],

            'stok_minimum' => [
                'required',
                'integer',
                'min:0',
            ],

            'deskripsi' => [
                'nullable',
                'string',
                'max:500',
            ],

            'foto' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'buat_stok_awal' => ['required', 'boolean'],
            'stok_awal_jumlah' => ['nullable', 'required_if:buat_stok_awal,1', 'integer', 'min:1'],
            'stok_awal_harga' => ['nullable', 'required_if:buat_stok_awal,1', 'numeric', 'min:0'],
            'stok_awal_pemasok' => ['nullable', 'string', 'max:150'],
            'stok_awal_lokasi' => ['nullable', 'string', 'max:120'],
            'stok_awal_kondisi' => ['nullable', 'string', 'max:120'],
        ];

        $messages = [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.unique' => 'Nama barang sudah digunakan.',

            'id_kategori.required' => 'Kategori wajib diisi atau dipilih.',
            'id_kategori.max' => 'Nama kategori maksimal 100 huruf.',

            'id_satuan.required' => 'Satuan wajib diisi atau dipilih.',
            'id_satuan.max' => 'Nama satuan maksimal 100 huruf.',

            'stok_minimum.required' => 'Stok minimum wajib diisi.',

            'stok_awal_jumlah.required_if' => 'Jumlah stok awal wajib diisi ya Bu/Pak.',
            'stok_awal_jumlah.integer' => 'Jumlah stok awal harus berupa angka.',
            'stok_awal_jumlah.min' => 'Jumlah stok awal minimal 1.',
            'stok_awal_harga.required_if' => 'Harga beli satuan wajib diisi ya Bu/Pak.',
            'stok_awal_harga.numeric' => 'Harga beli satuan harus berupa angka.',
            'stok_awal_harga.min' => 'Harga beli satuan tidak boleh kurang dari 0.',

            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
        ];

        $request->validate($rules, $messages);

        $userId = Auth::id();
        $buatStokAwal = (bool) $request->buat_stok_awal;
        $jumlahStokAwal = (int) $request->input('stok_awal_jumlah', 0);
        $hargaStokAwal = (float) $request->input('stok_awal_harga', 0);

        try {
            $item = DB::transaction(function () use ($request, $userId, $buatStokAwal, $jumlahStokAwal, $hargaStokAwal) {
                $item = new Item;

                $item->user_id = $userId;

                // Kode barang berlaku per user
                $item->kode_barang = Item::generateKodeBarang();

                // Token publik TIDAK BOLEH menggunakan kode barang
                $item->public_token = (string) Str::uuid();

                $item->nama_barang = trim((string) $request->nama_barang);

                $kategori = $this->resolveKategori($userId, $request->id_kategori);
                $satuan = $this->resolveSatuan($userId, $request->id_satuan);

                $item->id_kategori = $kategori->id;
                $item->id_satuan = $satuan->id;

                $item->stok_minimum = (int) $request->stok_minimum;

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
                        'public_token' => $item->public_token,
                    ]);

                    $filename = 'qr_item_'.$item->public_token.'.svg';

                    $relativePath = 'qrcodes/items/'.$filename;

                    $qr = QrCode::format('svg')
                        ->size(300)
                        ->margin(2)
                        ->generate($qrLink);

                    Storage::disk('public')->put(
                        $relativePath,
                        $qr
                    );

                    $item->update([
                        'qr_code' => $relativePath,
                    ]);
                } catch (\Throwable $qrException) {
                    report($qrException);
                    // Item tetap tersimpan meskipun QR gagal dibuat.
                }

                /*
                |--------------------------------------------------------------------------
                | STOK AWAL (OPSIONAL)
                |--------------------------------------------------------------------------
                |
                | Jika pengguna mencentang "Masukkan sebagai stok awal", buat
                | transaksi BarangMasuk dalam transaksi database yang sama.
                |
                */
                if ($buatStokAwal) {
                    $pemasok = $this->resolvePemasok(
                        $userId,
                        $request->input('stok_awal_pemasok')
                    );

                    $lokasi = $this->resolveLokasi(
                        $userId,
                        $request->input('stok_awal_lokasi')
                    );
                    $kondisi = $this->resolveKondisi(
                        $userId,
                        $request->input('stok_awal_kondisi')
                    );

                    BarangMasuk::create([
                        'user_id' => $userId,
                        'item_id' => $item->id,
                        'id_pemasok' => $pemasok->id,
                        'id_lokasi' => $lokasi->id,
                        'id_kondisi' => $kondisi->id,
                        'jumlah' => $jumlahStokAwal,
                        'harga_satuan' => $hargaStokAwal,
                        'total_harga' => $jumlahStokAwal * $hargaStokAwal,
                        'tanggal_masuk' => now(),
                        'tanggal_kadaluarsa' => null,
                        'catatan' => 'Stok awal saat item dibuat',
                    ]);
                }

                return $item->fresh(['kategori', 'satuan']);
            });

            $message = $buatStokAwal
                ? 'Item dan stok awal berhasil ditambahkan beserta QR Code.'
                : 'Item berhasil ditambahkan beserta QR Code.';

            return redirect()
                ->route('item.index')
                ->with('success', $message);
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

    /**
     * Kategori: ID numerik yang sudah ada ATAU nama baru (inline create).
     */
    private function resolveKategori(int $userId, mixed $input): Kategori
    {
        $value = trim((string) $input);

        if (ctype_digit($value)) {
            $kategori = Kategori::where('user_id', $userId)
                ->where('id', (int) $value)
                ->first();

            if ($kategori) {
                return $kategori;
            }
        }

        return Kategori::firstOrCreate(
            ['user_id' => $userId, 'kategori' => $value],
            ['deskripsi' => 'Dibuat otomatis dari Form Item']
        );
    }

    /**
     * Satuan: ID numerik yang sudah ada ATAU nama baru (inline create).
     */
    private function resolveSatuan(int $userId, mixed $input): Satuan
    {
        $value = trim((string) $input);

        if (ctype_digit($value)) {
            $satuan = Satuan::where('user_id', $userId)
                ->where('id', (int) $value)
                ->first();

            if ($satuan) {
                return $satuan;
            }
        }

        return Satuan::firstOrCreate([
            'user_id' => $userId,
            'nama_satuan' => $value,
        ]);
    }

    /**
     * Pemasok default "Pemasok Umum" untuk stok awal.
     * Menerima ID numerik atau nama pemasok baru.
     */
    private function resolvePemasok(int $userId, mixed $input): Pemasok
    {
        $value = trim((string) $input);

        if ($value === '') {
            $value = 'Pemasok Umum';
        }

        if (ctype_digit($value)) {
            $pemasok = Pemasok::where('user_id', $userId)
                ->where('id', (int) $value)
                ->first();

            if ($pemasok) {
                return $pemasok;
            }
        }

        return Pemasok::firstOrCreate([
            'user_id' => $userId,
            'nama_pemasok' => $value,
        ]);
    }

    private function resolveLokasi(int $userId, mixed $input): Lokasi
    {
        $value = trim((string) $input);

        if ($value === '') {
            $value = 'Gudang Utama';

            $lokasi = Lokasi::where('user_id', $userId)
                ->where('nama_lokasi', $value)
                ->first()
                ?? Lokasi::where('user_id', $userId)->orderBy('id')->first();

            if ($lokasi) {
                return $lokasi;
            }
        } elseif (ctype_digit($value)) {
            $lokasi = Lokasi::where('user_id', $userId)
                ->where('id', (int) $value)
                ->first();

            if ($lokasi) {
                return $lokasi;
            }
        }

        return Lokasi::firstOrCreate([
            'user_id' => $userId,
            'nama_lokasi' => $value,
        ]);
    }

    private function resolveKondisi(int $userId, mixed $input): Kondisi
    {
        $value = trim((string) $input);

        if ($value === '') {
            $value = 'Segar';

            $kondisi = Kondisi::where('user_id', $userId)
                ->where('nama_kondisi', $value)
                ->first()
                ?? Kondisi::where('user_id', $userId)->orderBy('id')->first();

            if ($kondisi) {
                return $kondisi;
            }
        } elseif (ctype_digit($value)) {
            $kondisi = Kondisi::where('user_id', $userId)
                ->where('id', (int) $value)
                ->first();

            if ($kondisi) {
                return $kondisi;
            }
        }

        return Kondisi::firstOrCreate([
            'user_id' => $userId,
            'nama_kondisi' => $value,
        ]);
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
            'nama_barang' => [
                'required',
                'string',
                'max:150',
                Rule::unique('items', 'nama_barang')
                    ->where(fn ($q) => $q->where('user_id', Auth::id()))
                    ->ignore($item->id, 'id'),
            ],
            'id_kategori' => [
                'required',
                Rule::exists('kategoris', 'id')->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'id_satuan' => [
                'required',
                Rule::exists('satuans', 'id')->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'max:5120'],
        ];

        $messages = [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.unique' => 'Nama barang sudah digunakan.',
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_kategori.exists' => 'Kategori tidak valid.',
            'id_satuan.required' => 'Satuan wajib dipilih.',
            'id_satuan.exists' => 'Satuan tidak valid.',

            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
        ];

        $request->validate($rules, $messages);

        try {
            $item->nama_barang = trim((string) $request->nama_barang);
            $item->id_kategori = (int) $request->id_kategori;
            $item->id_satuan = (int) $request->id_satuan;

            $item->stok_minimum = (int) $request->stok_minimum;
            $item->deskripsi = $request->deskripsi;

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
            $sqlState = $e->errorInfo[0] ?? null;
            $pgCode = $e->getCode();

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
                $this->mmToPt(30),
            ],
            'portrait'
        );

        return $pdf->stream(
            "label_{$item->kode_barang}_50x30mm.pdf",
            [
                'Attachment' => false,
            ]
        );
    }

    // helper untuk konversi mm ke pt (point) untuk PDF
    private function mmToPt($mm)
    {
        return $mm * 2.83465;
    }
}
