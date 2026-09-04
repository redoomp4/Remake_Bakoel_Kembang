@extends('layouts.app')

@if (session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-300 bg-emerald-100 p-4 text-sm font-bold text-emerald-800">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-rose-300 bg-rose-100 p-4 text-sm font-bold text-rose-800">
        <p class="font-black mb-1">Gagal Menyimpan Data:</p>
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
    <div class="min-h-full bg-brand-offwhite">
        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

            {{-- Header --}}
            <header class="mb-8 flex flex-col gap-4 border-b border-brand-accent/60 pb-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="mb-2 text-xs font-black uppercase tracking-[0.2em] text-brand-slate">Operasional Gudang</p>
                    <h1 class="text-3xl font-black tracking-tight text-brand-emerald">Form Transaksi</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-brand-slate">Input item baru, dan transaksi tanpa berpindah halaman.</p>
                </div>
                <a href="{{ url()->previous() }}" class="inline-flex w-fit items-center gap-2 rounded-xl border border-brand-accent bg-white px-4 py-3 text-sm font-bold text-brand-slate shadow-sm transition hover:border-brand-emerald hover:text-brand-emerald">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i><span>Kembali</span>
                </a>
            </header>

            {{-- Tab Navigation --}}
            <div class="mb-6 overflow-x-auto rounded-2xl border border-brand-accent bg-white p-2 shadow-sm" role="tablist">
                <div class="flex min-w-max gap-2">
                    <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700" data-tab="item" role="tab" aria-selected="true">
                        <i class="fas fa-box mr-2" aria-hidden="true"></i>Tambah Item Baru
                    </button>
                    <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-transparent px-4 py-3 text-sm font-bold text-brand-slate hover:bg-brand-offwhite" data-tab="masuk" role="tab" aria-selected="false">
                        <i class="fas fa-arrow-down mr-2" aria-hidden="true"></i>Transaksi Masuk
                    </button>
                    <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-transparent px-4 py-3 text-sm font-bold text-brand-slate hover:bg-brand-offwhite" data-tab="keluar" role="tab" aria-selected="false">
                        <i class="fas fa-arrow-up mr-2" aria-hidden="true"></i>Transaksi Keluar
                    </button>
                </div>
            </div>

            {{-- Tab Contents --}}
            <div id="tab-content-item" class="oneshot-tab-content">
                @include('form.partials.tab-item')
            </div>
            <div id="tab-content-masuk" class="oneshot-tab-content hidden">
                @include('form.partials.tab-masuk')
            </div>
            <div id="tab-content-keluar" class="oneshot-tab-content hidden">
                @include('form.partials.tab-keluar')
            </div>

        </main>
    </div>

    {{-- ================================================================ --}}
    {{-- STYLE GLOBAL (termasuk dynamic width & semua komponen)           --}}
    {{-- ================================================================ --}}
    <style>
        /* --------------------------------------------
           VARIABEL
        -------------------------------------------- */
        :root {
            --brand-emerald: #0B4F35;
            --brand-slate: #475569;
            --border: #e2e8f0;
        }

        /* --------------------------------------------
           FIELD (dari sebelumnya)
        -------------------------------------------- */
        .field-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--brand-slate, #475569);
        }
        .field-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid var(--brand-accent, #dbe5df);
            background-color: #f7faf8;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
        .field-input:focus {
            border-color: var(--brand-emerald, #087443);
            outline: 2px solid transparent;
            box-shadow: 0 0 0 2px rgba(8, 116, 67, 0.2);
        }

        /* --------------------------------------------
           KOMPONEN PARTIALS
        -------------------------------------------- */
        .voice-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .voice-box .title {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #0B4F35;
            font-weight: 900;
            font-size: 1rem;
        }
        .voice-box .title i {
            color: #059669;
            font-size: 1.2rem;
        }
        .voice-box .desc {
            color: #475569;
            font-size: .75rem;
            margin-top: .25rem;
        }
        .voice-box .desc em {
            color: #065f46;
            font-weight: 700;
        }
        .btn-voice {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1.5rem;
            background: #0B4F35;
            color: #fff;
            border: 0;
            border-radius: 1rem;
            font-size: .85rem;
            font-weight: 800;
            cursor: pointer;
            transition: .2s;
            flex-shrink: 0;
        }
        .btn-voice:hover {
            background: #065f46;
            transform: translateY(-1px);
        }
        .btn-voice:disabled {
            opacity: .6;
            cursor: not-allowed;
        }
        .btn-voice.recording {
            background: #dc2626;
            animation: pulse 1.5s infinite;
        }
        .btn-voice.processing {
            background: #d97706;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .7; }
        }
        .voice-transcript {
            display: none;
            background: #fff;
            border: 1px solid #a7f3d0;
            border-radius: .75rem;
            padding: .75rem 1rem;
            margin-top: .75rem;
            font-size: .8rem;
            color: #475569;
        }
        .voice-transcript.show {
            display: block;
        }
        .voice-transcript .label {
            font-weight: 700;
            color: #065f46;
        }

        .form-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }
        .form-header {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .form-header .icon-box {
            width: 40px;
            height: 40px;
            background: #0B4F35;
            color: #fff;
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-header h2 {
            color: #0B4F35;
            font-weight: 900;
            font-size: 1.1rem;
            margin: 0;
        }
        .form-header p {
            color: #475569;
            font-size: .75rem;
            margin: 0;
        }
        .form-body {
            padding: 1.5rem;
        }
        .form-footer {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group:last-child {
            margin-bottom: 0;
        }
        .form-label {
            display: block;
            color: #475569;
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: .4rem;
        }
        .form-label .required {
            color: #dc2626;
        }
        .form-control {
            width: 100%;
            padding: .7rem 1rem;
            border: 2px solid var(--border);
            border-radius: .75rem;
            font-size: .9rem;
            font-weight: 600;
            transition: .2s;
            background: #fff;
        }
        .form-control:focus {
            outline: 0;
            border-color: #0B4F35;
            box-shadow: 0 0 0 4px rgba(11, 79, 53, .12);
        }
        .form-control:read-only {
            background: #f8fafc;
            color: #64748b;
        }
        select.form-control {
            appearance: auto;
        }
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
            font-weight: 400;
        }
        input[type="file"].form-control {
            padding: .5rem .75rem;
            font-weight: 400;
        }
        .form-error {
            color: #dc2626;
            font-size: .75rem;
            font-weight: 700;
            margin-top: .25rem;
        }
        .form-hint {
            color: #94a3b8;
            font-size: .7rem;
            margin-top: .25rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }
        @media (min-width: 768px) {
            .grid-2 { grid-template-columns: 1fr 1fr; }
            .grid-3 { grid-template-columns: 1fr 1fr 1fr; }
        }

        .hybrid-select {
            position: relative;
        }
        .hybrid-select .hybrid-input {
            width: 100%;
            padding: .7rem 2.5rem .7rem 1rem;
            border: 2px solid var(--border);
            border-radius: .75rem;
            font-size: .9rem;
            font-weight: 600;
            transition: .2s;
            background: #fff;
        }
        .hybrid-select .hybrid-input:focus {
            outline: 0;
            border-color: #0B4F35;
            box-shadow: 0 0 0 4px rgba(11, 79, 53, .12);
        }
        .hybrid-select .hybrid-toggle {
            position: absolute;
            top: 50%;
            right: .75rem;
            transform: translateY(-50%);
            background: none;
            border: 0;
            color: #94a3b8;
            cursor: pointer;
            padding: .25rem;
        }
        .hybrid-select .hybrid-options {
            display: none;
            position: absolute;
            z-index: 50;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: .25rem;
            max-height: 200px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: .75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,.1);
            padding: .25rem 0;
        }
        .hybrid-select .hybrid-options.show {
            display: block;
        }
        .hybrid-select .hybrid-option {
            padding: .6rem 1rem;
            font-size: .85rem;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            transition: .15s;
        }
        .hybrid-select .hybrid-option:hover {
            background: #ecfdf5;
            color: #0B4F35;
        }

        .total-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .total-box .label {
            color: #059669;
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .total-box .sub-label {
            color: #475569;
            font-size: .7rem;
            margin-top: .25rem;
        }
        .total-box .amount {
            color: #0B4F35;
            font-size: 1.5rem;
            font-weight: 900;
        }
        @media (min-width: 768px) {
            .total-box .amount { font-size: 2rem; }
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .75rem 1.5rem;
            background: #0B4F35;
            color: #fff;
            border: 0;
            border-radius: .75rem;
            font-size: .85rem;
            font-weight: 800;
            cursor: pointer;
            transition: .2s;
        }
        .btn-submit:hover:not(:disabled) {
            background: #065f46;
            transform: translateY(-1px);
        }
        .btn-submit:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        /* --------------------------------------------
           DINAMIS WIDE - FIX agar konten full-width
        -------------------------------------------- */
        /* Semua konten tab harus 100% */
        .oneshot-tab-content {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Semua form, card, voice box di dalam tab */
        .oneshot-tab-content form,
        .oneshot-tab-content .form-card,
        .oneshot-tab-content .voice-box {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Grid dan grup form */
        .oneshot-tab-content .grid-2,
        .oneshot-tab-content .grid-3,
        .oneshot-tab-content .form-group {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Input, select, textarea selalu 100% */
        .oneshot-tab-content .form-control,
        .oneshot-tab-content .hybrid-input,
        .oneshot-tab-content .field-input {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Hybrid select agar dropdown tidak overflow */
        .hybrid-select {
            width: 100% !important;
            max-width: 100% !important;
        }
        .hybrid-select .hybrid-options {
            width: 100% !important;
            max-width: 100% !important;
            left: 0 !important;
            right: 0 !important;
        }

        /* Main container sudah max-w-7xl, namun biarkan konten di dalamnya 100% */
        main {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        @media (min-width: 640px) {
            main {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }
        }
        @media (min-width: 1024px) {
            main {
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }
        }

        /* Hilangkan overflow horizontal */
        body {
            overflow-x: hidden !important;
        }
        *,
        *::before,
        *::after {
            box-sizing: border-box !important;
        }

        /* Responsive tambahan */
        @media (max-width: 768px) {
            .voice-box { flex-direction: column; align-items: stretch; }
            .btn-voice { justify-content: center; }
            .form-body { padding: 1rem; }
            .form-footer { flex-direction: column; }
            .btn-submit { width: 100%; justify-content: center; }
            .total-box { flex-direction: column; text-align: center; }
        }
        @media (max-width: 480px) {
            .form-body { padding: 0.75rem; }
            .voice-box { padding: 1rem; }
        }
    </style>

    {{-- ================================================================ --}}
    {{-- SCRIPT GLOBAL: tab switching, hybrid dropdown, toast            --}}
    {{-- ================================================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ===== TAB SWITCHING =====
            const buttons = document.querySelectorAll('.oneshot-tab-btn');
            const panels = document.querySelectorAll('.oneshot-tab-content');

            const activate = (tab) => {
                // Update tombol
                buttons.forEach((btn) => {
                    const active = btn.dataset.tab === tab;
                    btn.classList.toggle('border-emerald-600', active);
                    btn.classList.toggle('text-emerald-700', active);
                    btn.classList.toggle('bg-emerald-50', active);
                    btn.setAttribute('aria-selected', active ? 'true' : 'false');
                });

                // Update panel
                panels.forEach((panel) => {
                    const shouldShow = panel.id === `tab-content-${tab}`;
                    panel.classList.toggle('hidden', !shouldShow);
                });
            };

            // Event listener
            buttons.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    activate(btn.dataset.tab);
                });
            });

            // Aktifkan tab yang sedang aktif (default)
            const activeTab = document.querySelector('.oneshot-tab-btn[aria-selected="true"]');
            if (activeTab) {
                activate(activeTab.dataset.tab);
            } else {
                // Jika tidak ada, aktifkan tab pertama
                const firstBtn = document.querySelector('.oneshot-tab-btn');
                if (firstBtn) activate(firstBtn.dataset.tab);
            }

            // ===== TOAST =====
            window.toast = (message, type = 'success') => {
                const node = document.createElement('div');
                node.className =
                    `fixed bottom-5 right-5 z-50 rounded-xl px-5 py-3 text-sm font-bold shadow-2xl transition-all duration-300 ${type === 'success' ? 'bg-slate-900 text-emerald-300' : 'bg-rose-900 text-rose-100'}`;
                node.textContent = message;
                document.body.appendChild(node);
                setTimeout(() => node.remove(), 3200);
            };

            // ===== HYBRID DROPDOWN =====
            function initHybridDropdowns() {
                document.querySelectorAll('.hybrid-select').forEach((wrapper) => {
                    const input = wrapper.querySelector('.hybrid-input');
                    const toggleBtn = wrapper.querySelector('.hybrid-toggle');
                    const list = wrapper.querySelector('.hybrid-options');
                    if (!input || !list) return;

                    const openOptions = () => {
                        document.querySelectorAll('.hybrid-options').forEach((el) => {
                            if (el !== list) el.classList.add('hidden');
                        });
                        filterOptions();
                    };
                    const closeOptions = () => list.classList.add('hidden');

                    const filterOptions = () => {
                        const val = input.value.toLowerCase().trim();
                        let hasVisible = false;
                        list.querySelectorAll('.hybrid-option').forEach((opt) => {
                            const text = opt.textContent.toLowerCase();
                            if (text.includes(val)) {
                                opt.classList.remove('hidden');
                                hasVisible = true;
                            } else {
                                opt.classList.add('hidden');
                            }
                        });
                        list.classList.toggle('hidden', !hasVisible);
                    };

                    input.addEventListener('focus', openOptions);
                    input.addEventListener('input', () => {
                        list.classList.remove('hidden');
                        filterOptions();
                    });

                    toggleBtn?.addEventListener('click', (e) => {
                        e.stopPropagation();
                        list.classList.toggle('hidden');
                        if (!list.classList.contains('hidden')) filterOptions();
                    });

                    list.addEventListener('click', (e) => {
                        const option = e.target.closest('.hybrid-option');
                        if (option) {
                            input.value = option.dataset.value || option.textContent.trim();
                            closeOptions();
                        }
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.hybrid-select')) {
                        document.querySelectorAll('.hybrid-options').forEach((el) => el.classList.add('hidden'));
                    }
                });
            }

            initHybridDropdowns();

            // ===== CSRF TOKEN =====
            window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        });
    </script>
@endsection
