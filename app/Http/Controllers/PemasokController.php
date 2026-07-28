<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class PemasokController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasok::query()
            ->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            // group kondisi pencarian agar tidak "bocor" dari filter user_id
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemasok', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jenis', 'like', "%{$search}%")
                  ->orWhere('nama_pic', 'like', "%{$search}%")
                  ->orWhere('bergabung_sejak', 'like', "%{$search}%");
            });
        }

        $pemasoks = $query->orderBy('nama_pemasok')
            ->paginate(10)
            ->appends($request->only('search'));

        return view('pemasok.index', compact('pemasoks'));
    }

    public function create()
    {
        return view('pemasok.create');
    }

    public function store(Request $request)
    {
        // normalisasi ringan
        $nama   = trim((string) $request->input('nama_pemasok'));
        $email  = $request->input('email');

        $request->validate([
            'nama_pemasok' => [
                'required','string','max:255',
                Rule::unique('pemasoks','nama_pemasok')
                    ->where(fn($q)=>$q->where('user_id', Auth::id())),
            ],
            'email' => [
                'nullable','email','max:191',
                // opsional: jaga unik email per user jika migrasi menambahkan unique gabungan (user_id,email)
                Rule::unique('pemasoks','email')
                    ->where(fn($q)=>$q->where('user_id', Auth::id())),
            ],
            'no_telepon'      => ['nullable','string','max:20'],
            'alamat'          => ['nullable','string'],
            'jenis'           => ['nullable','string','max:50'],
            'bergabung_sejak' => ['nullable','date'],
            'nama_pic'        => ['nullable','string','max:255'],
        ], [
            'nama_pemasok.required' => 'Nama pemasok wajib diisi.',
            'nama_pemasok.unique'   => 'Nama pemasok ini sudah ada.',
            'email.email'           => 'Format email tidak valid.',
        ]);

        try {
            Pemasok::create([
                'user_id'        => Auth::id(),
                'nama_pemasok'   => $nama,
                'email'          => $email,
                'no_telepon'     => $request->input('no_telepon'),
                'alamat'         => $request->input('alamat'),
                'jenis'          => $request->input('jenis'),
                'bergabung_sejak'=> $request->input('bergabung_sejak'),
                'nama_pic'       => $request->input('nama_pic'),
            ]);

            return redirect()->route('pemasok.index')
                ->with('success', 'Pemasok berhasil ditambahkan');
        } catch (QueryException $e) {
            // Duplicate key (MySQL: SQLSTATE 23000 / 1062)
            $sqlState = $e->errorInfo[0] ?? null; // 23000
            $mysqlErr = $e->errorInfo[1] ?? null; // 1062
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama/email pemasok ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Pemasok $pemasok)
    {
        // hanya pemilik yang boleh mengedit
        if ($pemasok->user_id !== Auth::id()) {
            return redirect()->route('pemasok.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        return view('pemasok.edit', compact('pemasok'));
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        if ($pemasok->user_id !== Auth::id()) {
            return redirect()->route('pemasok.index')
                ->with('error', 'Anda tidak berhak mengedit data ini.');
        }

        $nama  = trim((string) $request->input('nama_pemasok'));
        $email = $request->input('email');

        $request->validate([
            'nama_pemasok' => [
                'required','string','max:255',
                Rule::unique('pemasoks','nama_pemasok')
                    ->where(fn($q)=>$q->where('user_id', Auth::id()))
                    ->ignore($pemasok->id),
            ],
            'email' => [
                'nullable','email','max:191',
                Rule::unique('pemasoks','email')
                    ->where(fn($q)=>$q->where('user_id', Auth::id()))
                    ->ignore($pemasok->id),
            ],
            'no_telepon'      => ['nullable','string','max:20'],
            'alamat'          => ['nullable','string'],
            'jenis'           => ['nullable','string','max:50'],
            'bergabung_sejak' => ['nullable','date'],
            'nama_pic'        => ['nullable','string','max:255'],
        ], [
            'nama_pemasok.required' => 'Nama pemasok wajib diisi.',
            'nama_pemasok.unique'   => 'Nama pemasok ini sudah ada.',
            'email.email'           => 'Format email tidak valid.',
        ]);

        try {
            $pemasok->update([
                'nama_pemasok'    => $nama,
                'email'           => $email,
                'no_telepon'      => $request->input('no_telepon'),
                'alamat'          => $request->input('alamat'),
                'jenis'           => $request->input('jenis'),
                'bergabung_sejak' => $request->input('bergabung_sejak'),
                'nama_pic'        => $request->input('nama_pic'),
            ]);

            return redirect()->route('pemasok.index')
                ->with('success', 'Pemasok berhasil diperbarui');
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;
            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()->withInput()
                    ->with('error', 'Nama/email pemasok ini sudah ada.');
            }
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Pemasok $pemasok)
    {
        if ($pemasok->user_id !== Auth::id()) {
            return redirect()->route('pemasok.index')
                ->with('error', 'Anda tidak berhak menghapus data ini.');
        }

        try {
            // Opsional: cegah hapus jika masih dipakai relasi
            if (method_exists($pemasok, 'items') && $pemasok->items()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            $pemasok->delete();

            return redirect()->route('pemasok.index')
                ->with('success', 'Pemasok berhasil dihapus.');
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
