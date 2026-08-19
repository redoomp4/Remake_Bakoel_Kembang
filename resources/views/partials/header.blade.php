<header class="bg-white border-b border-brand-accent px-4 md:px-6 py-3 md:py-4 sticky top-0 z-40 shadow-sm">
  <div class="flex items-center justify-between gap-4">
    
    <!-- Brand Logo & Title -->
    <a href="{{ route('welcome') }}" class="flex items-center gap-3 md:gap-4 group shrink-0">
      <div class="w-10 h-10 md:w-14 md:h-14 bg-brand-emerald rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-emerald/10">
        <i class="fas fa-seedling text-xl md:text-3xl"></i>
      </div>
      <div>
        <h1 class="text-lg md:text-2xl font-black tracking-tight text-brand-emerald leading-none">
          BAKOEL<span class="text-brand-sage font-semibold">KEMBANG</span>
        </h1>
        <p class="text-[10px] md:text-xs font-bold text-brand-slate uppercase tracking-widest mt-0.5 md:mt-1 hidden sm:block">
          Premium Orchids Nursery & Botanical Fintech
        </p>
      </div>
    </a>

    <!-- Desktop Navigation Menu -->
    <div class="hidden lg:flex items-center gap-3">
      <div class="flex bg-gray-100 p-1.5 rounded-2xl gap-2 border border-gray-200">
        <a href="{{ route('welcome') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('welcome') ? 'bg-white text-brand-emerald shadow-md' : 'text-brand-slate hover:text-brand-emerald' }}">
          <i class="fas fa-shopping-bag mr-2 text-brand-sage"></i>KATALOG PUBLIK
        </a>
        @auth
        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('dashboard*') ? 'bg-white text-brand-emerald shadow-md' : 'text-brand-slate hover:text-brand-emerald' }}">
          <i class="fas fa-chart-line mr-2 text-brand-sage"></i>DASHBOARD ADMIN
        </a>
        @endauth
      </div>

      @auth
        <div class="flex items-center gap-2 border-l border-brand-accent pl-3">
          <span class="text-xs font-bold text-brand-emerald bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg">
            👤 {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})
          </span>
          <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Logout">
              <i class="fas fa-sign-out-alt text-lg"></i>
            </button>
          </form>
        </div>
      @else
        <a href="{{ route('login') }}" class="px-5 py-2.5 bg-brand-emerald text-white rounded-xl text-sm font-extrabold shadow-md hover:bg-[#073A27] transition-all">
          <i class="fas fa-lock mr-2"></i>MASUK / LOGIN
        </a>
      @endauth
    </div>

    <!-- Mobile Hamburger Burger Button -->
    <div class="flex lg:hidden items-center gap-2">
      @auth
        <span class="text-[11px] font-bold text-brand-emerald bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-lg max-w-[120px] truncate">
          {{ Auth::user()->name }}
        </span>
      @endauth

      <button id="mobileBurgerBtn" onclick="toggleMobileNav()" type="button" class="w-10 h-10 bg-brand-emerald/10 hover:bg-brand-emerald text-brand-emerald hover:text-white rounded-xl flex items-center justify-center text-xl transition-all focus:outline-none" aria-label="Toggle Navigation">
        <i class="fas fa-bars" id="burgerIcon"></i>
      </button>
    </div>

  </div>

  <!-- Mobile Dropdown Menu (Navbar Burger) -->
  <div id="mobileNavMenu" class="hidden lg:hidden mt-3 pt-3 border-t border-brand-accent space-y-3 animate-fade-in">
    <div class="flex flex-col gap-2">
      <a href="{{ route('welcome') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm {{ request()->routeIs('welcome') ? 'bg-brand-emerald text-white' : 'bg-gray-100 text-brand-slate' }}">
        <i class="fas fa-shopping-bag text-brand-sage"></i> Katalog Publik
      </a>
      
      @auth
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm {{ request()->routeIs('dashboard*') ? 'bg-brand-emerald text-white' : 'bg-gray-100 text-brand-slate' }}">
          <i class="fas fa-chart-line text-brand-sage"></i> Dashboard Admin
        </a>

        @php
          $uRole = strtolower(Auth::user()->role ?? 'viewer');
        @endphp

        @if($uRole !== 'viewer')
          <a href="{{ route('barang-keluar.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-sm bg-rose-50 text-rose-700">
            <i class="fas fa-arrow-circle-up"></i> Barang Keluar (OUT)
          </a>
          <a href="{{ route('barang-masuk.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-sm bg-emerald-50 text-emerald-700">
            <i class="fas fa-arrow-circle-down"></i> Barang Masuk (IN)
          </a>
          <a href="{{ route('item.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-sm bg-gray-100 text-brand-slate">
            <i class="fas fa-plus-circle text-brand-sage"></i> Tambah Item Baru
          </a>
          <a href="{{ route('pemasok.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-sm bg-gray-100 text-brand-slate">
            <i class="fas fa-database text-brand-sage"></i> Master Database
          </a>
        @endif

        <a href="{{ route('laporan') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-sm bg-gray-100 text-brand-slate">
          <i class="fas fa-file-invoice-dollar text-brand-sage"></i> Laporan Transaksi
        </a>

        <div class="pt-2 border-t border-gray-200 flex items-center justify-between px-2">
          <span class="text-xs font-bold text-brand-slate">
            Role: <strong class="text-brand-emerald uppercase">{{ $uRole }}</strong>
          </span>
          <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs flex items-center gap-1.5">
              <i class="fas fa-sign-out-alt"></i> Logout
            </button>
          </form>
        </div>
      @else
        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-brand-emerald text-white rounded-xl font-bold text-sm shadow-md">
          <i class="fas fa-lock"></i> Masuk / Login
        </a>
      @endauth
    </div>
  </div>
</header>

<script>
  function toggleMobileNav() {
    const menu = document.getElementById('mobileNavMenu');
    const icon = document.getElementById('burgerIcon');
    if (menu.classList.contains('hidden')) {
      menu.classList.remove('hidden');
      icon.classList.remove('fa-bars');
      icon.classList.add('fa-times');
    } else {
      menu.classList.add('hidden');
      icon.classList.remove('fa-times');
      icon.classList.add('fa-bars');
    }
  }
</script>
