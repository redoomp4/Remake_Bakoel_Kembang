<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-brand-offwhite">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'BakoelKembang'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
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
        :root {
            --emerald: #0B4F35;
            --sage: #8FA882;
            --slate: #475569;
            --offwhite: #FAF9F6;
            --accent: #E4E4D9
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--offwhite);
            color: #1f2937;
            min-height: 100vh
        }

        main .page-shell {
            max-width: 1280px;
            margin: 0 auto
        }

        .table-responsive {
            border: 1px solid var(--accent);
            border-radius: 1rem;
            overflow: auto;
            background: #fff
        }

        .table-responsive table {
            margin-bottom: 0
        }

        .table-responsive thead th {
            background: #f1f3ed;
            color: var(--emerald);
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid var(--accent);
            white-space: nowrap
        }

        .table-responsive tbody td {
            vertical-align: middle;
            border-color: #edf0e9
        }

        input:not([type=checkbox]):not([type=radio]),
        select,
        textarea {
            border: 2px solid var(--accent);
            border-radius: .75rem;
            padding: .75rem 1rem;
            background: #fff
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--emerald);
            outline: 0;
            box-shadow: 0 0 0 4px rgba(11, 79, 53, .12)
        }

        .btn-chip {
            transition: all .2s ease
        }

        .btn-chip:hover {
            transform: translateY(-1px)
        }

        .page-shell>.container,
        .page-shell>.container-fluid {
            max-width: 1200px;
            padding: 0;
            margin: 0 auto
        }

        .page-shell .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem
        }

        .page-shell .header h1,
        .page-shell .header h2,
        .page-shell .header h3,
        .page-shell .header h4 {
            color: var(--emerald);
            font-weight: 900;
            letter-spacing: -.025em;
            margin: 0
        }

        .page-shell .filter-form {
            background: #fff;
            border: 1px solid var(--accent);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 8px 24px rgba(11, 79, 53, .05)
        }

        .page-shell .filter-form label {
            color: var(--slate);
            font-weight: 800;
            font-size: .8rem;
            margin-bottom: .35rem
        }

        .page-shell .create-button,
        .page-shell .btn-primary {
            background: var(--emerald);
            border: 0;
            color: #fff;
            font-weight: 800;
            border-radius: .75rem;
            padding: .7rem 1rem;
            text-decoration: none
        }

        .page-shell .back-button,
        .page-shell .btn-secondary {
            background: #eef1eb;
            color: var(--emerald);
            font-weight: 800;
            border-radius: .75rem;
            padding: .7rem 1rem;
            text-decoration: none
        }

        .page-shell .table-wrapper {
            border: 1px solid var(--accent);
            border-radius: 1rem;
            overflow: auto;
            background: #fff
        }

        .page-shell .table-wrapper table {
            margin: 0
        }

        .page-shell .table-wrapper th {
            background: #f1f3ed;
            color: var(--emerald);
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em
        }

        .page-shell .table-wrapper td,
        .page-shell .table-wrapper th {
            border-color: #edf0e9;
            padding: .85rem .75rem
        }

        .page-shell .alert {
            border-radius: .85rem;
            border-width: 1px;
            font-weight: 700
        }

        .gaptek-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            background: #fff;
            border: 1px solid var(--accent);
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(11, 79, 53, .06)
        }

        .gaptek-tools__copy {
            max-width: 30rem
        }

        .gaptek-tools__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: var(--sage);
            font-size: .72rem;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase
        }

        .gaptek-tools h2 {
            margin: .35rem 0;
            color: var(--emerald);
            font-size: 1.15rem;
            font-weight: 900
        }

        .gaptek-tools p {
            margin: 0;
            color: var(--slate);
            font-size: .86rem;
            line-height: 1.55
        }

        .gaptek-tools__actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            justify-content: flex-end
        }

        .gaptek-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .55rem;
            min-height: 3rem;
            padding: .75rem 1rem;
            border: 0;
            border-radius: .85rem;
            font-weight: 900;
            font-size: .78rem;
            text-decoration: none;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease
        }

        .gaptek-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(11, 79, 53, .12)
        }

        .page-shell .form-control,
        .page-shell .form-select {
            min-height: 3.1rem;
            font-size: 1rem
        }

        .page-shell form label {
            display: block;
            color: var(--slate);
            font-weight: 800;
            margin-bottom: .45rem
        }

        .page-shell .text-danger,
        .page-shell .invalid-feedback {
            font-weight: 700;
            margin-top: .35rem
        }

        .page-shell .alert-danger {
            background: #fff4ed;
            border-color: #f2b27b;
            color: #9a3412
        }

        .page-shell .alert-success {
            background: #edf9f0;
            border-color: #9bd2a5;
            color: #166534
        }

        .pos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr));
            gap: 1rem;
            margin: 1rem 0
        }

        .pos-card {
            border: 2px solid var(--accent);
            border-radius: 1rem;
            background: #fff;
            padding: 1rem;
            text-align: center;
            color: var(--emerald);
            font-weight: 900
        }

        .pos-card__image {
            width: 100%;
            height: 6rem;
            object-fit: cover;
            border-radius: .75rem;
            background: #eef1eb;
            margin-bottom: .7rem
        }

        .pos-card__controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            margin-top: .7rem
        }

        .pos-card__button {
            width: 2.5rem;
            height: 2.5rem;
            border: 0;
            border-radius: .75rem;
            background: var(--emerald);
            color: #fff;
            font-size: 1.25rem;
            font-weight: 900
        }

        .human-alert {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: 1rem 1.1rem;
            border-radius: 1rem;
            background: #fff4ed;
            border: 1px solid #f2b27b;
            color: #9a3412;
            font-weight: 800;
            line-height: 1.5
        }

        .human-alert i {
            margin-top: .2rem
        }

        .gaptek-action--voice {
            background: var(--emerald);
            color: #fff
        }

        .gaptek-action--voice i {
            animation: gaptek-pulse 1.8s infinite
        }

        .gaptek-action--camera {
            background: #eef1eb;
            color: var(--emerald)
        }

        .gaptek-action--whatsapp {
            background: #1f9d55;
            color: #fff
        }

        .gaptek-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: grid;
            place-items: center;
            background: rgba(15, 23, 42, .48);
            padding: 1rem
        }

        .gaptek-modal[hidden] {
            display: none
        }

        .gaptek-modal__card {
            position: relative;
            width: min(100%, thirtyrem);
            background: #fff;
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: 0 25px 80px rgba(15, 23, 42, .25)
        }

        .gaptek-modal__close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            border: 0;
            background: #eef1eb;
            color: var(--emerald);
            border-radius: 999px;
            width: 2.5rem;
            height: 2.5rem
        }

        .gaptek-modal__card h2 {
            color: var(--emerald);
            font-size: 1.5rem;
            font-weight: 900;
            margin: .5rem 0
        }

        .gaptek-modal__hint {
            color: var(--slate);
            line-height: 1.55
        }

        .voice-record-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .65rem;
            width: 100%;
            min-height: 4rem;
            border: 0;
            border-radius: 1rem;
            background: var(--emerald);
            color: #fff;
            font-weight: 900;
            font-size: 1rem;
            margin: 1rem 0
        }

        .voice-record-button.is-recording {
            background: #b45309
        }

        .gaptek-modal textarea {
            width: 100%;
            font-size: 1rem
        }

        .gaptek-modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            margin-top: 1rem
        }

        .gaptek-button {
            border: 0;
            border-radius: .85rem;
            padding: .9rem 1.5rem;
            font-weight: 900;
            cursor: pointer
        }

        .gaptek-button--primary {
            background: var(--emerald);
            color: #fff
        }

        .gaptek-button--muted {
            background: #eef1eb;
            color: var(--emerald)
        }

        @keyframes gaptek-pulse {

            0%,
            100% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.18)
            }
        }

        @media(max-width:1023px) {
            main .page-shell {
                max-width: none
            }
        }

        @media(max-width:768px) {
            .page-shell .header {
                align-items: flex-start
            }

            .page-shell .header>* {
                max-width: 100%
            }

            .page-shell .filter-form {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: .75rem
            }

            .page-shell .filter-form .form-group {
                width: 100%;
                min-width: 0
            }

            .page-shell .table-wrapper table {
                min-width: 680px
            }

            .gaptek-tools {
                display: block;
                padding: 1rem
            }

            .gaptek-tools__actions {
                display: grid;
                grid-template-columns: 1fr;
                margin-top: 1rem
            }

            .gaptek-action {
                width: 100%
            }

            .gaptek-modal__card {
                padding: 1.25rem
            }

            .gaptek-modal__actions {
                display: grid;
                grid-template-columns: 1fr 1fr
            }

            .gaptek-button {
                padding: .85rem .75rem
            }
        }

        @media(min-width:1024px) {
            main>aside {
                min-height: calc(100vh - 150px)
            }
        }
    </style>
</head>
@if (Auth::check())
    <script>
        (function() {
            const limit = 900;
            let idle = 0;
            const reset = () => idle = 0;
            const logout = () => fetch('{{ route('logout') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => location.href = '{{ route('welcome') }}');
            setInterval(() => {
                idle++;
                if (idle >= limit) {
                    Swal.fire({
                        title: 'Auto Logout',
                        text: 'Anda telah logout otomatis karena tidak aktif selama 15 menit.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(r => r.isConfirmed && logout());
                    idle = 0
                }
            }, 1000);
            ['load', 'mousemove', 'keypress', 'click', 'scroll', 'touchstart'].forEach(e => window.addEventListener(e,
                reset, {
                    passive: true
                }))
        })();
    </script>
@endif

<body class="min-h-screen flex flex-col antialiased">
    @include('partials.header')
    <main class="flex-grow flex flex-col lg:flex-row">
        @auth @include('partials.sidebar') @endauth
        <div class="page-shell flex-grow w-full p-4 md:p-8">@auth @include('partials.gaptek-assistant') @endauth
            @yield('content')</div>
    </main>
    @include('partials.toast')
    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
