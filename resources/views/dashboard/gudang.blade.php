@extends('layouts.bakoelkembang')

@section('content')
<div class="flex-grow flex flex-col lg:flex-row">
  <!-- Admin Sidebar Navigation -->
  @include('partials.sidebar')

  <!-- Admin Main Dynamic Section -->
  <main class="flex-grow p-6 md:p-8 overflow-y-auto w-full max-w-7xl">

    <!-- TAB 1: DASHBOARD KEUANGAN & LACI KAS -->
    <section class="space-y-8">
      <!-- Dashboard Greeting -->
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
        <!-- Main Highlight: Pergerakan Hari Ini -->
        <div class="bg-gradient-to-tr from-brand-emerald via-[#0A452E] to-[#052C1E] p-8 rounded-3xl shadow-lg relative overflow-hidden border border-white/5 md:col-span-2">
          <div class="absolute right-4 bottom-4 text-white/5 pointer-events-none">
            <i class="fas fa-circle-dollar-to-slot text-[140px]"></i>
          </div>
          <div class="flex justify-between items-center flex-wrap gap-2">
            <span class="text-xs font-black uppercase tracking-widest text-[#E4E4D9] bg-white/10 border border-white/10 px-3.5 py-1.5 rounded-xl inline-block">
              🌟 UNTUNG BERSIH DIBAWA PULANG
            </span>
            <span class="text-[11px] font-bold text-white bg-brand-sage/40 border border-brand-sage/20 px-2.5 py-1 rounded-lg">
              Masuk: {{ $masukHariIni }} • Keluar: {{ $keluarHariIni }}
            </span>
          </div>
          <h3 class="text-4xl md:text-5xl font-black text-brand-offwhite mt-6 tracking-tight">
            {{ $masukHariIni + $keluarHariIni }} <span class="text-lg font-semibold">transaksi</span>
          </h3>
          <p class="text-xs font-semibold text-brand-sage mt-3 uppercase tracking-widest">
            Hasil bersih murni siap kantong kebun!
          </p>
        </div>

        <!-- Card 2: Barang Masuk -->
        <div class="bg-white border-2 border-brand-accent/60 p-6 rounded-3xl shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
          <div class="absolute right-4 bottom-4 text-emerald-50/40 pointer-events-none">
            <i class="fas fa-wallet text-80px text-brand-emerald opacity-10"></i>
          </div>
          <span class="text-[10px] font-black uppercase tracking-widest text-brand-emerald bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-md">
            💰 UANG MASUK (OMZET)
          </span>
          <h3 class="text-3xl font-black text-brand-emerald mt-6 tracking-tight">{{ $masukHariIni }}</h3>
          <p class="text-xs font-medium text-brand-slate mt-2">
            Jumlah bruto seluruh penerimaan barang hari ini.
          </p>
        </div>

        <!-- Card 3: Barang Keluar -->
        <div class="bg-white border-2 border-brand-accent/60 p-6 rounded-3xl shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
          <div class="absolute right-4 bottom-4 text-slate-50/40 pointer-events-none">
            <i class="fas fa-receipt text-80px text-brand-slate opacity-10"></i>
          </div>
          <span class="text-[10px] font-black uppercase tracking-widest text-brand-slate bg-gray-50 border border-gray-100 px-3 py-1 rounded-md">
            📋 UANG KELUAR (MODAL BELI)
          </span>
          <h3 class="text-3xl font-black text-brand-slate mt-6 tracking-tight">{{ $keluarHariIni }}</h3>
          <p class="text-xs font-medium text-brand-slate mt-2">
            Pengeluaran barang & penjualan hari ini.
          </p>
        </div>
      </div>

      <!-- B. STATISTIK ARUS KAS MINIMALIS -->
      @php
        $totalMasuk = $masukHariIni;
        $totalKeluar = $keluarHariIni;
        $maxVal = max($totalMasuk, $totalKeluar, 1);
        $inPct = round(($totalMasuk / $maxVal) * 100);
        $outPct = round(($totalKeluar / $maxVal) * 100);
      @endphp
      <div class="bg-white rounded-3xl border border-brand-accent p-6 md:p-8 shadow-sm space-y-6">
        <div>
          <h3 class="text-xl font-extrabold text-brand-emerald tracking-tight">Visualisasi Perbandingan Kas</h3>
          <p class="text-brand-slate text-xs font-medium">Bandingan kas masuk (penerimaan) vs kas keluar (pengeluaran) secara visual.</p>
        </div>

        <div class="space-y-4">
          <!-- Progress Bar Uang Masuk -->
          <div class="space-y-1.5">
            <div class="flex justify-between items-center text-sm font-bold">
              <span class="text-brand-emerald font-black"><i class="fas fa-arrow-trend-up"></i> UANG MASUK (OMZET)</span>
              <span class="font-mono">{{ $masukHariIni }} item</span>
            </div>
            <div class="w-full bg-gray-100 h-5 rounded-full overflow-hidden">
              <div class="bg-brand-emerald h-full transition-all duration-500 rounded-full" style="width: {{ $inPct }}%"></div>
            </div>
          </div>

          <!-- Progress Bar Uang Keluar -->
          <div class="space-y-1.5">
            <div class="flex justify-between items-center text-sm font-bold">
              <span class="text-brand-slate font-black"><i class="fas fa-arrow-trend-down"></i> UANG KELUAR (MODAL)</span>
              <span class="font-mono">{{ $keluarHariIni }} item</span>
            </div>
            <div class="w-full bg-gray-100 h-5 rounded-full overflow-hidden">
              <div class="bg-brand-slate h-full transition-all duration-500 rounded-full" style="width: {{ $outPct }}%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- C. BUKU KAS TRANSAKSI CEPAT — Top Barang -->
      <div class="bg-white rounded-3xl border border-brand-accent p-6 md:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
          <div>
            <h3 class="text-xl font-extrabold text-brand-emerald tracking-tight">Buku Kas Laci Transaksi</h3>
            <p class="text-brand-slate text-xs font-medium">Daftar arus barang masuk dan keluar kebun.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Top Barang Masuk -->
          <div class="space-y-4">
            <h4 class="text-lg font-black text-brand-emerald">5 Barang Paling Banyak Masuk</h4>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="border-b-2 border-brand-accent/40 text-brand-slate text-xs font-black uppercase tracking-widest">
                    <th class="py-4 px-4">Nama Item</th>
                    <th class="py-4 px-4">Jumlah</th>
                    <th class="py-4 px-4">Frekuensi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-md">
                  @forelse($topMasuk as $b)
                    <tr class="hover:bg-gray-50/50">
                      <td class="py-4 px-4 font-black text-gray-900">{{ $b->nama_barang }}</td>
                      <td class="py-4 px-4 font-extrabold text-brand-emerald">{{ $b->total }}</td>
                      <td class="py-4 px-4 text-xs font-bold text-gray-500">{{ $b->frekuensi }}x</td>
                    </tr>
                  @empty
                    <tr><td colspan="3" class="py-8 text-center text-sm font-semibold text-gray-400">Belum ada data transaksi masuk.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <!-- Top Barang Keluar -->
          <div class="space-y-4">
            <h4 class="text-lg font-black text-rose-600">5 Barang Paling Banyak Keluar</h4>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="border-b-2 border-brand-accent/40 text-brand-slate text-xs font-black uppercase tracking-widest">
                    <th class="py-4 px-4">Nama Item</th>
                    <th class="py-4 px-4">Jumlah</th>
                    <th class="py-4 px-4">Frekuensi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-md">
                  @forelse($topKeluar as $b)
                    <tr class="hover:bg-gray-50/50">
                      <td class="py-4 px-4 font-black text-gray-900">{{ $b->nama_barang }}</td>
                      <td class="py-4 px-4 font-extrabold text-rose-600">{{ $b->total }}</td>
                      <td class="py-4 px-4 text-xs font-bold text-gray-500">{{ $b->frekuensi }}x</td>
                    </tr>
                  @empty
                    <tr><td colspan="3" class="py-8 text-center text-sm font-semibold text-gray-400">Belum ada data transaksi keluar.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- D. OPERASIONAL BOTANIK -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left: Stok Terendah & Idle Stock -->
        <div class="lg:col-span-2 space-y-6">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-xl font-extrabold text-brand-emerald tracking-tight">Panel Transaksi Sat-Set</h3>
              <p class="text-brand-slate text-xs font-medium">Laci operasional kebun untuk penyesuaian cepat.</p>
            </div>
          </div>

          <!-- 5 Stok Terendah -->
          <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
            <h4 class="text-lg font-black text-brand-emerald">5 Stok Barang Terendah</h4>
            <div class="grid grid-cols-1 gap-3">
              @forelse($stokTerendah as $item)
                @php
                  $warningStyle = $item->stok_akhir <= 2
                    ? 'bg-rose-50 text-rose-600 border-rose-100'
                    : 'bg-emerald-50 text-emerald-600 border-emerald-100';
                @endphp
                <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-xl border border-gray-150 font-bold text-xs">
                  <span class="text-brand-slate font-black">{{ $item->nama_barang }}</span>
                  <span class="{{ $warningStyle }} px-2.5 py-0.5 rounded-md font-black border">Stok: {{ $item->stok_akhir }}</span>
                </div>
              @empty
                <p class="text-gray-400 font-semibold text-center text-xs py-4">Belum ada data stok.</p>
              @endforelse
            </div>
          </div>

          <!-- Idle Stock -->
          @if(count($idleStock) > 0)
          <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
            <h4 class="text-lg font-black text-amber-600">Barang Mengendap > 30 Hari</h4>
            <div class="grid grid-cols-1 gap-3">
              @foreach($idleStock as $item)
                <div class="flex items-center justify-between p-2.5 bg-amber-50/50 rounded-xl border border-amber-100 font-bold text-xs">
                  <div>
                    <span class="block text-gray-900 font-extrabold">{{ $item->nama_barang }}</span>
                    <span class="text-[10px] text-brand-slate uppercase tracking-wider block mt-0.5">{{ $item->nama_lokasi }}</span>
                  </div>
                  <span class="bg-amber-600 text-white font-black px-3 py-1 rounded-lg text-xs">{{ $item->hari_idle }} hari</span>
                </div>
              @endforeach
            </div>
          </div>
          @endif
        </div>

        <!-- Right: Peringatan Stok & Kadaluarsa -->
        <div class="space-y-6">

          <!-- Peringatan Stok Tipis -->
          <div class="bg-white p-6 rounded-3xl border border-brand-accent shadow-sm space-y-4">
            <h4 class="text-lg font-black text-rose-600">Peringatan Stok Tipis</h4>
            <div class="space-y-2 text-md">
              @forelse($stokMinimum as $item)
                <div class="flex justify-between items-center bg-rose-50/70 p-3.5 rounded-xl border border-rose-100 font-bold text-xs sm:text-sm">
                  <div>
                    <span class="block text-gray-900 font-extrabold">{{ $item->nama_barang }}</span>
                    <span class="text-[10px] text-brand-slate uppercase tracking-wider block mt-0.5">Stok min: {{ $item->stok_minimum }}</span>
                  </div>
                  <span class="bg-rose-600 text-white font-black px-3 py-1 rounded-lg text-xs">Sisa: {{ $item->stok_akhir }}</span>
                </div>
              @empty
                <p class="text-gray-400 font-semibold text-center text-xs py-4">Aman! Seluruh tanaman mencukupi ambang batas.</p>
              @endforelse
            </div>
          </div>

          <!-- Kadaluarsa Garansi -->
          <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
            <h4 class="text-lg font-black text-amber-600">Kadaluarsa Dalam 30 Hari</h4>
            <div class="space-y-2 text-md">
              @forelse($kadaluarsa as $item)
                <div class="flex justify-between items-center bg-amber-50/70 p-3.5 rounded-xl border border-amber-100 font-bold text-xs sm:text-sm">
                  <span class="text-gray-900 font-extrabold">{{ $item->nama_barang }}</span>
                  <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                    {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->translatedFormat('d M Y') }}
                  </span>
                </div>
              @empty
                <p class="text-gray-400 font-semibold text-center text-xs py-4">Tidak ada barang kadaluarsa dalam 30 hari ke depan.</p>
              @endforelse
            </div>
          </div>

          <!-- Total Aset Summary -->
          <div class="bg-white rounded-3xl border border-brand-accent p-6 shadow-sm space-y-4">
            <h4 class="text-lg font-black text-brand-emerald">Total Nilai Investasi Aset</h4>
            <div class="bg-brand-offwhite p-4 rounded-2xl border border-brand-accent font-black text-md flex justify-between items-center">
              <span class="text-brand-slate text-sm font-semibold">Total Item Terdaftar</span>
              <span class="text-brand-emerald font-black text-lg">{{ count($stokTerendah) > 0 ? count($stokTerendah) . '+ item' : '0 item' }}</span>
            </div>
          </div>

        </div>
      </div>

      <!-- F. EXPORT LAPORAN KEUANGAN EXCEL -->
      <div class="bg-gradient-to-r from-brand-emerald via-[#0A452E] to-[#052C1E] rounded-3xl p-6 md:p-8 shadow-lg border border-white/5 space-y-5">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div>
            <span class="text-[10px] font-black uppercase tracking-widest text-white/80 bg-white/10 border border-white/10 px-3 py-1.5 rounded-xl inline-block mb-3">
              📊 LAPORAN KEUANGAN (LABA RUGI)
            </span>
            <h3 class="text-2xl font-black text-white tracking-tight">Unduh Laporan Keuangan Excel</h3>
            <p class="text-xs font-semibold text-brand-sage mt-1">Rincian lengkap omzet, modal, laba bersih, & nilai aset inventaris kebun Anda.</p>
          </div>
        </div>

        <form action="{{ route('export.keuangan.excel') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
          <div class="space-y-1.5 flex-1">
            <label class="text-[10px] font-black uppercase tracking-widest text-white/70 block">Tanggal Mulai</label>
            <input type="date" name="start_date" class="w-full px-4 py-3 rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-sage placeholder-white/50">
          </div>
          <div class="space-y-1.5 flex-1">
            <label class="text-[10px] font-black uppercase tracking-widest text-white/70 block">Tanggal Akhir</label>
            <input type="date" name="end_date" class="w-full px-4 py-3 rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm text-white text-sm font-bold focus:outline-none focus:ring-2 focus:ring-brand-sage placeholder-white/50">
          </div>
          <button type="submit" class="bg-white text-brand-emerald font-black text-sm px-8 py-3.5 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
            <i class="fas fa-file-excel text-lg"></i> UNDUH EXCEL KEUANGAN
          </button>
        </form>

        <p class="text-[10px] font-medium text-white/40">
          Kosongkan tanggal untuk mengunduh semua data keuangan sejak awal.
        </p>
      </div>
    </section>

  </main>
</div>
@endsection
