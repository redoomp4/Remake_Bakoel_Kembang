<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\NotDisposableEmail;               // ⬅️ tambahkan rule kustom
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman register.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses submit register.
     */
    public function store(Request $request): RedirectResponse
    {
        // ✅ Validasi (format email RFC + DNS MX, unique, blok disposable)
        //   Perbaikan: 'email' harus berupa array rules (bukan string terpisah)
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email'    => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email', new NotDisposableEmail],
            'phone'    => ['required', 'string'],
            'role'     => ['required', 'string'],
            // 'position' & 'status' opsional di form (set default di bawah)
            'photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'note'     => ['nullable', 'string'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        // Simpan foto jika ada
        $photoPath = $request->file('photo')
            ? $request->file('photo')->store('photos', 'public')
            : null;

        // ===== Default otomatis =====
        // Position: default = role (fallback 'viewer' sudah ditangani oleh value role)
        $role     = strtolower($validated['role']);
        $position = strtolower($request->input('position', $role));

        // Status: default 'active' (ubah ke 'pending' jika kebijakan kamu butuh approval)
        $status   = strtolower($request->input('status', 'active'));

        // Buat user
        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'role'     => $role,
            'position' => $position,   // tidak akan NULL
            'status'   => $status,     // tidak akan NULL
            'photo'    => $photoPath,
            'note'     => $validated['note'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        // Kirim email verifikasi (bawaan Laravel)
        event(new Registered($user));

        // Opsi A (disarankan): jangan login dulu, arahkan ke halaman verifikasi
        // -> memastikan kepemilikan email sebelum akses masuk
        // return redirect()->route('verification.notice')
        //     ->with('status', 'Kami telah mengirim tautan verifikasi ke email Anda.');

        // Opsi B: login dulu tapi tetap arahkan ke halaman verifikasi.
        // (Semua route penting WAJIB pakai middleware "verified")
        Auth::login($user);

        // Arahkan ke halaman verifikasi agar user segera cek email
        return redirect()->route('verification.notice')
            ->with('status', 'Kami telah mengirim tautan verifikasi ke email Anda.');
        //
        // Catatan:
        // Jika kamu tetap ingin langsung arahkan by role:
        //   return redirect(RouteServiceProvider::redirectByRole());
        // pastikan route tujuan diproteksi middleware ['auth','verified'].
    }
}
