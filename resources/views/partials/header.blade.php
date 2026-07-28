<header class="bg-white border-b border-brand-accent px-6 py-4 sticky top-0 z-40 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-4">
  <div class="flex items-center gap-4">
    <a href="{{ route('welcome') }}" class="flex items-center gap-4 group">
      <div class="w-14 h-14 bg-brand-emerald rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-emerald/10">
        <i class="fas fa-seedling text-3xl"></i>
      </div>
      <div>
        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-brand-emerald leading-none">
          BAKOEL<span class="text-brand-sage font-semibold">KEMBANG</span>
        </h1>
        <p class="text-xs font-bold text-brand-slate uppercase tracking-widest mt-1">
          Premium Orchids Nursery & Botanical Fintech
        </p>
      </div>
    </a>
  </div>

  <!-- Toggle View Switcher -->
  <div class="flex items-center gap-3">
    <div class="flex bg-gray-100 p-2 rounded-2xl gap-2 border border-gray-200">
      <a href="{{ route('welcome') }}" class="px-6 py-3 rounded-xl text-md font-bold transition-all {{ request()->routeIs('welcome') ? 'bg-white text-brand-emerald shadow-md' : 'text-brand-slate hover:text-brand-emerald' }}">
        <i class="fas fa-shopping-bag mr-2 text-brand-sage"></i>KATALOG PUBLIK
      </a>
      @auth
      <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl text-md font-bold transition-all {{ request()->routeIs('dashboard*') ? 'bg-white text-brand-emerald shadow-md' : 'text-brand-slate hover:text-brand-emerald' }}">
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
</header>
