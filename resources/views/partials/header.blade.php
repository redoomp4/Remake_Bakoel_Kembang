<header class="bg-white border-b border-brand-accent px-3 md:px-5 py-2 sticky top-0 z-40 shadow-sm">

    <div class="max-w-[1500px] mx-auto relative flex items-center justify-between">

        {{-- LOGO --}}
        <a href="{{ route('welcome') }}" class="flex items-center gap-2 group shrink-0" aria-label="BakoelKembang beranda">

            <span
                class="w-9 h-9 md:w-10 md:h-10 bg-brand-emerald rounded-xl
                         flex items-center justify-center text-white shadow-md">

                <i class="fas fa-seedling text-lg md:text-xl"></i>

            </span>

            {{-- Nama brand disembunyikan pada HP sangat kecil --}}
            <span class="hidden sm:block">

                <span
                    class="block text-lg md:text-xl font-black tracking-tight
                             text-brand-emerald leading-none">

                    BAKOEL<span class="text-brand-sage font-semibold">
                        KEMBANG
                    </span>

                </span>

                <span
                    class="block text-[8px] font-bold text-brand-slate
                             uppercase tracking-[.12em] mt-0.5">

                    Premium Orchids Nursery

                </span>

            </span>

        </a>


        {{-- NAVIGASI TENGAH --}}
        <nav class="absolute left-1/2 -translate-x-1/2
                   flex items-center gap-1
                   bg-gray-100 p-1 rounded-xl
                   border border-gray-200"
            aria-label="Navigasi utama">

            {{-- PROFIL WEBSITE --}}
            <a href="{{ route('profil') }}"
                class="px-2.5 sm:px-3 py-2 rounded-lg
                      text-[10px] sm:text-xs font-extrabold whitespace-nowrap
               {{ request()->routeIs('profil')
                   ? 'bg-white text-brand-emerald shadow-sm'
                   : 'text-brand-slate hover:text-brand-emerald' }}">

                <i class="fas fa-circle-info sm:mr-1.5 text-brand-sage"></i>

                <span>
                    Profil
                </span>

            </a>

            {{-- KATALOG --}}
            <a href="{{ route('welcome') }}"
                class="px-2.5 sm:px-3 py-2 rounded-lg
                      text-[10px] sm:text-xs font-extrabold whitespace-nowrap
               {{ request()->routeIs('welcome')
                   ? 'bg-white text-brand-emerald shadow-sm'
                   : 'text-brand-slate hover:text-brand-emerald' }}">

                <i class="fas fa-store sm:mr-1.5 text-brand-sage"></i>

                <span class="hidden xs:inline">
                    Katalog
                </span>

                {{-- Untuk layar kecil --}}
                <span class="inline xs:hidden">
                    Katalog
                </span>

            </a>



            {{-- ADMIN --}}
            @auth

                <a href="{{ route('dashboard') }}"
                    class="px-2 sm:px-3 py-2 rounded-lg
                           text-[10px] sm:text-xs font-extrabold
                           whitespace-nowrap transition
                    {{ request()->routeIs('dashboard*')
                        ? 'bg-white text-brand-emerald shadow-sm'
                        : 'text-brand-slate hover:text-brand-emerald' }}">

                    <i class="fas fa-chart-line sm:mr-1 text-brand-sage"></i>

                    <span>
                        Admin
                    </span>

                </a>

            @endauth

        </nav>


        {{-- USER --}}
        <div class="flex items-center shrink-0">

            @auth

                <div class="flex items-center gap-1.5
                            border-l border-brand-accent pl-2">

                    <span
                        class="hidden lg:inline-flex
                                 text-[10px] font-bold text-brand-emerald
                                 bg-emerald-50 border border-emerald-200
                                 px-2 py-1.5 rounded-lg">

                        {{ Auth::user()->name }}

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
                    class="w-8 h-8 sm:w-auto
                          sm:px-3 sm:py-2
                          flex items-center justify-center
                          bg-brand-emerald text-white
                          rounded-lg text-xs font-extrabold shadow-sm
                          hover:opacity-90">

                    <i class="fas fa-lock sm:mr-1"></i>

                    <span class="hidden sm:inline">
                        Masuk
                    </span>

                </a>
            @endauth

        </div>

    </div>

</header>
