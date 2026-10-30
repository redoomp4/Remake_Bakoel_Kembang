@extends('layouts.app')

@section('content')
    @php
        $stokAwalAktif = (bool) old('buat_stok_awal', false);
        $pemasokUmum = $pemasoks->firstWhere('nama_pemasok', 'Pemasok Umum');
        $pemasokUmumValue = (string) ($pemasokUmum?->id ?? 'Pemasok Umum');
        $pemasokTerpilih = (string) old('stok_awal_pemasok', $pemasokUmumValue);

        $oldKategori = (string) old('id_kategori');
        $oldSatuan = (string) old('id_satuan');

        $lokasiDefault = (string) old(
            'stok_awal_lokasi',
            $lokasis->firstWhere('nama_lokasi', 'Gudang Utama')?->nama_lokasi
                ?? $lokasis->first()?->nama_lokasi
                ?? 'Gudang Utama'
        );

        $kondisiDefault = (string) old(
            'stok_awal_kondisi',
            $kondisis->firstWhere('nama_kondisi', 'Segar')?->nama_kondisi
                ?? $kondisis->first()?->nama_kondisi
                ?? 'Segar'
        );

        $kategoriBaru = $oldKategori !== '' && !$kategori->contains(
            fn ($k) => (string) $k->kategori === $oldKategori || (string) $k->id === $oldKategori
        );
        $satuanBaru = $oldSatuan !== '' && !$satuan->contains(
            fn ($s) => (string) $s->nama_satuan === $oldSatuan || (string) $s->id === $oldSatuan
        );
        $lokasiBaru = $lokasiDefault !== '' && !$lokasis->contains(
            fn ($l) => (string) $l->nama_lokasi === $lokasiDefault || (string) $l->id === $lokasiDefault
        );
        $kondisiBaru = $kondisiDefault !== '' && !$kondisis->contains(
            fn ($k) => (string) $k->nama_kondisi === $kondisiDefault || (string) $k->id === $kondisiDefault
        );
        $pemasokBaru = $pemasokTerpilih !== ''
            && $pemasokTerpilih !== $pemasokUmumValue
            && !$pemasoks->contains(
                fn ($p) => (string) $p->id === $pemasokTerpilih || (string) $p->nama_pemasok === $pemasokTerpilih
            );
    @endphp

    <style>
        #item-create-form .select2-container--default .select2-selection--single {
            min-height: 64px !important;
            border: 2px solid #cbd5e1 !important;
            border-radius: 0.75rem !important;
            background: #fff !important;
        }
        #item-create-form .select2-container--default .select2-selection--single:focus,
        #item-create-form .select2-container--default.select2-container--focus .select2-selection--single,
        #item-create-form .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #047857 !important;
            box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.12) !important;
        }
        #item-create-form .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 60px !important;
            padding-left: 1rem !important;
            padding-right: 2.75rem !important;
            font-size: 1rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
        }
        #item-create-form .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
            font-weight: 600 !important;
        }
        #item-create-form .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 62px !important;
            right: 0.75rem !important;
        }
        #item-create-form .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 1.25rem !important;
            font-size: 1.25rem !important;
        }
        #item-create-form .select2-dropdown {
            border: 2px solid #047857 !important;
            border-radius: 0.9rem !important;
            overflow: hidden !important;
        }
        #item-create-form .select2-container--default .select2-search--dropdown .select2-search__field {
            min-height: 48px !important;
            padding: 0.7rem 1rem !important;
            border: 2px solid #cbd5e1 !important;
            border-radius: 0.65rem !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
        }
        #item-create-form .select2-results__option {
            padding: 0.85rem 1rem !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
        }
        #item-create-form .select2-results__option--highlighted[aria-selected] {
            background: #047857 !important;
            color: #fff !important;
        }
    </style>

    <div class="mx-auto w-full max-w-4xl px-4 py-6 sm:px-6 lg:py-8">

        <header class="mb-6">
            <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">Inventori · Master Barang</p>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-emerald-950 sm:text-4xl">Tambah Item Baru</h1>
                    <p class="mt-2 max-w-2xl text-base font-semibold leading-7 text-slate-600">
                        Isi nama dan kategori barang. Kalau barang sudah datang, centang stok awal agar langsung tercatat.
                    </p>
                </div>
                <a href="{{ route('item.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </header>

        @if ($errors->any())
            <div class="mb-5 rounded-2xl border-2 border-rose-200 bg-rose-50 p-4 sm:p-5">
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle-exclamation mt-1 text-xl text-rose-600"></i>
                    <div>
                        <p class="text-base font-black text-rose-800">Ada data yang belum lengkap</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm font-semibold text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form id="item-create-form" action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <section class="overflow-hidden rounded-2xl border-2 border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b-2 border-slate-100 bg-slate-50 p-4 sm:p-5">
                    <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-800 text-white">
                        <i class="fas fa-box"></i>
                    </span>
                    <div>
                        <h2 class="text-lg font-black text-emerald-950 sm:text-xl">Data Item</h2>
                        <p class="text-sm font-semibold text-slate-500">Bagian ini wajib diisi.</p>
                    </div>
                </div>

                <div class="space-y-5 p-4 sm:p-6">
                    <div>
                        <label for="nama_barang" class="mb-1.5 block text-base font-black text-slate-900">
                            Nama Item / Bunga <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}"
                            required autofocus placeholder="Contoh: Bunga Anggrek Bulan / Mawar Merah"
                            class="h-14 w-full rounded-xl border-2 border-slate-300 bg-white px-4 text-lg font-bold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        @error('nama_barang')
                            <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="id_kategori" class="mb-1.5 block text-base font-black text-slate-900">
                                Kategori <span class="text-rose-600">*</span>
                            </label>
                            <select id="id_kategori" name="id_kategori" required
                                class="select2-dynamic w-full" data-placeholder="Pilih atau ketik kategori baru...">
                                <option value=""></option>
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->kategori }}" @selected($oldKategori === (string) $k->kategori || $oldKategori === (string) $k->id)>
                                        {{ $k->kategori }}
                                    </option>
                                @endforeach
                                @if ($kategoriBaru)
                                    <option value="{{ $oldKategori }}" selected>{{ $oldKategori }}</option>
                                @endif
                            </select>
                            <p class="mt-1.5 text-sm font-semibold text-slate-500">
                                <i class="fas fa-lightbulb text-amber-500"></i> Ketik nama baru lalu tekan Enter untuk membuat kategori.
                            </p>
                            @error('id_kategori')
                                <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="id_satuan" class="mb-1.5 block text-base font-black text-slate-900">
                                Satuan <span class="text-rose-600">*</span>
                            </label>
                            <select id="id_satuan" name="id_satuan" required
                                class="select2-dynamic w-full" data-placeholder="Pilih atau ketik satuan baru...">
                                <option value=""></option>
                                @foreach ($satuan as $s)
                                    <option value="{{ $s->nama_satuan }}" @selected($oldSatuan === (string) $s->nama_satuan || $oldSatuan === (string) $s->id)>
                                        {{ $s->nama_satuan }}
                                    </option>
                                @endforeach
                                @if ($satuanBaru)
                                    <option value="{{ $oldSatuan }}" selected>{{ $oldSatuan }}</option>
                                @endif
                            </select>
                            <p class="mt-1.5 text-sm font-semibold text-slate-500">
                                <i class="fas fa-lightbulb text-amber-500"></i> Ketik nama baru lalu tekan Enter untuk membuat satuan.
                            </p>
                            @error('id_satuan')
                                <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:max-w-xs">
                        <label for="stok_minimum" class="mb-1.5 block text-base font-black text-slate-900">
                            Stok Minimum <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" id="stok_minimum" name="stok_minimum" min="0" inputmode="numeric"
                            value="{{ old('stok_minimum', 0) }}" required placeholder="Contoh: 5"
                            class="h-14 w-full rounded-xl border-2 border-slate-300 bg-white px-4 text-lg font-black text-slate-900 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <p class="mt-1.5 text-sm font-semibold text-slate-500">Batas peringatan saat stok mulai menipis.</p>
                        @error('stok_minimum')
                            <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border-2 border-emerald-300 bg-white shadow-sm">
                <label for="buat_stok_awal"
                    class="flex cursor-pointer items-start gap-4 bg-emerald-50 p-4 transition hover:bg-emerald-100/70 sm:p-6">
                    <input type="checkbox" id="buat_stok_awal" name="buat_stok_awal" value="1"
                        class="mt-0.5 h-7 w-7 flex-shrink-0 rounded border-2 border-emerald-400 text-emerald-700 focus:ring-4 focus:ring-emerald-200"
                        @checked($stokAwalAktif)>
                    <span>
                        <span class="block text-lg font-black text-emerald-950 sm:text-xl">
                            Masukkan Sebagai Stok Awal (Barang Masuk)
                        </span>
                        <span class="mt-1 block text-sm font-semibold leading-6 text-emerald-800">
                            Centang kalau barang ini sudah diterima. Sistem akan mencatat item dan transaksi masuk sekaligus.
                        </span>
                    </span>
                </label>

                <div id="stok-awal-panel" class="{{ $stokAwalAktif ? '' : 'hidden' }} border-t-2 border-emerald-200 p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="stok_awal_jumlah" class="mb-1.5 block text-base font-black text-slate-900">
                                Jumlah Stok Masuk <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" id="stok_awal_jumlah" name="stok_awal_jumlah" min="1" inputmode="numeric"
                                value="{{ old('stok_awal_jumlah') }}" placeholder="Contoh: 10"
                                class="h-14 w-full rounded-xl border-2 border-slate-300 bg-white px-4 text-lg font-black text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                            @error('stok_awal_jumlah')
                                <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stok_awal_harga" class="mb-1.5 block text-base font-black text-slate-900">
                                Harga Beli Satuan <span class="text-rose-600">*</span>
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center rounded-l-xl border-2 border-r-0 border-slate-300 bg-slate-100 px-4 text-base font-black text-slate-700">Rp</span>
                                <input type="number" id="stok_awal_harga" name="stok_awal_harga" min="0" step="100" inputmode="numeric"
                                    value="{{ old('stok_awal_harga') }}" placeholder="Contoh: 15000"
                                    class="h-14 w-full rounded-r-xl border-2 border-slate-300 bg-white px-4 text-lg font-black text-emerald-900 placeholder:font-normal placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                            </div>
                            @error('stok_awal_harga')
                                <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label for="stok_awal_pemasok" class="mb-1.5 block text-base font-black text-slate-900">
                                Pemasok / Supplier
                            </label>
                            <select id="stok_awal_pemasok" name="stok_awal_pemasok"
                                class="select2-dynamic w-full" data-placeholder="Pilih atau ketik pemasok baru...">
                                <option value="{{ $pemasokUmumValue }}" {{ $pemasokTerpilih === $pemasokUmumValue || $pemasokTerpilih === 'Pemasok Umum' ? 'selected' : '' }}>
                                    Pemasok Umum (Default)
                                </option>
                                @foreach ($pemasoks as $pem)
                                    @continue($pem->nama_pemasok === 'Pemasok Umum')
                                    <option value="{{ $pem->id }}" {{ $pemasokTerpilih === (string) $pem->id || $pemasokTerpilih === (string) $pem->nama_pemasok ? 'selected' : '' }}>
                                        {{ $pem->nama_pemasok }}
                                    </option>
                                @endforeach
                                @if ($pemasokBaru)
                                    <option value="{{ $pemasokTerpilih }}" selected>{{ $pemasokTerpilih }}</option>
                                @endif
                            </select>
                            @error('stok_awal_pemasok')
                                <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stok_awal_lokasi" class="mb-1.5 block text-base font-black text-slate-900">
                                Lokasi Stok
                            </label>
                            <select id="stok_awal_lokasi" name="stok_awal_lokasi"
                                class="select2-dynamic w-full" data-placeholder="Pilih atau ketik lokasi baru...">
                                <option value=""></option>
                                @foreach ($lokasis as $l)
                                    <option value="{{ $l->nama_lokasi }}" @selected($lokasiDefault === (string) $l->nama_lokasi || $lokasiDefault === (string) $l->id)>
                                        {{ $l->nama_lokasi }}
                                    </option>
                                @endforeach
                                @if ($lokasiBaru)
                                    <option value="{{ $lokasiDefault }}" selected>{{ $lokasiDefault }}</option>
                                @endif
                            </select>
                            @error('stok_awal_lokasi')
                                <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stok_awal_kondisi" class="mb-1.5 block text-base font-black text-slate-900">
                                Kondisi Barang
                            </label>
                            <select id="stok_awal_kondisi" name="stok_awal_kondisi"
                                class="select2-dynamic w-full" data-placeholder="Pilih atau ketik kondisi baru...">
                                <option value=""></option>
                                @foreach ($kondisis as $k)
                                    <option value="{{ $k->nama_kondisi }}" @selected($kondisiDefault === (string) $k->nama_kondisi || $kondisiDefault === (string) $k->id)>
                                        {{ $k->nama_kondisi }}
                                    </option>
                                @endforeach
                                @if ($kondisiBaru)
                                    <option value="{{ $kondisiDefault }}" selected>{{ $kondisiDefault }}</option>
                                @endif
                            </select>
                            @error('stok_awal_kondisi')
                                <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-900">
                        <span><i class="fas fa-calendar-day"></i> Tanggal: Hari ini</span>
                        <span><i class="fas fa-lightbulb"></i> Lokasi dan kondisi bisa dipilih atau diketik baru.</span>
                    </div>
                </div>
            </section>

            <details class="overflow-hidden rounded-2xl border-2 border-slate-200 bg-white shadow-sm">
                <summary class="cursor-pointer list-none p-4 text-base font-black text-slate-700 transition hover:bg-slate-50 sm:p-5">
                    <span class="flex items-center gap-3">
                        <i class="fas fa-circle-plus text-emerald-700"></i>
                        Informasi Tambahan (Opsional)
                    </span>
                </summary>

                <div class="space-y-5 border-t-2 border-slate-100 p-4 sm:p-6">
                    <div>
                        <label for="deskripsi" class="mb-1.5 block text-base font-black text-slate-900">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            placeholder="Tuliskan catatan atau deskripsi singkat bunga (misal: warna kelopak, cara perawatan, atau ketahanan bunga)..."
                            class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-base font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="foto" class="mb-1.5 block text-base font-black text-slate-900">Foto Barang</label>
                        <input type="file" id="foto" name="foto" accept="image/*"
                            class="w-full rounded-xl border-2 border-slate-300 bg-white p-3 text-base font-semibold text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-100 file:px-4 file:py-2 file:text-sm file:font-black file:text-emerald-800 focus:border-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <p class="mt-1.5 text-sm font-semibold text-slate-500">Maksimal 5MB. Boleh dikosongkan.</p>
                        @error('foto')
                            <p class="mt-1.5 text-sm font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </details>

            <div class="flex flex-col-reverse gap-3 rounded-2xl border-2 border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:justify-end sm:p-5">
                <a href="{{ route('item.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-6 py-4 text-base font-black text-slate-700 transition hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-8 py-4 text-base font-black text-white shadow-md transition hover:bg-emerald-800 sm:text-lg">
                    <i class="fas fa-save"></i> Simpan Item
                </button>
            </div>
        </form>
    </div>

    <script>
        const stokAwalToggle = document.getElementById('buat_stok_awal');
        const stokAwalPanel = document.getElementById('stok-awal-panel');
        const stokAwalJumlah = document.getElementById('stok_awal_jumlah');
        const stokAwalHarga = document.getElementById('stok_awal_harga');
        const stokAwalPemasok = document.getElementById('stok_awal_pemasok');
        const stokAwalLokasi = document.getElementById('stok_awal_lokasi');
        const stokAwalKondisi = document.getElementById('stok_awal_kondisi');
        let stokAwalSelect2Ready = false;

        function initDynamicSelect2(selector) {
            if (!window.jQuery || !$.fn.select2) return;

            $(selector).each(function () {
                const $select = $(this);
                if ($select.data('select2')) return;

                const placeholder = $select.data('placeholder') || 'Pilih atau ketik data baru...';

                $select.select2({
                    tags: true,
                    allowClear: true,
                    width: '100%',
                    placeholder: placeholder,
                    createTag: function (params) {
                        const term = $.trim(params.term);
                        if (term === '') return null;

                        return {
                            id: term,
                            text: term,
                            newTag: true
                        };
                    },
                    templateResult: function (data) {
                        if (data.newTag) {
                            const $span = $('<span class="font-black text-emerald-800"></span>');
                            $span.append('<i class="fas fa-plus-circle mr-1"></i> Tambah Baru: ');
                            $span.append($('<strong>').text(data.text));
                            return $span;
                        }
                        return data.text;
                    },
                    language: {
                        noResults: function () {
                            return 'Belum ada. Ketik nama lalu tekan Enter untuk membuat baru.';
                        },
                        searching: function () {
                            return 'Mencari...';
                        }
                    }
                });
            });
        }

        function initStokAwalSelect2() {
            if (stokAwalSelect2Ready) return;
            initDynamicSelect2('#stok_awal_pemasok, #stok_awal_lokasi, #stok_awal_kondisi');
            stokAwalSelect2Ready = true;
        }

        if (window.jQuery && $.fn.select2) {
            initDynamicSelect2('#id_kategori, #id_satuan');
        }

        function syncStokAwal(clearWhenOff = false) {
            const aktif = stokAwalToggle.checked;

            stokAwalPanel.classList.toggle('hidden', !aktif);
            stokAwalPanel.setAttribute('aria-hidden', String(!aktif));

            [stokAwalJumlah, stokAwalHarga, stokAwalPemasok, stokAwalLokasi, stokAwalKondisi].forEach(function (el) {
                el.disabled = !aktif;
                el.required = aktif && el !== stokAwalPemasok && el !== stokAwalLokasi && el !== stokAwalKondisi;
            });

            if (aktif) {
                initStokAwalSelect2();
                setTimeout(function () {
                    if (window.jQuery && $.fn.select2) {
                        $(window).trigger('resize');
                    }
                }, 60);
            }

            if (!aktif && clearWhenOff) {
                stokAwalJumlah.value = '';
                stokAwalHarga.value = '';
            }
        }

        stokAwalToggle.addEventListener('change', function () {
            syncStokAwal(true);
            if (this.checked) stokAwalJumlah.focus();
        });

        syncStokAwal(false);
    </script>
@endsection
