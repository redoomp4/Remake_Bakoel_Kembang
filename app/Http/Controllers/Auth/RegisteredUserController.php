<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\NotDisposableEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        // Validasi input
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'string', 'email', 'max:191', 'unique:users,email', new NotDisposableEmail],
            'phone'    => ['required', 'string', 'max:30'],
            'role'     => ['nullable', 'string', 'in:admin,kios'],
            'photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'note'     => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'username.required'    => 'Username wajib diisi.',
            'username.alpha_dash'  => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique'      => 'Username ini sudah terdaftar, silakan pilih username lain.',
            'email.required'       => 'Alamat email wajib diisi.',
            'email.email'          => 'Format alamat email tidak valid.',
            'email.unique'         => 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.',
            'phone.required'       => 'Nomor telepon/WA wajib diisi.',
            'password.required'    => 'Kata sandi wajib diisi.',
            'password.confirmed'   => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'         => 'Kata sandi minimal harus 8 karakter.',
        ]);

        // Simpan foto jika ada
        $photoPath = $request->file('photo')
            ? $request->file('photo')->store('photos', 'public')
            : null;

        // Tentukan default role & status (Hanya admin dan kios)
        $role     = strtolower($validated['role'] ?? 'kios');
        if (!in_array($role, ['admin', 'kios'])) {
            $role = 'kios';
        }
        $position = strtolower($request->input('position', $role));
        $status   = 'Active';

        // Simpan user baru ke database
        $user = User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => strtolower($validated['email']),
            'phone'     => $validated['phone'],
            'role'      => $role,
            'position'  => $position,
            'status'    => $status,
            'is_active' => true,
            'photo'     => $photoPath,
            'note'      => $validated['note'] ?? null,
            'password'  => Hash::make($validated['password']),
        ]);

        // Kirim event verifikasi email secara aman (try-catch agar kendala mail server tidak menggagalkan registrasi)
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::warning('Email verification notice could not be sent: ' . $e->getMessage());
        }

        // Arahkan ke halaman login dengan pesan sukses berhasil
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda telah berhasil disimpan di database. Silakan masuk.');
    }
}

