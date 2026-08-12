<header class="bg-white border-b border-brand-accent px-4 md:px-8 py-4 sticky top-0 z-40 shadow-sm">
  <div class="max-w-[1500px] mx-auto flex flex-wrap items-center justify-between gap-4">
    <a href="{{ route('welcome') }}" class="flex items-center gap-3 group" aria-label="BakoelKembang beranda">
      <span class="w-12 h-12 md:w-14 md:h-14 bg-brand-emerald rounded-2xl flex items-center justify-center text-white shadow-lg"><i class="fas fa-seedling text-2xl md:text-3xl"></i></span>
      <span><span class="block text-xl md:text-2xl font-black tracking-tight text-brand-emerald leading-none">BAKOEL<span class="text-brand-sage font-semibold">KEMBANG</span></span><span class="block text-[10px] font-bold text-brand-slate uppercase tracking-[.14em] mt-1">Premium Orchids Nursery</span></span>
    </a>
    <div class="flex items-center gap-2 md:gap-3">
      <nav class="hidden md:flex items-center gap-1 bg-gray-100 p-1.5 rounded-2xl border border-gray-200" aria-label="Navigasi utama">
        <a href="{{ route('welcome') }}" class="px-4 py-2.5 rounded-xl text-sm font-extrabold {{ request()->routeIs('welcome') ? 'bg-white text-brand-emerald shadow-sm' : 'text-brand-slate hover:text-brand-emerald' }}"><i class="fas fa-store mr-2 text-brand-sage"></i>Katalog</a>
        @auth<a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl text-sm font-extrabold {{ request()->routeIs('dashboard*') ? 'bg-white text-brand-emerald shadow-sm' : 'text-brand-slate hover:text-brand-emerald' }}"><i class="fas fa-chart-line mr-2 text-brand-sage"></i>Admin</a>@endauth
      </nav>
      @auth
      <div class="flex items-center gap-2 border-l border-brand-accent pl-2 md:pl-3"><span class="hidden sm:inline-flex text-xs font-bold text-brand-emerald bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-lg">{{ Auth::user()->name }} <span class="text-brand-sage ml-1">({{ strtoupper(Auth::user()->role) }})</span></span><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-10 h-10 text-rose-600 hover:bg-rose-50 rounded-xl" aria-label="Keluar"><i class="fas fa-sign-out-alt"></i></button></form></div>
      @else<a href="{{ route('login') }}" class="px-4 py-2.5 bg-brand-emerald text-white rounded-xl text-sm font-extrabold shadow-md hover:opacity-90"><i class="fas fa-lock mr-2"></i>Masuk</a>@endauth
    </div>
  </div>
</header>
