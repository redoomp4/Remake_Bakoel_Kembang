<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Lokasi::query()
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            // Grouping agar orWhere tidak mengabaikan filter user_id
            $query->where(function ($q) use ($search) {
                $q->where('nama_lokasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $lokasis = $query->orderBy('nama_lokasi')
            ->paginate(10)
            ->appends($request->only('search'));

        return view('lokasi.index', compact('lokasis'));
    }

    public function create()
    {
        return view('lokasi.create');
    }

    public function store(Request $request)
    {
        $nama = trim((string) $request->input('nama_lokasi'));

        $request->validate([
            'nama_lokasi' => [
                'required', 'string', 'max:120',
                Rule::unique('lokasis', 'nama_lokasi')
                    ->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique'   => 'Nama lokasi ini sudah ada.',
        ]);

        try {
            Lokasi::create([
                'user_id'     => Auth::id(),
                'nama_lokasi' => $nama,
                'deskripsi'   => $request->input('deskripsi'),
            ]);

            return redirect()->route('lokasi.index')
                ->with('success', 'Lokasi berhasil ditambahkan.');
        } catch (QueryException $e) {
            // Duplicate key (MySQL: SQLSTATE 23000 / 1062)
            $sqlState = $e->errorInfo[0] ?? null; // 23000
            $mysqlErr = $e->errorInfo[1] ?? null; // 1062
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama lokasi ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Lokasi $lokasi)
    {
        if ($lokasi->user_id !== Auth::id()) {
            return redirect()->route('lokasi.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        if ($lokasi->user_id !== Auth::id()) {
            return redirect()->route('lokasi.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        $nama = trim((string) $request->input('nama_lokasi'));

        $request->validate([
            'nama_lokasi' => [
                'required', 'string', 'max:120',
                Rule::unique('lokasis', 'nama_lokasi')
                    ->where(fn ($q) => $q->where('user_id', Auth::id()))
                    ->ignore($lokasi->id),
            ],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique'   => 'Nama lokasi ini sudah ada.',
        ]);

        try {
            $lokasi->update([
                'nama_lokasi' => $nama,
                'deskripsi'   => $request->input('deskripsi'),
            ]);

            return redirect()->route('lokasi.index')
                ->with('success', 'Lokasi berhasil diperbarui.');
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama lokasi ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Lokasi $lokasi)
    {
        if ($lokasi->user_id !== Auth::id()) {
            return redirect()->route('lokasi.index')
                ->with('error', 'Anda tidak berhak menghapus data ini.');
        }

        try {
            // Opsional: jika punya relasi yang memakai lokasi, bisa dicek seperti:
            // if (method_exists($lokasi, 'barangMasuk') && $lokasi->barangMasuk()->exists()) { ... }

            $lokasi->delete();

            return redirect()->route('lokasi.index')
                ->with('success', 'Lokasi berhasil dihapus.');
        } catch (QueryException $e) {
            // FK violation (MySQL 1451 / SQLSTATE 23000 / Postgres 23503)
            $mysqlCode = $e->errorInfo[1] ?? null; // 1451
            $sqlState  = $e->errorInfo[0] ?? null; // 23000
            $pgCode    = $e->getCode();           // 23503
            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
