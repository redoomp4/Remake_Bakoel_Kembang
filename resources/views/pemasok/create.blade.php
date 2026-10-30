@extends('layouts.app')

@section('content')
    @php
        $hasAdvancedError = collect(['nama_pic', 'email', 'jenis', 'alamat'])
            ->contains(fn ($field) => $errors->has($field));
    @endphp

    <div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">Data Pemasok</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-emerald-950 sm:text-4xl">Tambah Pemasok Baru</h1>
                <p class="mt-2 max-w-xl text-base font-semibold leading-7 text-slate-600">
                    Cukup isi <strong class="text-emerald-800">nama pemasok</strong> saja. Kolom lain boleh dikosongkan.
                </p>
            </div>

            <a href="{{ route('pemasok.index') }}"
                class="inline-flex w-fit items-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-600 shadow-sm transition hover:border-emerald-600 hover:text-emerald-700">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- FLASH ERROR --}}
        @if (session('error'))
            <div class="mb-5 flex items-start gap-3 rounded-2xl border-2 border-rose-300 bg-rose-50 p-4 text-base font-bold text-rose-900">
                <i class="fas fa-exclamation-triangle mt-0.5 text-xl text-rose-600"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border-2 border-rose-300 bg-rose-50 p-4 text-rose-900">
                <p class="flex items-center gap-2 text-base font-black">
                    <i class="fas fa-circle-exclamation text-rose-600"></i>
                    Data belum bisa disimpan
                </p>
                <ul class="mt-2 list-disc space-y-1 pl-6 text-sm font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pemasok.store') }}" method="POST"
            class="rounded-3xl border-2 border-emerald-200 bg-white p-5 shadow-sm sm:p-7">
            @csrf

            {{-- FIELD UTAMA: NAMA PEMASOK --}}
            <div class="rounded-2xl border-2 border-emerald-200 bg-emerald-50/70 p-4 sm:p-5">
                <label for="nama_pemasok" class="mb-2 block text-lg font-black text-slate-900 sm:text-xl">
                    Nama Pemasok / Toko <span class="text-rose-600">*</span>
                </label>

                <input type="text" name="nama_pemasok" id="nama_pemasok" maxlength="150" required autofocus
                    value="{{ old('nama_pemasok') }}"
                    placeholder="Contoh: Toko Bunga Melati"
                    class="h-16 w-full rounded-2xl border-2 border-slate-400 bg-white px-4 text-xl font-black text-slate-900 placeholder:font-semibold placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:text-2xl">

                <p class="mt-2 text-sm font-semibold text-slate-600">
                    <i class="fas fa-lightbulb text-amber-500"></i>
                    Bisa nama toko, petani, grosir, atau perorangan.
                </p>

                @error('nama_pemasok')
                    <p class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- KONTAK & TANGGAL --}}
            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="no_telepon" class="mb-2 block text-base font-black text-slate-800">
                        No. Telepon / WhatsApp
                        <span class="text-xs font-semibold text-slate-400">(opsional)</span>
                    </label>
                    <input type="tel" name="no_telepon" id="no_telepon" maxlength="50" inputmode="tel"
                        value="{{ old('no_telepon') }}"
                        placeholder="Contoh: 0812 3456 7890"
                        class="h-14 w-full rounded-xl border-2 border-slate-300 bg-white px-4 text-lg font-bold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    @error('no_telepon')
                        <p class="mt-1 text-sm font-bold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bergabung_sejak" class="mb-2 block text-base font-black text-slate-800">
                        Bergabung Sejak
                        <span class="text-xs font-semibold text-slate-400">(opsional)</span>
                    </label>
                    <input type="date" name="bergabung_sejak" id="bergabung_sejak"
                        value="{{ old('bergabung_sejak') }}"
                        class="h-14 w-full rounded-xl border-2 border-slate-300 bg-white px-4 text-lg font-bold text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    @error('bergabung_sejak')
                        <p class="mt-1 text-sm font-bold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- INFORMASI TAMBAHAN (OPSIONAL, TERTUTUP) --}}
            <details class="mt-5 rounded-2xl border-2 border-slate-200 bg-slate-50" {{ $hasAdvancedError ? 'open' : '' }}>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 sm:p-5">
                    <span class="flex items-center gap-2 text-base font-black text-slate-800">
                        <i class="fas fa-circle-plus text-emerald-700"></i>
                        Informasi Tambahan
                    </span>
                    <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-500">
                        Opsional — boleh dikosongkan
                    </span>
                </summary>

                <div class="grid grid-cols-1 gap-4 px-4 pb-5 sm:grid-cols-2 sm:px-5">
                    <div>
                        <label for="nama_pic" class="mb-1 block text-sm font-black uppercase tracking-wide text-slate-600">
                            Nama PIC
                        </label>
                        <input type="text" name="nama_pic" id="nama_pic" maxlength="120"
                            value="{{ old('nama_pic') }}"
                            placeholder="Nama penanggung jawab"
                            class="h-12 w-full rounded-xl border-2 border-slate-300 bg-white px-3 text-base font-semibold text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        @error('nama_pic')
                            <p class="mt-1 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-sm font-black uppercase tracking-wide text-slate-600">
                            Email
                        </label>
                        <input type="email" name="email" id="email" maxlength="191"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            class="h-12 w-full rounded-xl border-2 border-slate-300 bg-white px-3 text-base font-semibold text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        @error('email')
                            <p class="mt-1 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis" class="mb-1 block text-sm font-black uppercase tracking-wide text-slate-600">
                            Jenis Pemasok
                        </label>
                        <input type="text" name="jenis" id="jenis" maxlength="50" list="jenis-pemasok-list"
                            value="{{ old('jenis') }}"
                            placeholder="Contoh: Toko Bunga"
                            class="h-12 w-full rounded-xl border-2 border-slate-300 bg-white px-3 text-base font-semibold text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <datalist id="jenis-pemasok-list">
                            <option value="Toko Bunga"></option>
                            <option value="Petani Bunga"></option>
                            <option value="Grosir"></option>
                            <option value="Perorangan"></option>
                            <option value="Lainnya"></option>
                        </datalist>
                        @error('jenis')
                            <p class="mt-1 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="alamat" class="mb-1 block text-sm font-black uppercase tracking-wide text-slate-600">
                            Alamat
                        </label>
                        <textarea name="alamat" id="alamat" rows="3" maxlength="255"
                            placeholder="Alamat lengkap (opsional)"
                            class="w-full rounded-xl border-2 border-slate-300 bg-white px-3 py-2.5 text-base font-semibold text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </details>

            {{-- TOMBOL AKSI --}}
            <div class="mt-6 flex flex-col gap-3 border-t-2 border-slate-100 pt-5 sm:flex-row">
                <button type="submit"
                    class="inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-emerald-600 px-6 py-4 text-lg font-black text-white shadow-lg shadow-emerald-900/20 transition hover:bg-emerald-700 active:scale-[.99] sm:flex-1 sm:text-xl">
                    <i class="fas fa-save"></i> SIMPAN PEMASOK
                </button>

                <a href="{{ route('pemasok.index') }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-slate-300 bg-white px-6 py-4 text-lg font-black text-slate-600 transition hover:bg-slate-100 sm:w-auto">
                    <i class="fas fa-xmark"></i> Batal
                </a>
            </div>
        </form>
    </div>
@endsection
