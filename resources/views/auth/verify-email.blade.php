@php($title = 'Verifikasi Email - Bakoel Kembang')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
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

    <!-- Background Botanical Glow Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-emerald/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-brand-sage/30 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-[#0A452E]/30 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-lg my-8">
        
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
                        Konfirmasi Keamanan Akun
                    </p>
                </div>
            </a>
        </div>

        <!-- Glass Card -->
        <div class="glass-card rounded-[32px] p-8 shadow-2xl space-y-6 text-center">
            
            <div class="w-20 h-20 bg-emerald-50 border-2 border-emerald-200 text-brand-emerald rounded-full flex items-center justify-center text-3xl mx-auto shadow-sm">
                <i class="fas fa-envelope-open-text"></i>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl font-black text-brand-emerald tracking-tight">Verifikasi Email Anda</h2>
                <p class="text-xs font-semibold text-brand-slate leading-relaxed">
                    Terima kasih telah mendaftar! Kami telah mengirimkan tautan konfirmasi ke alamat email Anda.
                </p>

                @auth
                    <div class="pt-2">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-extrabold text-brand-emerald">
                            <i class="fas fa-envelope"></i> {{ Auth::user()->email }}
                        </span>
                    </div>
                @endauth
            </div>

            {{-- Alerts --}}
            @if (session('status') === 'verification-link-sent')
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2 text-left">
                    <i class="fas fa-check-circle text-lg text-emerald-600 shrink-0"></i>
                    <span>Tautan verifikasi baru telah berhasil dikirim! Silakan periksa kotak masuk atau folder Spam email Anda.</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold flex items-center gap-2 text-left">
                    <i class="fas fa-exclamation-triangle text-lg shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Widget Panduan Membuka Folder Spam -->
            <div class="bg-amber-50/90 border-2 border-amber-200/80 rounded-2xl p-4 text-left space-y-3 shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-amber-900 font-extrabold text-xs">
                        <i class="fas fa-shield-virus text-amber-600 text-base"></i>
                        <span>PANDUAN: CARA CEK FOLDER SPAM</span>
                    </div>
                    <a href="https://mail.google.com/mail/u/0/#spam" target="_blank" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-xs">
                        <i class="fab fa-google"></i> Buka Spam Gmail
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-amber-950 font-medium pt-1">
                    <div class="bg-white/80 p-3 rounded-xl border border-amber-200/60 space-y-1">
                        <strong class="font-extrabold text-amber-900 flex items-center gap-1">
                            <i class="fas fa-mobile-alt text-amber-600"></i> Di HP (Aplikasi Gmail):
                        </strong>
                        <ol class="list-decimal list-inside space-y-0.5 text-gray-700 leading-snug">
                            <li>Buka aplikasi Gmail di HP</li>
                            <li>Ketuk menu garis tiga (<strong>≡</strong>) kiri atas</li>
                            <li>Pilih folder <strong>"Spam"</strong></li>
                            <li>Buka email & klik <strong>"Bukan Spam"</strong></li>
                        </ol>
                    </div>

                    <div class="bg-white/80 p-3 rounded-xl border border-amber-200/60 space-y-1">
                        <strong class="font-extrabold text-amber-900 flex items-center gap-1">
                            <i class="fas fa-laptop text-amber-600"></i> Di Komputer / Laptop:
                        </strong>
                        <ol class="list-decimal list-inside space-y-0.5 text-gray-700 leading-snug">
                            <li>Buka tab Gmail di browser</li>
                            <li>Di sidebar kiri, klik <strong>"Selengkapnya (More)"</strong></li>
                            <li>Klik folder <strong>"Spam"</strong></li>
                            <li>Buka email Bakoel Kembang & verifikasi</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Grid -->
            <div class="space-y-3 pt-2">
                <!-- Form Kirim Ulang Email -->
                <form method="POST" action="{{ route('verification.send') }}" onsubmit="disableSubmit(this)">
                    @csrf
                    <button type="submit" id="resendBtn" class="btn-gradient w-full py-3.5 rounded-2xl text-white font-black text-xs tracking-wider uppercase shadow-md cursor-pointer flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane" id="btnIcon"></i>
                        <span id="btnText">Kirim Ulang Email Verifikasi</span>
                    </button>
                </form>

                <!-- Quick Link to Gmail & Dashboard -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <a href="https://mail.google.com" target="_blank" class="py-3 px-4 bg-white border-2 border-brand-accent hover:border-brand-emerald rounded-2xl text-xs font-extrabold text-brand-emerald transition-all flex items-center justify-center gap-2 shadow-sm">
                        <i class="fab fa-google text-rose-500"></i> Buka Gmail
                    </a>
                    <a href="{{ route('dashboard') }}" class="py-3 px-4 bg-white border-2 border-brand-accent hover:border-brand-emerald rounded-2xl text-xs font-extrabold text-brand-emerald transition-all flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-chart-line text-brand-sage"></i> Ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Logout Link -->
            <div class="pt-4 border-t border-brand-accent/60 flex items-center justify-between text-xs font-bold">
                <span class="text-brand-slate">Ingin pakai akun lain?</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-rose-600 hover:underline font-black flex items-center gap-1">
                        <i class="fas fa-sign-out-alt"></i> Logout / Keluar
                    </button>
                </form>
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
        function disableSubmit(form) {
            const btn = document.getElementById('resendBtn');
            const txt = document.getElementById('btnText');
            const icon = document.getElementById('btnIcon');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            txt.textContent = 'Mengirim Email…';
            icon.className = 'fas fa-spinner fa-spin';
        }
    </script>
</body>
</html>
