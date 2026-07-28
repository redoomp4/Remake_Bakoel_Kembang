@extends('layouts.bakoelkembang')

@section('content')
<div class="flex-grow flex flex-col lg:flex-row">
  @include('partials.sidebar')
  <main class="flex-grow p-6 md:p-8 overflow-y-auto w-full max-w-7xl space-y-8">
    <div class="bg-white p-8 rounded-3xl border border-brand-accent shadow-sm space-y-4 text-center">
      <div class="w-16 h-16 bg-emerald-50 text-brand-emerald rounded-full flex items-center justify-center text-3xl mx-auto mb-2">
        <i class="fas fa-user-shield"></i>
      </div>
      <h2 class="text-3xl font-black text-brand-emerald">Selamat Datang di BakoelKembang V3</h2>
      <p class="text-brand-slate font-medium text-md max-w-xl mx-auto">
        Anda telah berhasil masuk sebagai <strong>{{ Auth::user()->name }}</strong> ({{ strtoupper(Auth::user()->role) }}). Silakan pilih menu di sidebar untuk mengelola transaksi kas kebun dan inventaris.
      </p>
      <div class="pt-4 flex justify-center gap-4">
        <a href="{{ route('dashboard.gudang') }}" class="px-6 py-3 bg-brand-emerald text-white rounded-xl font-bold text-sm shadow-md">
          <i class="fas fa-wallet mr-2"></i> Laci Kas Kebun
        </a>
        <a href="{{ route('barang-masuk.index') }}" class="px-6 py-3 bg-brand-offwhite text-brand-emerald border border-brand-accent rounded-xl font-bold text-sm">
          <i class="fas fa-box-open mr-2"></i> Kelola Inventaris
        </a>
      </div>
    </div>
  </main>
</div>
@endsection
