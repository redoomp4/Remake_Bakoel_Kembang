@extends('layouts.bakoelkembang')

@section('title', 'Detail Bunga - QR Code')

@section('content')
<div class="flex-grow max-w-4xl w-full mx-auto p-6 md:p-8 space-y-8">

  <!-- Back Button -->
  <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-brand-accent rounded-xl text-sm font-bold text-brand-slate hover:bg-brand-offwhite transition-all">
    <i class="fas fa-arrow-left"></i> Kembali
  </a>

  <div class="bg-white rounded-3xl border border-brand-accent shadow-sm overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

      <!-- LEFT: Foto & QR Code -->
      <div class="bg-brand-offwhite p-8 flex flex-col items-center justify-center gap-6 border-r border-brand-accent">
        @php
          $foto = optional($barangMasuk->item)->foto;
        @endphp
        @if($foto && Storage::disk('public')->exists($foto))
          <div class="w-full max-w-[280px] rounded-2xl overflow-hidden border border-brand-accent shadow-sm">
            <img src="{{ Storage::url($foto) }}" alt="Foto Bunga" class="w-full h-auto object-cover">
          </div>
        @else
          <div class="w-full max-w-[280px] h-56 bg-white rounded-2xl border border-brand-accent flex items-center justify-center text-brand-slate">
            <div class="text-center">
              <i class="fas fa-seedling text-4xl text-brand-sage mb-2"></i>
              <p class="text-xs font-bold">Foto Belum Tersedia</p>
            </div>
          </div>
        @endif

        <!-- QR Code Display -->
        @if($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code))
          <div class="bg-white p-4 rounded-2xl border border-brand-accent shadow-sm">
            <img src="{{ Storage::url($barangMasuk->qr_code) }}" alt="QR Code" class="w-40 h-40 object-contain mx-auto">
          </div>
          <p class="text-[10px] font-bold text-brand-slate uppercase tracking-widest text-center">Scan QR untuk detail publik</p>
        @else
          <div class="bg-white p-6 rounded-2xl border border-brand-accent text-center">
            <i class="fas fa-qrcode text-4xl text-gray-300 mb-2"></i>
            <p class="text-xs font-bold text-brand-slate">QR Code Belum Di-generate</p>
          </div>
        @endif
      </div>

      <!-- RIGHT: Detail Informasi Bunga -->
      <div class="p-8 space-y-6">
        <div>
          <span class="text-[10px] font-black uppercase tracking-widest text-brand-emerald bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-md">
            🌸 DETAIL VARIETAS BUNGA
          </span>
          <h2 class="text-2xl font-black text-brand-emerald mt-3 tracking-tight">
            {{ optional($barangMasuk->item)->nama_barang ?? 'Tanaman Tidak Diketahui' }}
          </h2>
          <p class="text-xs font-mono font-bold text-brand-slate mt-1">
            Kode: <span class="bg-brand-offwhite px-2 py-0.5 rounded-md border border-brand-accent">{{ $barangMasuk->kode_barang }}</span>
          </p>
        </div>

        <!-- Info Cards Grid -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Harga -->
          <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-100 space-y-1">
            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600">💰 HARGA DASAR</span>
            <p class="text-xl font-black text-brand-emerald">
              Rp {{ number_format(optional($barangMasuk->item)->harga_dasar ?? 0, 0, ',', '.') }}
            </p>
          </div>

          <!-- Stok -->
          <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-100 space-y-1">
            <span class="text-[9px] font-black uppercase tracking-widest text-blue-600">📦 STOK TERSEDIA</span>
            <p class="text-xl font-black text-blue-700">
              {{ optional($barangMasuk->item)->total_stok ?? $barangMasuk->jumlah }} unit
            </p>
          </div>

          <!-- Umur Tanaman -->
          <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-100 space-y-1">
            <span class="text-[9px] font-black uppercase tracking-widest text-amber-600">🌱 UMUR TANAMAN</span>
            <p class="text-lg font-black text-amber-700">
              {{ $barangMasuk->umur_tanaman }}
            </p>
          </div>

          <!-- Kondisi -->
          <div class="bg-purple-50/70 p-4 rounded-2xl border border-purple-100 space-y-1">
            <span class="text-[9px] font-black uppercase tracking-widest text-purple-600">🔍 KONDISI</span>
            <p class="text-lg font-black text-purple-700">
              {{ optional($barangMasuk->kondisi)->nama_kondisi ?? '-' }}
            </p>
          </div>
        </div>

        <!-- Detail Table -->
        <div class="border-t border-brand-accent/40 pt-4 space-y-3">
          <h4 class="text-sm font-black text-brand-emerald uppercase tracking-wider">Rincian Lengkap</h4>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
              <span class="font-bold text-brand-slate">Kategori</span>
              <span class="font-extrabold text-gray-900">{{ data_get($barangMasuk, 'item.kategori.kategori', '-') }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
              <span class="font-bold text-brand-slate">Jumlah Masuk</span>
              <span class="font-extrabold text-gray-900">{{ $barangMasuk->jumlah }} unit</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
              <span class="font-bold text-brand-slate">Harga Satuan</span>
              <span class="font-extrabold text-gray-900">Rp {{ number_format($barangMasuk->harga_satuan ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
              <span class="font-bold text-brand-slate">Total Harga</span>
              <span class="font-extrabold text-brand-emerald">Rp {{ number_format($barangMasuk->total_harga ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
              <span class="font-bold text-brand-slate">Tanggal Masuk</span>
              <span class="font-extrabold text-gray-900">{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d F Y') }}</span>
            </div>
            @if($barangMasuk->tanggal_kadaluarsa)
            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
              <span class="font-bold text-brand-slate">Kadaluarsa</span>
              <span class="font-extrabold text-amber-600">{{ \Carbon\Carbon::parse($barangMasuk->tanggal_kadaluarsa)->translatedFormat('d F Y') }}</span>
            </div>
            @endif
            <div class="flex justify-between items-center py-1.5">
              <span class="font-bold text-brand-slate">Lokasi</span>
              <span class="font-extrabold text-gray-900">{{ optional($barangMasuk->lokasi)->nama_lokasi ?? '-' }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
