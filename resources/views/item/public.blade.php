@extends('layouts.bakoelkembang')

@section('title', 'Detail Barang - Bakoel Kembang')

@section('content')

<div class="public-page flex-grow max-w-3xl w-full mx-auto px-3 py-3 md:px-4 md:py-4 space-y-3">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

        <div>
            <span
                class="text-[8px] font-black uppercase tracking-widest
                       text-brand-emerald bg-emerald-50
                       border border-emerald-100
                       px-2 py-0.5 rounded-md">
                🌸 KATALOG BAKOEL KEMBANG
            </span>

            <h1 class="text-lg md:text-xl font-black text-brand-emerald mt-1.5 tracking-tight">
                Detail Barang
            </h1>

            <p class="text-[10px] text-brand-slate mt-0.5">
                Informasi produk yang tersedia untuk publik.
            </p>
        </div>

        <a href="{{ route('welcome') }}"
           class="inline-flex items-center justify-center gap-1.5
                  px-3 py-2 bg-white
                  border border-brand-accent
                  rounded-lg text-[10px] font-bold
                  text-brand-slate
                  hover:bg-brand-offwhite transition-all">
            <i class="fas fa-home"></i>
            Beranda
        </a>

    </div>


    {{-- MAIN CARD --}}
    <div class="bg-white rounded-xl border border-brand-accent shadow-sm overflow-hidden">

        <div class="grid grid-cols-1 md:grid-cols-2">


            {{-- ================================================= --}}
            {{-- LEFT : FOTO --}}
            {{-- ================================================= --}}

            <div class="bg-brand-offwhite p-3
                        flex flex-col items-center justify-center
                        gap-3 md:border-r border-brand-accent">

                @if($item->foto && Storage::disk('public')->exists($item->foto))

                    <div class="w-full max-w-[220px]
                                rounded-xl overflow-hidden
                                border border-brand-accent
                                bg-white shadow-sm">

                        <img
                            src="{{ Storage::url($item->foto) }}"
                            alt="Foto {{ $item->nama_barang }}"
                            class="w-full aspect-square object-cover">

                    </div>

                @else

                    <div class="w-full max-w-[220px] h-44
                                bg-white rounded-xl
                                border border-brand-accent
                                flex items-center justify-center
                                text-brand-slate">

                        <div class="text-center">

                            <i class="fas fa-seedling
                                      text-3xl text-brand-sage mb-1"></i>

                            <p class="text-[10px] font-bold">
                                Foto Belum Tersedia
                            </p>

                        </div>

                    </div>

                @endif


                {{-- STATUS --}}
                @php
                    $stok = (int) $item->total_stok;
                    $minimum = (int) $item->stok_minimum;

                    if ($stok <= 0) {
                        $status = 'Habis';
                        $statusClass = 'bg-red-50 text-red-700 border-red-100';
                        $statusIcon = 'fa-circle-xmark';
                    } elseif ($stok <= $minimum) {
                        $status = 'Stok Menipis';
                        $statusClass = 'bg-amber-50 text-amber-700 border-amber-100';
                        $statusIcon = 'fa-triangle-exclamation';
                    } else {
                        $status = 'Tersedia';
                        $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                        $statusIcon = 'fa-circle-check';
                    }
                @endphp

                <div class="inline-flex items-center gap-1.5
                            px-3 py-1.5 rounded-full
                            border {{ $statusClass }}
                            text-[9px] font-black uppercase tracking-wider">

                    <i class="fas {{ $statusIcon }}"></i>

                    {{ $status }}

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- RIGHT : DETAIL --}}
            {{-- ================================================= --}}

            <div class="p-4 md:p-5 space-y-4">


                {{-- NAMA --}}
                <div>

                    <span class="text-[8px] font-black uppercase
                                 tracking-widest text-brand-emerald
                                 bg-emerald-50 border border-emerald-100
                                 px-2 py-0.5 rounded-md">

                        🌸 DETAIL BARANG

                    </span>

                    <h2 class="text-lg md:text-xl font-black
                               text-brand-emerald mt-1.5 tracking-tight">

                        {{ $item->nama_barang }}

                    </h2>

                    <p class="text-[9px] font-mono font-bold
                              text-brand-slate mt-1">

                        Kode Barang:

                        <span class="bg-brand-offwhite
                                     px-1.5 py-0.5 rounded
                                     border border-brand-accent">

                            {{ $item->kode_barang }}

                        </span>

                    </p>

                </div>


                {{-- ================================================= --}}
                {{-- INFO CARDS --}}
                {{-- ================================================= --}}

                <div class="grid grid-cols-2 gap-2">


                    {{-- STOK --}}
                    <div class="bg-blue-50/70 p-2.5 rounded-xl
                                border border-blue-100">

                        <span class="text-[7px] font-black uppercase
                                     tracking-widest text-blue-600">

                            📦 STOK TERSEDIA

                        </span>

                        <p class="text-base md:text-lg font-black
                                  text-blue-700 mt-0.5">

                            {{ number_format($stok) }}

                            <span class="text-[9px] font-bold">
                                {{ $item->satuan->nama_satuan ?? 'unit' }}
                            </span>

                        </p>

                    </div>


                    {{-- HARGA --}}
                    <div class="bg-emerald-50/70 p-2.5 rounded-xl
                                border border-emerald-100">

                        <span class="text-[7px] font-black uppercase
                                     tracking-widest text-emerald-600">

                            💰 HARGA DASAR

                        </span>

                        <p class="text-base md:text-lg font-black
                                  text-brand-emerald mt-0.5">

                            Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}

                        </p>

                    </div>


                    {{-- KATEGORI --}}
                    <div class="bg-amber-50/70 p-2.5 rounded-xl
                                border border-amber-100">

                        <span class="text-[7px] font-black uppercase
                                     tracking-widest text-amber-600">

                            🌱 KATEGORI

                        </span>

                        <p class="text-sm font-black
                                  text-amber-700 mt-0.5">

                            {{ $item->kategori->kategori ?? '-' }}

                        </p>

                    </div>


                    {{-- SATUAN --}}
                    <div class="bg-purple-50/70 p-2.5 rounded-xl
                                border border-purple-100">

                        <span class="text-[7px] font-black uppercase
                                     tracking-widest text-purple-600">

                            📏 SATUAN

                        </span>

                        <p class="text-sm font-black
                                  text-purple-700 mt-0.5">

                            {{ $item->satuan->nama_satuan ?? '-' }}

                        </p>

                    </div>

                </div>

                <div class="border-t border-brand-accent/40 pt-3">

                    <h4 class="text-[10px] font-black
                               text-brand-emerald
                               uppercase tracking-wider mb-2">

                        Deskripsi Barang

                    </h4>

                    <div class="bg-brand-offwhite
                                rounded-xl p-2.5
                                border border-brand-accent">

                        @if($item->deskripsi)

                            <p class="text-[10px] leading-relaxed
                                      text-brand-slate">

                                {{ $item->deskripsi }}

                            </p>

                        @else

                            <p class="text-[10px] italic text-brand-slate">

                                Belum ada deskripsi barang.

                            </p>

                        @endif

                    </div>

                </div>

                <div class="border-t border-brand-accent/40 pt-3">

                    <h4 class="text-[10px] font-black
                               text-brand-emerald
                               uppercase tracking-wider mb-2">

                        Ketersediaan

                    </h4>

                    <div class="space-y-1.5 text-[10px]">

                        <div class="flex justify-between items-center
                                    py-1.5 border-b border-gray-100">

                            <span class="font-bold text-brand-slate">
                                Status
                            </span>

                            <span class="font-extrabold
                                {{ $status === 'Tersedia'
                                    ? 'text-emerald-600'
                                    : ($status === 'Stok Menipis'
                                        ? 'text-amber-600'
                                        : 'text-red-600') }}">

                                {{ $status }}

                            </span>

                        </div>


                        <div class="flex justify-between items-center
                                    py-1.5 border-b border-gray-100">

                            <span class="font-bold text-brand-slate">
                                Stok Minimum
                            </span>

                            <span class="font-extrabold text-gray-900">

                                {{ number_format($minimum) }}
                                {{ $item->satuan->nama_satuan ?? 'unit' }}

                            </span>

                        </div>


                        <div class="flex justify-between items-center py-1.5">

                            <span class="font-bold text-brand-slate">
                                Ketersediaan Saat Ini
                            </span>

                            <span class="font-extrabold text-brand-emerald">

                                {{ number_format($stok) }}
                                {{ $item->satuan->nama_satuan ?? 'unit' }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- FOOTNOTE --}}
    <div class="text-center pb-2">

        <p class="text-[9px] text-brand-slate">
            Informasi produk Bakoel Kembang.
        </p>

        <p class="text-[8px] text-brand-sage mt-0.5">
            Data ketersediaan dapat berubah mengikuti transaksi inventaris.
        </p>

    </div>

</div>

@endsection