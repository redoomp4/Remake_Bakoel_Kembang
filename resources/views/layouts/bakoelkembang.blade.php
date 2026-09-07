<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bakoel Kembang V3') }} - Fintech Minimalis & Laci Kas Kebun</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Plus Jakarta Sans & JetBrains Mono -->
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- FontAwesome Icons via CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
                            emerald: '#0B4F35', // Deep luxurious emerald green
                            sage: '#8FA882', // Calm sage green
                            slate: '#475569', // Slate gray
                            offwhite: '#FAF9F6', // Luxury off-white canvas
                            accent: '#E4E4D9' // Light warm borders
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
        }

        html {
            scroll-behavior: smooth;
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

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #FAF9F6;
        }

        ::-webkit-scrollbar-thumb {
            background: #8FA882;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0B4F35;
        }

        /* perbaiki zoom in halaman publik item */
        /* Khusus halaman publik */
        .public-page {
            font-size: 0.9rem;
        }

        .public-page h1,
        .public-page h2 {
            line-height: 1.25;
        }

        .public-page .public-card {
            padding: 1.5rem;
        }
    </style>
    @stack('styles')
</head>

<body
    class="min-h-screen flex flex-col antialiased bg-brand-offwhite text-gray-900 selection:bg-brand-emerald selection:text-white">


    <!-- Main Navigation Header -->
    @include('partials.header')

    <!-- Content Slot -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Affirmation Toast Modal -->
    @include('partials.toast')

    <!-- Footer -->
    @include('partials.footer')

    @stack('scripts')
</body>

</html>
