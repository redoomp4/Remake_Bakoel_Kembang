<aside class="w-full lg:w-80 bg-white border-r border-brand-accent p-6 flex flex-col gap-3 shrink-0">
  <div class="mb-4 px-2 py-3 border-b border-brand-accent/50">
    <p class="text-xs font-black text-brand-slate uppercase tracking-widest">Akses Admin Kebun</p>
    <p class="text-[13px] text-brand-emerald font-bold mt-1">
      <i class="fas fa-shield-alt mr-1 text-brand-sage"></i> Panel Laci Kas V3
    </p>
  </div>

  <nav class="flex flex-col gap-2">
    <!-- Dashboard Keuangan -->
    <a href="{{ route('dashboard.gudang') }}" class="w-full flex items-center justify-between px-5 py-4 rounded-2xl font-black text-lg transition-all {{ request()->routeIs('dashboard.gudang') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate font-extrabold hover:bg-brand-offwhite' }}">
      <span class="flex items-center gap-3"><i class="fas fa-wallet w-6"></i> Dashboard Keuangan</span>
    </a>

    <!-- Barang Keluar (OUT) -->
    <a href="{{ route('barang-keluar.index') }}" class="w-full flex items-center justify-between px-5 py-4 rounded-2xl font-extrabold text-lg transition-all {{ request()->routeIs('barang-keluar*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
      <span class="flex items-center gap-3"><i class="fas fa-arrow-circle-up w-6 text-rose-500"></i> Barang Keluar</span>
      <span class="text-xs bg-rose-50 text-rose-600 px-2.5 py-1 rounded-full font-black uppercase border border-rose-100">OUT</span>
    </a>

    <!-- Barang Masuk (IN) -->
    <a href="{{ route('barang-masuk.index') }}" class="w-full flex items-center justify-between px-5 py-4 rounded-2xl font-extrabold text-lg transition-all {{ request()->routeIs('barang-masuk*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
      <span class="flex items-center gap-3"><i class="fas fa-arrow-circle-down w-6 text-emerald-600"></i> Barang Masuk</span>
      <span class="text-xs bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full font-black uppercase border border-emerald-100">IN</span>
    </a>

    <!-- Tambah Item Baru -->
    <a href="{{ route('item.index') }}" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-extrabold text-lg transition-all {{ request()->routeIs('item*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
      <i class="fas fa-plus-circle w-6 text-brand-sage"></i> Tambah Item Baru
    </a>

    <!-- Master Database -->
    <a href="{{ route('pemasok.index') }}" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-extrabold text-lg transition-all {{ request()->routeIs('pemasok*') || request()->routeIs('kategori*') || request()->routeIs('satuan*') || request()->routeIs('lokasi*') || request()->routeIs('kondisi*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
      <i class="fas fa-database w-6 text-brand-sage"></i> Master Database
    </a>

    <!-- Laporan Transaksi -->
    <a href="{{ route('laporan') }}" class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl font-extrabold text-lg transition-all {{ request()->routeIs('laporan*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
      <i class="fas fa-file-invoice-dollar w-6 text-brand-sage"></i> Laporan Transaksi
    </a>
  </nav>
</aside>
