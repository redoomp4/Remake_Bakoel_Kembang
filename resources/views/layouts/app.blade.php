<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BakoelKembang') }}</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Plus Jakarta Sans & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap core for legacy forms & tables compatibility -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Plus Jakarta Sans"', 'sans-serif'],
              mono: ['"JetBrains Mono"', 'monospace'],
            },
            colors: {
              brand: {
                emerald: '#0B4F35',
                sage: '#8FA882',
                slate: '#475569',
                offwhite: '#FAF9F6',
                accent: '#E4E4D9'
              }
            }
          }
        }
      }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF9F6;
            color: #1A1A1A;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .btn-chip {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-chip.active {
            background-color: #0B4F35;
            color: #ffffff;
            border-color: #0B4F35;
            transform: scale(1.03);
            box-shadow: 0 8px 16px -3px rgba(11, 79, 53, 0.25);
        }

        .input-big {
            font-size: 1.25rem;
            padding: 1rem 1.25rem;
            border: 3px solid #E4E4D9;
            border-radius: 16px;
            width: 100%;
            background-color: #ffffff;
            color: #1a1a1a;
            transition: all 0.2s ease;
        }
        .input-big:focus {
            outline: none;
            border-color: #0B4F35;
            box-shadow: 0 0 0 6px rgba(11, 79, 53, 0.15);
        }

        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #E4E4D9;
        }
    </style>
</head>

@if(Auth::check())
<script>
    (function () {
        const IDLE_TIMEOUT = 900; // 15 menit
        let idleTime = 0;

        function resetIdleTime() {
            idleTime = 0;
        }

        function logoutViaPost() {
            fetch('{{ route('logout') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => {
                window.location.href = '{{ route('welcome') }}';
            })
            .catch(error => {
                console.error('Logout error:', error);
            });
        }

        setInterval(() => {
            idleTime++;
            if (idleTime >= IDLE_TIMEOUT) {
                Swal.fire({
                    title: 'Auto Logout',
                    text: 'Anda telah logout otomatis karena tidak aktif selama 15 menit.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutViaPost();
                    }
                });
            }
        }, 1000);

        window.onload = resetIdleTime;
        document.onmousemove = resetIdleTime;
        document.onkeypress = resetIdleTime;
        document.onclick = resetIdleTime;
        document.onscroll = resetIdleTime;
    })();
</script>
@endif

<body class="min-h-screen flex flex-col antialiased bg-brand-offwhite text-gray-900">

    <!-- Header Banner Mode Lansia -->
    <div class="bg-brand-emerald text-brand-offwhite py-3 px-6 text-center text-xs md:text-sm font-black tracking-wider flex items-center justify-center gap-2 border-b border-white/10">
        <span class="animate-pulse">🟢</span>
        DESAIN RAMAH LANSIA V3 ACTIVE • TEKS EKSTRA BESAR (18px+) • TOMBOL LAPANG • FORM SAT-SET BEBAS BINGUNG!
    </div>

    <!-- Main Navigation Header -->
    @include('partials.header')

    <!-- Layout Container with Optional Sidebar for Auth Users -->
    <main class="flex-grow flex flex-col lg:flex-row">
        @auth
            @include('partials.sidebar')
        @endauth
        
        <div class="flex-grow p-4 md:p-8 w-full max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>

    <!-- Toast Modal -->
    @include('partials.toast')

    <!-- Footer -->
    @include('partials.footer')

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>