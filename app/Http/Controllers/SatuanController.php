<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Satuan::query()
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('nama_satuan', 'like', "%{$search}%");
        }

        $satuans = $query->orderBy('nama_satuan')
            ->paginate(10)
            ->appends($request->only('search'));

        return view('satuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $nama = trim((string) $request->input('nama_satuan'));

        $request->validate([
            'nama_satuan' => [
                'required', 'string', 'max:255',
                Rule::unique('satuans', 'nama_satuan')
                    ->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique'   => 'Nama satuan ini sudah ada.',
        ]);

        try {
            Satuan::create([
                'user_id'     => Auth::id(),
                'nama_satuan' => $nama,
            ]);

            return redirect()->route('satuan.index')
                ->with('success', 'Satuan berhasil ditambahkan.');
        } catch (QueryException $e) {
            // Duplicate key (MySQL: SQLSTATE 23000 / error 1062)
            $sqlState = $e->errorInfo[0] ?? null; // 23000
            $mysqlErr = $e->errorInfo[1] ?? null; // 1062
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama satuan ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Satuan $satuan)
    {
        if ($satuan->user_id !== Auth::id()) {
            return redirect()->route('satuan.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        if ($satuan->user_id !== Auth::id()) {
            return redirect()->route('satuan.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        $nama = trim((string) $request->input('nama_satuan'));

        $request->validate([
            'nama_satuan' => [
                'required', 'string', 'max:255',
                Rule::unique('satuans', 'nama_satuan')
                    ->where(fn ($q) => $q->where('user_id', Auth::id()))
                    ->ignore($satuan->id),
            ],
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique'   => 'Nama satuan ini sudah ada.',
        ]);

        try {
            $satuan->update([
                'nama_satuan' => $nama,
            ]);

            return redirect()->route('satuan.index')
                ->with('success', 'Satuan berhasil diperbarui.');
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama satuan ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Satuan $satuan)
    {
        if ($satuan->user_id !== Auth::id()) {
            return redirect()->route('satuan.index')
                ->with('error', 'Anda tidak berhak menghapus data ini.');
        }

        try {
            // Opsional: cegah hapus jika dipakai relasi
            if (method_exists($satuan, 'items') && $satuan->items()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus karena sudah digunakan untuk item.');
            }

            $satuan->delete();

            return redirect()->route('satuan.index')
                ->with('success', 'Satuan berhasil dihapus.');
        } catch (QueryException $e) {
            $mysqlCode = $e->errorInfo[1] ?? null;   // 1451 (MySQL FK)
            $sqlState  = $e->errorInfo[0] ?? null;   // 23000
            $pgCode    = $e->getCode();              // 23503 (Postgres)
            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus karena sudah dipakai oleh data lain.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
