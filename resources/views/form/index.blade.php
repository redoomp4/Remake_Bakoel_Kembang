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
                    <h1 class="text-3xl font-black tracking-tight text-brand-emerald">Form Oneshot Master & Transaksi</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-brand-slate">Input master data, item baru, dan transaksi tanpa berpindah halaman.</p>
                </div>
                <a href="{{ url()->previous() }}" class="inline-flex w-fit items-center gap-2 rounded-xl border border-brand-accent bg-white px-4 py-3 text-sm font-bold text-brand-slate shadow-sm transition hover:border-brand-emerald hover:text-brand-emerald">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i><span>Kembali</span>
                </a>
            </header>

            {{-- Tab Navigation --}}
            <div class="mb-6 overflow-x-auto rounded-2xl border border-brand-accent bg-white p-2 shadow-sm" role="tablist">
                <div class="flex min-w-max gap-2">
                    <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700" data-tab="master" role="tab" aria-selected="true">
                        <i class="fas fa-bolt mr-2" aria-hidden="true"></i>Master Quick Add
                    </button>
                    <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-transparent px-4 py-3 text-sm font-bold text-brand-slate hover:bg-brand-offwhite" data-tab="item" role="tab" aria-selected="false">
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

            {{-- Tab Contents (masing-masing bawa script sendiri) --}}
            @include('form.partials.tab-master')
            @include('form.partials.tab-item')
            @include('form.partials.tab-masuk')
            @include('form.partials.tab-keluar')

        </main>
    </div>

    {{-- Style global (cuma sedikit) --}}
    <style>
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
    </style>

    {{-- Script global: tab switching, hybrid dropdown, toast (dipakai semua tab) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ===== TAB SWITCHING =====
            const buttons = document.querySelectorAll('.oneshot-tab-btn');
            const panels = document.querySelectorAll('.oneshot-tab-content');

            const activate = (tab) => {
                buttons.forEach((btn) => {
                    const active = btn.dataset.tab === tab;
                    btn.classList.toggle('border-emerald-600', active);
                    btn.classList.toggle('text-emerald-700', active);
                    btn.classList.toggle('bg-emerald-50', active);
                    btn.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                panels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.id !== `tab-content-${tab}`);
                });
            };

            buttons.forEach((btn) => {
                btn.addEventListener('click', () => activate(btn.dataset.tab));
            });

            // ===== TOAST =====
            window.toast = (message, type = 'success') => {
                const node = document.createElement('div');
                node.className =
                    `fixed bottom-5 right-5 z-50 rounded-xl px-5 py-3 text-sm font-bold shadow-2xl transition-all duration-300 ${type === 'success' ? 'bg-slate-900 text-emerald-300' : 'bg-rose-900 text-rose-100'}`;
                node.textContent = message;
                document.body.appendChild(node);
                setTimeout(() => node.remove(), 3200);
            };

            // ===== HYBRID DROPDOWN (global) =====
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

            // ===== CSRF TOKEN (dipakai oleh skrip di masing-masing tab) =====
            window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        });
    </script>
@endsection
