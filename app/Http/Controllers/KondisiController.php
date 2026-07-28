<?php

namespace App\Http\Controllers;

use App\Models\Kondisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class KondisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kondisi::query()
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            // Grouping agar orWhere tidak mengabaikan filter user_id
            $query->where(function ($q) use ($search) {
                $q->where('nama_kondisi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $kondisis = $query->orderBy('nama_kondisi')
            ->paginate(10)
            ->appends($request->only('search'));

        return view('kondisi.index', compact('kondisis'));
    }

    public function create()
    {
        return view('kondisi.create');
    }

    public function store(Request $request)
    {
        $nama = trim((string) $request->input('nama_kondisi'));

        // Validasi: unik per user
        $request->validate([
            'nama_kondisi' => [
                'required', 'string', 'max:120',
                Rule::unique('kondisis', 'nama_kondisi')
                    ->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama_kondisi.required' => 'Nama kondisi wajib diisi.',
            'nama_kondisi.unique'   => 'Nama kondisi ini sudah ada.',
        ]);

        try {
            Kondisi::create([
                'user_id'      => Auth::id(),
                'nama_kondisi' => $nama,
                'deskripsi'    => $request->input('deskripsi'),
            ]);

            return redirect()
                ->route('kondisi.index')
                ->with('success', 'Data berhasil ditambahkan.');
        } catch (QueryException $e) {
            // Duplicate key (MySQL: SQLSTATE 23000 / 1062)
            $sqlState = $e->errorInfo[0] ?? null; // 23000
            $mysqlErr = $e->errorInfo[1] ?? null; // 1062
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama kondisi ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Kondisi $kondisi)
    {
        // Hanya pemilik yang boleh mengedit
        if ($kondisi->user_id !== Auth::id()) {
            return redirect()->route('kondisi.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('kondisi.edit', compact('kondisi'));
    }

    public function update(Request $request, Kondisi $kondisi)
    {
        if ($kondisi->user_id !== Auth::id()) {
            return redirect()->route('kondisi.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        $nama = trim((string) $request->input('nama_kondisi'));

        // Validasi: unik per user, abaikan record sendiri
        $request->validate([
            'nama_kondisi' => [
                'required', 'string', 'max:120',
                Rule::unique('kondisis', 'nama_kondisi')
                    ->where(fn ($q) => $q->where('user_id', Auth::id()))
                    ->ignore($kondisi->id),
            ],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama_kondisi.required' => 'Nama kondisi wajib diisi.',
            'nama_kondisi.unique'   => 'Nama kondisi ini sudah ada.',
        ]);

        try {
            $kondisi->update([
                'nama_kondisi' => $nama,
                'deskripsi'    => $request->input('deskripsi'),
            ]);

            return redirect()
                ->route('kondisi.index')
                ->with('success', 'Data berhasil diubah.');
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama kondisi ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat mengubah data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    public function destroy(Kondisi $kondisi)
    {
        if ($kondisi->user_id !== Auth::id()) {
            return redirect()->route('kondisi.index')
                ->with('error', 'Anda tidak berhak menghapus data ini.');
        }

        try {
            // Opsional: pre-check relasi agar pesan jelas
            $dipakaiDiMasuk  = method_exists($kondisi, 'barangMasuks')  && $kondisi->barangMasuks()->exists();
            $dipakaiDiKeluar = method_exists($kondisi, 'barangKeluars') && $kondisi->barangKeluars()->exists();
            if ($dipakaiDiMasuk || $dipakaiDiKeluar) {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.');
            }

            $kondisi->delete();

            return redirect()
                ->route('kondisi.index')
                ->with('success', 'Kondisi berhasil dihapus.');
        } catch (QueryException $e) {
            // FK violation (MySQL 1451 / SQLSTATE 23000 / Postgres 23503)
            $mysqlCode = $e->errorInfo[1] ?? null; // 1451
            $sqlState  = $e->errorInfo[0] ?? null; // 23000
            $pgCode    = $e->getCode();           // 23503
            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.');
            }

            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
