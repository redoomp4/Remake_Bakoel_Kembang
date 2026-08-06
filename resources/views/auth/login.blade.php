@php($title = 'Login - Bakoelkembang V3')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts - Plus Jakarta Sans & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- FontAwesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(228, 228, 217, 0.8);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #0B4F35 0%, #073A27 100%);
            transition: all 0.25s ease;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(11, 79, 53, 0.4);
        }
    </style>
</head>
<body class="min-h-screen bg-[#041E14] flex items-center justify-center p-4 relative overflow-x-hidden selection:bg-brand-emerald selection:text-white">

    <!-- Background Botanical Aesthetics (Glow Orbs & Botanical Pattern) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-emerald/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-brand-sage/30 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-[#0A452E]/30 rounded-full blur-3xl"></div>
        <!-- Decorative SVG Leaves -->
        <svg class="absolute top-10 right-10 opacity-10 text-white w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
        <svg class="absolute bottom-10 left-10 opacity-10 text-white w-72 h-72" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 59 16.17 3.82 21.34l1.42 1.42C8.83 19.17 11 10 17 8z"/></svg>
    </div>

    <!-- Main Auth Card Container -->
    <div class="relative z-10 w-full max-w-md my-8">
        
        <!-- Header Brand Badge -->
        <div class="text-center mb-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-3 group">
                <div class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex items-center justify-center text-brand-sage shadow-xl group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-seedling text-3xl"></i>
                </div>
                <div class="text-left">
                    <h1 class="text-2xl font-black tracking-tight text-white leading-none">
                        BAKOEL<span class="text-brand-sage font-semibold">KEMBANG</span>
                    </h1>
                    <p class="text-[10px] font-extrabold text-brand-sage uppercase tracking-widest mt-1">
                        Botanical Fintech & Kas Kebun V3
                    </p>
                </div>
            </a>
        </div>

        <!-- Glassmorphism Card -->
        <div class="glass-card rounded-[32px] p-8 shadow-2xl space-y-6">
            
            <div class="text-center space-y-1">
                <h2 class="text-2xl font-black text-brand-emerald tracking-tight">Selamat Datang Kembali!</h2>
                <p class="text-xs font-semibold text-brand-slate">Masuk untuk mengelola kas kebun dan inventaris bunga.</p>
            </div>

            {{-- Alerts --}}
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-md"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-md"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold space-y-1">
                    <div class="font-extrabold flex items-center gap-1.5 mb-1">
                        <i class="fas fa-exclamation-circle"></i> Mohon periksa kembali input Anda:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email Input --}}
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-brand-slate uppercase tracking-wider block">Email Kebun</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-brand-sage text-sm"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="nama@bakoelkembang.com"
                               class="w-full pl-11 pr-4 py-3.5 bg-white border-2 border-brand-accent rounded-2xl text-sm font-bold text-gray-900 focus:outline-none focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/15 transition-all">
                    </div>
                </div>

                {{-- Password Input --}}
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs font-bold text-brand-slate uppercase tracking-wider block">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-brand-sage text-sm"></i>
                        <input id="password" type="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-11 pr-11 py-3.5 bg-white border-2 border-brand-accent rounded-2xl text-sm font-bold text-gray-900 focus:outline-none focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/15 transition-all {{ $errors->has('password') ? 'border-rose-400' : '' }}">
                        <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-brand-slate hover:text-brand-emerald text-sm transition-colors focus:outline-none">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-gradient w-full py-4 rounded-2xl text-white font-black text-sm tracking-wider uppercase shadow-lg cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-right-to-bracket text-md"></i>
                    <span>MASUK SEKARANG</span>
                </button>
            </form>

            <div class="pt-4 border-t border-brand-accent/60 flex flex-col items-center gap-2 text-xs font-bold text-brand-slate">
                <a href="{{ route('magic.form') }}" class="text-brand-emerald hover:underline flex items-center gap-1">
                    <i class="fas fa-magic text-brand-sage"></i> Login Tanpa Password (Magic Link)
                </a>
                <div>
                    Belum punya akun? <a href="{{ route('register') }}" class="text-brand-emerald hover:underline font-black">Daftar Akun Baru</a>
                </div>
            </div>
        </div>

        <!-- Back to Home Link -->
        <div class="text-center mt-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-full text-xs font-bold backdrop-blur-md border border-white/10 transition-all">
                <i class="fas fa-arrow-left"></i> Kembali ke Katalog Publik
            </a>
        </div>

    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            toggleIcon.classList.toggle('bi-eye');
            toggleIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
