<aside class="w-full lg:w-72 bg-white border-r border-brand-accent p-4 md:p-6 shrink-0" aria-label="Navigasi admin">

    <nav class="flex flex-col gap-2">
        <!-- Dashboard -->
        <a href="{{ route('dashboard.gudang') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-extrabold {{ request()->routeIs('dashboard.gudang') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
            <i class="fas fa-wallet w-5"></i>Dashboard
        </a>

        <!-- Barang Keluar -->
        <a href="{{ route('barang-keluar.index') }}"
            class="flex items-center justify-between px-4 py-3.5 rounded-xl font-extrabold {{ request()->routeIs('barang-keluar*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
            <span><i class="fas fa-arrow-circle-up w-5 mr-2 text-rose-500"></i>Barang Keluar</span>
            <span class="text-[10px] bg-rose-50 text-rose-600 px-2 py-1 rounded-full font-black">OUT</span>
        </a>

        <!-- Barang Masuk -->
        <a href="{{ route('barang-masuk.index') }}"
            class="flex items-center justify-between px-4 py-3.5 rounded-xl font-extrabold {{ request()->routeIs('barang-masuk*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
            <span><i class="fas fa-arrow-circle-down w-5 mr-2 text-emerald-600"></i>Barang Masuk</span>
            <span class="text-[10px] bg-emerald-50 text-emerald-600 px-2 py-1 rounded-full font-black">IN</span>
        </a>

        <!-- Item & Persediaan -->
        <a href="{{ route('item.index') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-extrabold {{ request()->routeIs('item*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
            <i class="fas fa-boxes w-5 text-brand-sage"></i>Item & Persediaan
        </a>

        <!-- ACCORDION / DROPDOWN MASTER DATABASE -->
        @php
            $isMasterActive =
                request()->routeIs('kategori*') ||
                request()->routeIs('satuan*') ||
                request()->routeIs('pemasok*') ||
                request()->routeIs('lokasi*') ||
                request()->routeIs('kondisi*');
        @endphp

        <details class="group rounded-xl transition-all duration-200" {{ $isMasterActive ? 'open' : '' }}>
            <summary
                class="flex items-center justify-between px-4 py-3.5 rounded-xl font-extrabold cursor-pointer list-none select-none {{ $isMasterActive ? 'bg-brand-emerald/10 text-brand-emerald border border-brand-emerald/20' : 'text-brand-slate hover:bg-brand-offwhite' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-database w-5 text-brand-sage"></i>
                    <span>Master Database</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-open:rotate-180"></i>
            </summary>

            <!-- Sub-Menu Master Items -->
            <div class="flex flex-col gap-1 pl-6 pr-2 pt-2 pb-1 mt-1 border-l-2 border-brand-accent ml-6">
                <!-- 1. Kategori -->
                <a href="{{ route('kategori.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('kategori*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-tags w-4 text-center"></i>Kategori Bunga
                </a>

                <!-- 2. Satuan -->
                <a href="{{ route('satuan.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('satuan*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-ruler-combined w-4 text-center"></i>Satuan Ukur
                </a>

                <!-- 3. Pemasok -->
                <a href="{{ route('pemasok.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('pemasok*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-truck-loading w-4 text-center"></i>Pemasok / Suplier
                </a>

                <!-- 4. Lokasi -->
                <a href="{{ route('lokasi.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('lokasi*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-map-marker-alt w-4 text-center"></i>Lokasi Rak
                </a>

                <!-- 5. Kondisi -->
                <a href="{{ route('kondisi.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('kondisi*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-clipboard-check w-4 text-center"></i>Kondisi Barang
                </a>
            </div>
        </details>

        <!-- ACCORDION / DROPDOWN LAPORAN -->
        @php
            $isLaporanActive =
                request()->routeIs('laporan*') ||
                request()->routeIs('laporan.arus*') ||
                request()->routeIs('aset*') ||
                request()->routeIs('omzet*');
        @endphp

        <details class="group rounded-xl transition-all duration-200" {{ $isLaporanActive ? 'open' : '' }}>
            <summary
                class="flex items-center justify-between px-4 py-3.5 rounded-xl font-extrabold cursor-pointer list-none select-none {{ $isLaporanActive ? 'bg-brand-emerald/10 text-brand-emerald border border-brand-emerald/20' : 'text-brand-slate hover:bg-brand-offwhite' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-invoice-dollar w-5 text-brand-sage"></i>
                    <span>Laporan</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200 group-open:rotate-180"></i>
            </summary>

            <!-- Sub-Menu Laporan -->
            <div class="flex flex-col gap-1 pl-6 pr-2 pt-2 pb-1 mt-1 border-l-2 border-brand-accent ml-6">
                <!-- 1. Laporan Stok -->
                <a href="{{ route('laporan') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('laporan') && !request()->routeIs('laporan.arus*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-boxes w-4 text-center"></i>Laporan Stok
                </a>

                <!-- 2. Arus Barang -->
                <a href="{{ route('laporan.arus') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('laporan.arus*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-exchange-alt w-4 text-center"></i>Arus Barang
                </a>

                <!-- 3. Laporan Aset -->
                <a href="{{ route('aset.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('aset*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-coins w-4 text-center"></i>Laporan Aset
                </a>

                <!-- 4. Omzet -->
                <a href="{{ route('omzet.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('omzet*') ? 'bg-brand-emerald text-white shadow-sm' : 'text-brand-slate hover:bg-brand-offwhite hover:text-brand-emerald' }}">
                    <i class="fas fa-chart-line w-4 text-center"></i>Omzet
                </a>
            </div>
        </details>

        @if(strtolower(Auth::user()->role ?? '') === 'admin')
            <!-- Kelola User (Admin Only) -->
            <a href="{{ route('user.index') }}"
                class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-extrabold {{ request()->routeIs('user*') ? 'bg-brand-emerald text-white shadow-md' : 'text-brand-slate hover:bg-brand-offwhite' }}">
                <i class="fas fa-users-cog w-5 text-brand-sage"></i>Kelola User
            </a>
        @endif
    </nav>
</aside>
