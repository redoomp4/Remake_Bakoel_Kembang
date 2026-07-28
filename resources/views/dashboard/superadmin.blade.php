@extends('layouts.bakoelkembang')

@section('content')
<div class="flex-grow flex flex-col lg:flex-row">
  @include('partials.sidebar')

  <main class="flex-grow p-6 md:p-8 overflow-y-auto w-full max-w-7xl space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-brand-accent/40 pb-6">
      <div>
        <h2 class="text-3xl font-black text-brand-emerald tracking-tight">DASHBOARD SUPERADMIN</h2>
        <p class="text-brand-slate font-medium text-md mt-1">Panel kontrol pengguna dan manajemen hak akses sistem.</p>
      </div>
      <div class="bg-white/80 backdrop-blur-sm px-5 py-3 rounded-2xl border border-brand-accent font-bold text-md shadow-sm flex items-center gap-2">
        <i class="far fa-user text-brand-emerald"></i>
        <span>Total Pengguna:</span>
        <span class="font-extrabold text-brand-emerald">{{ $totalUsers }}</span>
      </div>
    </div>

    <!-- Cards Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-gradient-to-tr from-brand-emerald via-[#0A452E] to-[#052C1E] p-6 rounded-3xl shadow-lg text-white">
        <span class="text-xs font-black uppercase tracking-widest text-[#E4E4D9]">👑 TOTAL PENGGUNA</span>
        <h3 class="text-4xl font-black text-brand-offwhite mt-4">{{ $totalUsers }}</h3>
        <p class="text-xs font-semibold text-brand-sage mt-2">Terdaftar di sistem Bakoel Kembang</p>
      </div>

      <div class="bg-white border-2 border-brand-accent/60 p-6 rounded-3xl shadow-sm">
        <span class="text-[10px] font-black uppercase tracking-widest text-brand-emerald bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-md">👥 USER PER ROLE</span>
        <div class="mt-4 space-y-2 text-sm font-bold">
          @foreach($usersPerRole as $role => $jumlah)
            <div class="flex justify-between items-center">
              <span class="capitalize text-brand-slate">{{ $role }}:</span>
              <span class="text-brand-emerald font-black">{{ $jumlah }} pengguna</span>
            </div>
          @endforeach
        </div>
      </div>

      <div class="bg-white border-2 border-brand-accent/60 p-6 rounded-3xl shadow-sm">
        <span class="text-[10px] font-black uppercase tracking-widest text-brand-slate bg-gray-50 border border-gray-100 px-3 py-1 rounded-md">📊 STATUS STATUS PER ROLE</span>
        <div class="mt-4 space-y-2 text-xs font-bold">
          @foreach($statusPerRole as $role => $status)
            <div class="border-b border-gray-100 pb-1">
              <span class="capitalize text-gray-900 font-extrabold block mb-1">{{ $role }}</span>
              <div class="flex gap-4">
                <span class="text-emerald-600">Aktif: {{ $status['aktif'] }}</span>
                <span class="text-rose-600">Nonaktif: {{ $status['nonaktif'] }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </main>
</div>
@endsection
