<header class="bg-white border-b border-brand-accent px-3 md:px-5 py-2 sticky top-0 z-40 shadow-sm">
    <div class="max-w-[1500px] mx-auto flex flex-wrap items-center justify-between gap-2">

        {{-- LOGO --}}
        <a href="{{ route('welcome') }}"
           class="flex items-center gap-2 group"
           aria-label="BakoelKembang beranda">

            <span class="w-9 h-9 md:w-10 md:h-10 bg-brand-emerald rounded-xl
                         flex items-center justify-center text-white shadow-md">
                <i class="fas fa-seedling text-lg md:text-xl"></i>
            </span>

            <span>
                <span class="block text-lg md:text-xl font-black tracking-tight
                             text-brand-emerald leading-none">
                    BAKOEL<span class="text-brand-sage font-semibold">KEMBANG</span>
                </span>

                <span class="block text-[8px] font-bold text-brand-slate
                             uppercase tracking-[.12em] mt-0.5">
                    Premium Orchids Nursery
                </span>
            </span>
        </a>


        {{-- NAVIGATION + USER --}}
        <div class="flex items-center gap-1.5 md:gap-2">

            <nav class="hidden md:flex items-center gap-1
                        bg-gray-100 p-1 rounded-xl border border-gray-200"
                 aria-label="Navigasi utama">

                <a href="{{ route('welcome') }}"
                   class="px-3 py-2 rounded-lg text-xs font-extrabold
                   {{ request()->routeIs('welcome')
                        ? 'bg-white text-brand-emerald shadow-sm'
                        : 'text-brand-slate hover:text-brand-emerald' }}">

                    <i class="fas fa-store mr-1.5 text-brand-sage"></i>
                    Katalog
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-lg text-xs font-extrabold
                       {{ request()->routeIs('dashboard*')
                            ? 'bg-white text-brand-emerald shadow-sm'
                            : 'text-brand-slate hover:text-brand-emerald' }}">

                        <i class="fas fa-chart-line mr-1.5 text-brand-sage"></i>Dashboard
                    </a>
                @endauth

            </nav>


            {{-- USER --}}
            @auth

                <div class="flex items-center gap-1.5
                            border-l border-brand-accent pl-1.5 md:pl-2">

                    <span class="hidden sm:inline-flex
                                 text-[10px] font-bold text-brand-emerald
                                 bg-emerald-50 border border-emerald-200
                                 px-2 py-1.5 rounded-lg">

                        {{ Auth::user()->username ?? Auth::user()->name }}

                        <span class="text-brand-sage ml-1">
                            ({{ strtoupper(Auth::user()->role) }})
                        </span>

                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                                class="w-8 h-8 text-rose-600
                                       hover:bg-rose-50 rounded-lg"
                                aria-label="Keluar">

                            <i class="fas fa-sign-out-alt text-sm"></i>

                        </button>
                    </form>

                </div>

            @else

                <a href="{{ route('login') }}"
                   class="px-3 py-2 bg-brand-emerald text-white
                          rounded-lg text-xs font-extrabold shadow-sm
                          hover:opacity-90">

                    <i class="fas fa-lock mr-1"></i>
                    Masuk

                </a>

            @endauth

        </div>
    </div>
</header>