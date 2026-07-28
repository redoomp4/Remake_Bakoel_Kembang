<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query()
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            // Grouping agar orWhere tidak menabrak filter user_id
            $query->where(function ($q) use ($search) {
                $q->where('kategori', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $kategoris = $query->orderBy('kategori')
            ->paginate(10)
            ->appends($request->only('search'));

        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $kategoriValue = trim((string) $request->input('kategori'));

        $request->validate([
            'kategori'  => [
                'required', 'string', 'max:100',
                Rule::unique('kategoris', 'kategori')
                    ->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'kategori.required' => 'Field kategori wajib diisi.',
            'kategori.unique'   => 'Field kategori sudah ada.',
        ]);

        try {
            Kategori::create([
                'user_id'   => Auth::id(),
                'kategori'  => $kategoriValue,
                'deskripsi' => $request->input('deskripsi'),
            ]);

            return redirect()
                ->route('kategori.index')
                ->with('success', 'Kategori berhasil ditambahkan.');
        } catch (QueryException $e) {
            // Antisipasi duplicate key dari DB (MySQL: 23000/1062)
            $sqlState = $e->errorInfo[0] ?? null; // 23000
            $mysqlErr = $e->errorInfo[1] ?? null; // 1062
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Field kategori sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Kategori $kategori)
    {
        if ($kategori->user_id !== Auth::id()) {
            return redirect()
                ->route('kategori.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        if ($kategori->user_id !== Auth::id()) {
            return redirect()
                ->route('kategori.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        $kategoriValue = trim((string) $request->input('kategori'));

        $request->validate([
            'kategori'  => [
                'required', 'string', 'max:100',
                Rule::unique('kategoris', 'kategori')
                    ->where(fn ($q) => $q->where('user_id', Auth::id()))
                    ->ignore($kategori->id),
            ],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'kategori.required' => 'Field kategori wajib diisi.',
            'kategori.unique'   => 'Field kategori sudah ada.',
        ]);

        try {
            $kategori->update([
                'kategori'  => $kategoriValue,
                'deskripsi' => $request->input('deskripsi'),
            ]);

            return redirect()
                ->route('kategori.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Field kategori sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat mengubah data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->user_id !== Auth::id()) {
            return redirect()
                ->route('kategori.index')
                ->with('error', 'Anda tidak berhak menghapus data ini.');
        }

        try {
            // Opsional: cegah hapus jika sudah dipakai relasi
            if (method_exists($kategori, 'items') && $kategori->items()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            $kategori->delete();

            return redirect()
                ->route('kategori.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (QueryException $e) {
            // FK violation (MySQL 1451 / SQLSTATE 23000 / Postgres 23503)
            $mysqlCode = $e->errorInfo[1] ?? null; // 1451
            $sqlState  = $e->errorInfo[0] ?? null; // 23000
            $pgCode    = $e->getCode();           // 23503
            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
