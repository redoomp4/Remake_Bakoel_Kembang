@extends('layouts.bakoelkembang')

@section('content')
<div class="flex-grow flex flex-col lg:flex-row">
  <!-- Sidebar Navigation -->
  @include('partials.sidebar')

  <!-- Admin Main Content -->
  <main class="flex-grow p-6 md:p-8 overflow-y-auto w-full max-w-7xl space-y-8">
    
    <!-- Header Greeting -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-brand-accent/40 pb-6">
      <div>
        <h2 class="text-3xl font-black text-brand-emerald tracking-tight">DASHBOARD KEUANGAN & LACI KAS</h2>
        <p class="text-brand-slate font-medium text-md mt-1">Urusan uang kebun aman terkendali. Star utama laci kas harian.</p>
      </div>
      <div class="bg-white/80 backdrop-blur-sm px-5 py-3 rounded-2xl border border-brand-accent font-bold text-md shadow-sm flex items-center gap-2">
        <i class="far fa-calendar-alt text-brand-emerald"></i>
        <span>Hari ini:</span>
        <span class="font-extrabold text-brand-emerald">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
      </div>
    </div>

    <!-- A. HERO FINANCIAL CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <!-- Highlight Untung Bersih -->
      <div class="bg-gradient-to-tr from-brand-emerald via-[#0A452E] to-[#052C1E] p-8 rounded-3xl shadow-lg relative overflow-hidden border border-white/5 md:col-span-2">
        <div class="absolute right-4 bottom-4 text-white/5 pointer-events-none">
          <i class="fas fa-circle-dollar-to-slot text-[140px]"></i>
        </div>
        <div class="flex justify-between items-center flex-wrap gap-2">
          <span class="text-xs font-black uppercase tracking-widest text-[#E4E4D9] bg-white/10 border border-white/10 px-3.5 py-1.5 rounded-xl inline-block">
            🌟 PERGERAKAN HARI INI
          </span>
          <span class="text-[11px] font-bold text-white bg-brand-sage/40 border border-brand-sage/20 px-2.5 py-1 rounded-lg">
            Realtime Dashboard
          </span>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-6">
          <div>
            <span class="text-xs font-bold text-brand-sage uppercase">Barang Masuk</span>
            <h3 class="text-3xl md:text-4xl font-black text-brand-offwhite tracking-tight">{{ $masukHariIni }} <span class="text-sm font-normal">item</span></h3>
          </div>
          <div>
            <span class="text-xs font-bold text-rose-300 uppercase">Barang Keluar</span>
            <h3 class="text-3xl md:text-4xl font-black text-rose-200 tracking-tight">{{ $keluarHariIni }} <span class="text-sm font-normal">item</span></h3>
          </div>
        </div>
        <p class="text-xs font-semibold text-brand-sage mt-4 uppercase tracking-widest">
          Aktivitas fisik & finansial kebun terkontrol mulus.
        </p>
      </div>

      <!-- Uang Masuk Card -->
      <div class="bg-white border-2 border-brand-accent/60 p-6 rounded-3xl shadow-sm relative overflow-hidden">
        <span class="text-[10px] font-black uppercase tracking-widest text-brand-emerald bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-md">
          💰 BARANG MASUK HARI INI
        </span>
        <h3 class="text-4xl font-black text-brand-emerald mt-6 tracking-tight">{{ $masukHariIni }}</h3>
        <p class="text-xs font-medium text-brand-slate mt-2">
          Total frekuensi penerimaan barang dari supplier.
        </p>
      </div>

      <!-- Uang Keluar Card -->
      <div class="bg-white border-2 border-brand-accent/60 p-6 rounded-3xl shadow-sm relative overflow-hidden">
        <span class="text-[10px] font-black uppercase tracking-widest text-rose-600 bg-rose-50 border border-rose-100 px-3 py-1 rounded-md">
          📋 BARANG KELUAR HARI INI
        </span>
        <h3 class="text-4xl font-black text-rose-600 mt-6 tracking-tight">{{ $keluarHariIni }}</h3>
        <p class="text-xs font-medium text-brand-slate mt-2">
          Total frekuensi pengeluaran / penjualan barang.
        </p>
      </div>
    </div>

    <!-- B. WARNINGS & ALERTS SECTION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Peringatan Stok Minimum -->
      <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-black text-rose-600 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i> Barang Hampir Habis / Stok Minimum
          </h3>
          <span class="text-xs font-bold bg-rose-50 text-rose-600 px-3 py-1 rounded-full border border-rose-100">
            {{ count($stokMinimum) }} Warning
          </span>
        </div>
        <div class="divide-y divide-gray-100">
          @forelse($stokMinimum as $item)
            <div class="py-3 flex justify-between items-center text-sm font-semibold">
              <span class="text-gray-900 font-bold">{{ $item->nama_barang }}</span>
              <span class="px-3 py-1 bg-rose-600 text-white rounded-xl text-xs font-black">
                Stok: {{ $item->stok_akhir }}
              </span>
            </div>
          @empty
            <p class="text-xs text-brand-slate py-4 italic">Semua stok barang dalam batas aman.</p>
          @endforelse
        </div>
      </div>

      <!-- Peringatan Kadaluarsa Garansi -->
      <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-black text-amber-600 flex items-center gap-2">
            <i class="fas fa-clock"></i> Kadaluarsa Dalam 30 Hari
          </h3>
          <span class="text-xs font-bold bg-amber-50 text-amber-600 px-3 py-1 rounded-full border border-amber-100">
            {{ count($kadaluarsa) }} Item
          </span>
        </div>
        <div class="divide-y divide-gray-100">
          @forelse($kadaluarsa as $item)
            <div class="py-3 flex justify-between items-center text-sm font-semibold">
              <span class="text-gray-900 font-bold">{{ $item->nama_barang }}</span>
              <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                Kadaluarsa: {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->translatedFormat('d M Y') }}
              </span>
            </div>
          @empty
            <p class="text-xs text-brand-slate py-4 italic">Tidak ada barang kadaluarsa dalam 30 hari ke depan.</p>
          @endforelse
        </div>
      </div>
    </div>

    <!-- C. RINGKASAN TOP BARANG -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      
      <!-- Top Barang Masuk -->
      <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
        <h3 class="text-xl font-extrabold text-brand-emerald">5 Barang Paling Banyak Masuk</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-brand-accent/40 text-brand-slate text-xs font-black uppercase">
                <th class="py-3 px-3">Nama Barang</th>
                <th class="py-3 px-3">Frekuensi</th>
                <th class="py-3 px-3">Total Masuk</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm font-semibold">
              @forelse($topMasuk as $b)
                <tr>
                  <td class="py-3 px-3 font-bold text-gray-900">{{ $b->nama_barang }}</td>
                  <td class="py-3 px-3 text-brand-slate">{{ $b->frekuensi }}x</td>
                  <td class="py-3 px-3 font-extrabold text-brand-emerald">{{ $b->total }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="py-4 text-xs text-brand-slate italic">Belum ada data transaksi.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Barang Keluar -->
      <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
        <h3 class="text-xl font-extrabold text-rose-600">5 Barang Paling Banyak Keluar</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-brand-accent/40 text-brand-slate text-xs font-black uppercase">
                <th class="py-3 px-3">Nama Barang</th>
                <th class="py-3 px-3">Frekuensi</th>
                <th class="py-3 px-3">Total Keluar</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm font-semibold">
              @forelse($topKeluar as $b)
                <tr>
                  <td class="py-3 px-3 font-bold text-gray-900">{{ $b->nama_barang }}</td>
                  <td class="py-3 px-3 text-brand-slate">{{ $b->frekuensi }}x</td>
                  <td class="py-3 px-3 font-extrabold text-rose-600">{{ $b->total }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="py-4 text-xs text-brand-slate italic">Belum ada data transaksi.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </main>
</div>
@endsection
