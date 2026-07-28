<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'BakoelKembang V3') }} - Botanical Nursery & Fintech</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Plus Jakarta Sans & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap core -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased bg-brand-offwhite text-gray-900">

    <!-- Header Banner Mode Lansia -->
    <div class="bg-brand-emerald text-brand-offwhite py-3 px-6 text-center text-xs md:text-sm font-black tracking-wider flex items-center justify-center gap-2 border-b border-white/10">
        <span class="animate-pulse">🟢</span>
        DESAIN RAMAH LANSIA V3 ACTIVE • TEKS EKSTRA BESAR (18px+) • TOMBOL LAPANG • FORM SAT-SET BEBAS BINGUNG!
    </div>

    <!-- Main Navigation Header -->
    @include('partials.header')

    <main class="flex-grow">
        @yield('content')
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