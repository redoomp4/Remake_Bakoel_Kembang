@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#FAF9F6] px-4 py-6 md:px-6 md:py-8">
        <div class="max-w-3xl mx-auto">
            {{-- HEADER --}}
            <div class="mb-6">
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#8FA882]">
                    Inventori · Arus Keluar
                </p>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-1">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-[#0B4F35]">
                            Detail Barang Keluar
                        </h1>
                        <p class="text-sm text-[#475569] mt-1">
                            Informasi lengkap transaksi barang keluar.
                        </p>
                    </div>
                    <a href="{{ route('barang-keluar.index') }}"
                        class="inline-flex items-center justify-center gap-2
                        px-4 py-2.5 rounded-xl
                        bg-white border border-[#E4E4D9]
                        text-sm font-bold text-[#475569]
                        hover:bg-[#FAF9F6] transition">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- CARD --}}
            <div class="bg-white rounded-3xl border border-[#E4E4D9] shadow-sm overflow-hidden">
                {{-- CARD HEADER --}}
                <div
                    class="px-5 py-5 md:px-7
                    border-b border-[#E4E4D9]
                    bg-[#FAF9F6]">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 rounded-xl bg-[#0B4F35]
                            text-white flex items-center justify-center">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-[#0B4F35]">
                                Informasi Transaksi
                            </h2>
                            <p class="text-xs text-[#475569]">
                                Detail barang dan pengeluaran inventori.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- CONTENT --}}
                <div class="p-5 md:p-7 space-y-6">
                    {{-- INFORMASI BARANG --}}
                    <div>
                        <h3 class="text-sm font-black text-[#0B4F35] mb-3">
                            Informasi Barang
                        </h3>
                        <div class="rounded-2xl border border-[#E4E4D9] overflow-hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-2">
                                {{-- KODE --}}
                                <div class="p-4 border-b sm:border-r border-[#E4E4D9]">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Kode Barang
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        {{ $barangKeluar->item->kode_barang ?? '-' }}
                                    </p>
                                </div>

                                {{-- NAMA --}}
                                <div class="p-4 border-b border-[#E4E4D9]">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Nama Barang
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        {{ $barangKeluar->item->nama_barang ?? '-' }}
                                    </p>
                                </div>

                                {{-- JUMLAH --}}
                                <div class="p-4 border-b sm:border-r border-[#E4E4D9]">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Jumlah Keluar
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        {{ number_format($barangKeluar->jumlah_keluar, 0, ',', '.') }}
                                        {{ $barangKeluar->item->satuan->nama_satuan ?? '' }}
                                    </p>
                                </div>

                                {{-- KONDISI --}}
                                <div class="p-4 border-b border-[#E4E4D9]">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Kondisi
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        {{ $barangKeluar->kondisi->nama_kondisi ?? '-' }}
                                    </p>
                                </div>

                                {{-- LOKASI AWAL --}}
                                <div class="p-4 sm:border-r border-[#E4E4D9]">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Lokasi Awal
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        {{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}
                                    </p>
                                </div>

                                {{-- LOKASI TUJUAN --}}
                                <div class="p-4">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Lokasi Tujuan
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        {{ $barangKeluar->lokasi_tujuan ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INFORMASI HARGA --}}
                    <div>
                        <h3 class="text-sm font-black text-[#0B4F35] mb-3">
                            Informasi Harga
                        </h3>
                        <div class="rounded-2xl border border-[#E4E4D9] overflow-hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-3">
                                {{-- HARGA RATA RATA --}}
                                <div class="p-4 sm:border-r border-b sm:border-b-0 border-[#E4E4D9]">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Harga Rata-Rata
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        Rp {{ number_format($hargaRata ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>

                                {{-- HARGA JUAL --}}
                                <div class="p-4 sm:border-r border-b sm:border-b-0 border-[#E4E4D9]">
                                    <p class="text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Harga Jual / Unit
                                    </p>
                                    <p class="mt-1 font-extrabold text-[#475569]">
                                        Rp {{ number_format($barangKeluar->harga_jual ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>

                                {{-- TOTAL --}}
                                <div class="p-4 bg-emerald-50">
                                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide">
                                        Total Nilai
                                    </p>
                                    <p class="mt-1 text-lg font-black text-[#0B4F35]">
                                        Rp {{ number_format($barangKeluar->total_harga_jual ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- INFORMASI TRANSAKSI --}}
                    <div>
                        <h3 class="text-sm font-black text-[#0B4F35] mb-3">
                            Informasi Transaksi
                        </h3>

                        <div class="rounded-2xl border border-[#E4E4D9] overflow-hidden">
                            <div class="divide-y divide-[#E4E4D9]">

                                {{-- TANGGAL --}}
                                <div class="flex flex-col sm:flex-row sm:items-center p-4 gap-1 sm:gap-4">
                                    <p class="sm:w-40 text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Tanggal Keluar
                                    </p>

                                    <p class="font-extrabold text-[#475569]">
                                        {{ $barangKeluar->tanggal_keluar ? \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d-m-Y') : '-' }}
                                    </p>
                                </div>

                                {{-- JENIS TRANSAKSI --}}
                                <div class="flex flex-col sm:flex-row sm:items-center p-4 gap-1 sm:gap-4">
                                    <p class="sm:w-40 text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Jenis Transaksi
                                    </p>

                                    <div>
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full
                        bg-sky-100 text-sky-700
                        text-xs font-extrabold">
                                            {{ $barangKeluar->jenis_transaksi ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- PENERIMA --}}
                                <div class="flex flex-col sm:flex-row sm:items-center p-4 gap-1 sm:gap-4">
                                    <p class="sm:w-40 text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Penerima
                                    </p>

                                    <p class="font-extrabold text-[#475569]">
                                        {{ $barangKeluar->penerima ?? '-' }}
                                    </p>
                                </div>

                                {{-- PETUGAS --}}
                                <div class="flex flex-col sm:flex-row sm:items-center p-4 gap-1 sm:gap-4">
                                    <p class="sm:w-40 text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Petugas
                                    </p>

                                    <p class="font-extrabold text-[#475569]">
                                        {{ $barangKeluar->user->username ?? '-' }}
                                    </p>
                                </div>

                                {{-- CATATAN --}}
                                <div class="flex flex-col sm:flex-row sm:items-start p-4 gap-1 sm:gap-4">
                                    <p class="sm:w-40 text-xs font-bold text-[#8FA882] uppercase tracking-wide">
                                        Catatan
                                    </p>

                                    <p class="font-semibold text-[#475569]">
                                        {{ $barangKeluar->catatan ?? '-' }}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div
                    class="px-5 py-5 md:px-7
                    bg-[#FAF9F6]
                    border-t border-[#E4E4D9]
                    flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-3">

                    <a href="{{ route('barang-keluar.index') }}"
                        class="inline-flex items-center justify-center gap-2
                        px-5 py-3 rounded-xl
                        bg-white border-2 border-[#8FA882]
                        text-sm font-extrabold text-[#0B4F35]
                        hover:bg-[#F0F5ED] hover:border-[#0B4F35]
                        transition shadow-sm">

                        <i class="fas fa-arrow-left text-[#0B4F35]"></i>
                        Kembali
                    </a>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('barang-keluar.cetak-detail', $barangKeluar->id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2
                            px-5 py-3 rounded-xl
                            bg-white border-2 border-blue-500
                            text-sm font-extrabold text-blue-600
                            shadow-md
                            hover:bg-blue-50 hover:shadow-lg
                            transition">
                            <i class="fas fa-file-pdf"></i>
                            Cetak PDF
                        </a>

                        <a href="{{ route('barang-keluar.cetak-ba', $barangKeluar->id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2
                            px-5 py-3 rounded-xl
                            bg-white border-2 border-[#0B4F35]
                            text-sm font-extrabold text-[#0B4F35]
                            shadow-md
                            hover:bg-[#F0F7F3]
                            hover:border-[#083d29]
                            hover:text-[#083d29]
                            hover:shadow-lg
                            transition">
                            <i class="fas fa-file-signature"></i>
                            Cetak Berita Acara
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
