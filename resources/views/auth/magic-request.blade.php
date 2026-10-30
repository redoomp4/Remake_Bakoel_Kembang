@php($title = 'Login Tanpa Password - Bakoel Kembang')
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

    <!-- FontAwesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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

    <!-- Background Botanical Aesthetics -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-emerald/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-brand-sage/30 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-[#0A452E]/30 rounded-full blur-3xl"></div>
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
                        Login Tanpa Password (Email)
                    </p>
                </div>
            </a>
        </div>

        <!-- Glassmorphism Card -->
        <div class="glass-card rounded-[32px] p-6 sm:p-8 shadow-2xl space-y-6">
            
            <div class="text-center space-y-1">
                <div class="w-16 h-16 bg-emerald-50 text-brand-emerald rounded-full flex items-center justify-center text-2xl mx-auto mb-2 border border-emerald-200">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h2 class="text-2xl font-black text-brand-emerald tracking-tight">Login Tanpa Password</h2>
                <p class="text-xs font-semibold text-brand-slate">Masukkan email akun Anda. Kami akan mengirimkan tautan login instan langsung ke kotak masuk email Anda.</p>
            </div>

            {{-- Alerts --}}
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold space-y-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-base text-emerald-600 shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                    <div class="pt-2 border-t border-emerald-200/60 flex justify-center">
                        <a href="https://mail.google.com" target="_blank" class="w-full py-2.5 bg-brand-emerald hover:bg-[#073A27] text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow flex items-center justify-center gap-2">
                            <i class="fab fa-google text-rose-400"></i>
                            <span>Buka Gmail Saya</span>
                        </a>
                    </div>
                </div>

                <!-- Widget Panduan Membuka Folder Spam -->
                <div class="bg-amber-50/90 border-2 border-amber-200/80 rounded-2xl p-4 text-left space-y-3 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-amber-900 font-extrabold text-xs">
                            <i class="fas fa-shield-virus text-amber-600 text-base"></i>
                            <span>CARA CEK FOLDER SPAM</span>
                        </div>
                        <a href="https://mail.google.com/mail/u/0/#spam" target="_blank" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all flex items-center gap-1 shadow-xs">
                            <i class="fab fa-google"></i> Buka Spam Gmail
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-amber-950 font-medium pt-1">
                        <div class="bg-white/80 p-2.5 rounded-xl border border-amber-200/60 space-y-1">
                            <strong class="font-extrabold text-amber-900 flex items-center gap-1">
                                <i class="fas fa-mobile-alt text-amber-600"></i> Di HP:
                            </strong>
                            <ol class="list-decimal list-inside space-y-0.5 text-gray-700 leading-snug">
                                <li>Buka Gmail -> Menu (<strong>≡</strong>)</li>
                                <li>Pilih folder <strong>"Spam"</strong></li>
                                <li>Klik <strong>"Bukan Spam"</strong></li>
                            </ol>
                        </div>

                        <div class="bg-white/80 p-2.5 rounded-xl border border-amber-200/60 space-y-1">
                            <strong class="font-extrabold text-amber-900 flex items-center gap-1">
                                <i class="fas fa-laptop text-amber-600"></i> Di Laptop/PC:
                            </strong>
                            <ol class="list-decimal list-inside space-y-0.5 text-gray-700 leading-snug">
                                <li>Di Gmail klik <strong>"More"</strong></li>
                                <li>Klik folder <strong>"Spam"</strong></li>
                                <li>Buka email login Bakoel Kembang</li>
                            </ol>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold space-y-1">
                    <div class="font-extrabold flex items-center gap-1.5 mb-1">
                        <i class="fas fa-exclamation-circle"></i> Periksa kembali email Anda:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('magic.request') }}" class="space-y-4" onsubmit="disableSubmit(this)">
                @csrf

                {{-- Input Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-brand-slate uppercase tracking-wider block">Alamat Email Terdaftar</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-brand-sage text-base"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="nama@bakoelkembang.com"
                               class="w-full pl-11 pr-4 py-3.5 bg-white border-2 border-brand-accent rounded-2xl text-sm font-bold text-gray-900 focus:outline-none focus:border-brand-emerald focus:ring-4 focus:ring-brand-emerald/15 transition-all">
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="submitBtn" class="btn-gradient w-full py-4 rounded-2xl text-white font-black text-sm tracking-wider uppercase shadow-lg cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane text-md" id="submitIcon"></i>
                    <span id="btnText">KIRIM LINK LOGIN KE EMAIL</span>
                </button>
            </form>

            <div class="pt-4 border-t border-brand-accent/60 flex flex-col items-center gap-2 text-xs font-bold text-brand-slate">
                <div>
                    Punya kata sandi? <a href="{{ route('login') }}" class="text-brand-emerald hover:underline font-black">Login Biasa</a>
                </div>
                <div>
                    Belum punya akun? <a href="{{ route('register') }}" class="text-brand-emerald hover:underline font-black">Daftar Akun Baru</a>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div class="text-center mt-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-full text-xs font-bold backdrop-blur-md border border-white/10 transition-all">
                <i class="fas fa-arrow-left"></i> Kembali ke Katalog Publik
            </a>
        </div>

    </div>

    <script>
        function disableSubmit(form) {
            const btn = document.getElementById('submitBtn');
            const txt = document.getElementById('btnText');
            const icon = document.getElementById('submitIcon');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            txt.textContent = 'Mengirim Link…';
            icon.className = 'fas fa-spinner fa-spin';
        }
    </script>
</body>
</html>
