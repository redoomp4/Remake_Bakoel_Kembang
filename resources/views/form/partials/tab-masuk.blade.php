<section id="masuk" class="tab-content" role="tabpanel">

    <style>
        /* Tampilan ramah lansia: dropdown lebih tinggi & tulisan lebih besar */
        #form-barang-masuk .select2-container--default .select2-selection--single {
            min-height: 64px !important;
            border-radius: 1rem !important;
            border-color: #94a3b8 !important;
            padding: 0.5rem 1rem !important;
        }
        #form-barang-masuk .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 1.2rem !important;
            font-weight: 800 !important;
        }
        #form-barang-masuk .select2-container--default .select2-selection--single .select2-selection__placeholder {
            font-size: 1.1rem !important;
        }
        #form-barang-masuk .select2-dropdown {
            border-radius: 1rem !important;
        }
        #form-barang-masuk .select2-results__option {
            padding: 0.85rem 1rem !important;
            font-size: 1.05rem !important;
        }
        #form-barang-masuk .select2-container--default .select2-search--dropdown .select2-search__field {
            font-size: 1.1rem !important;
            padding: 0.75rem !important;
        }
    </style>

    {{-- SWITCHER MODE INPUT (SATUAN vs MASSAL) --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border-2 border-emerald-200 bg-white p-3 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 font-black">
                <i class="fas fa-receipt text-xl"></i>
            </span>
            <div>
                <h3 class="text-base sm:text-lg font-black text-emerald-950">Mode Transaksi Barang Masuk</h3>
                <p class="text-xs text-slate-500">Pilih catat satu per satu atau input banyak barang sekaligus (Nota Restock)</p>
            </div>
        </div>
        <div class="flex rounded-xl bg-slate-100 p-1">
            <button type="button" id="btn-mode-masuk-single" onclick="switchMasukMode('single')"
                class="mode-switch-btn flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-black transition-all bg-white text-emerald-800 shadow-sm">
                <i class="fas fa-microphone"></i> Satu per Satu / Suara
            </button>
            <button type="button" id="btn-mode-masuk-bulk" onclick="switchMasukMode('bulk')"
                class="mode-switch-btn flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-black transition-all text-slate-600 hover:text-emerald-800">
                <i class="fas fa-file-invoice-dollar"></i> Input Massal (Nota Restock)
            </button>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 1. MODE SATU PER SATU / SUARA --}}
    {{-- ======================================================== --}}
    <div id="container-masuk-single">
        {{-- Voice Assistant Ringkas --}}
        <div class="voice-box mb-6">
            <div>
                <div class="title text-base">
                    <i class="fas fa-robot text-emerald-600"></i> Asisten Suara (Barang Masuk)
                </div>
                <div class="desc text-xs mt-1">
                    Tekan mic & sebutkan transaksi (Contoh: <em>"Masuk Mawar Merah 50 tangkai harga 10 ribu dari Sumber Tani"</em>).
                </div>
            </div>
            <button type="button" id="btn-voice-masuk" class="btn-voice">
                <i class="fas fa-microphone" id="voice-icon-masuk"></i>
                <span id="voice-status-masuk">Mulai Bicara</span>
            </button>
            <div id="voice-transcript-masuk" class="voice-transcript">
                <span class="label font-bold text-emerald-700">Terdengar:</span> <span id="voice-text-masuk" class="italic">...</span>
            </div>
        </div>

        @php
            $defaultLokasi = old('lokasi_input', $lokasis->first()?->nama_lokasi ?? 'Gudang Utama');
            $defaultKondisi = old('kondisi_input', $kondisis->first()?->nama_kondisi ?? 'Segar');
            $pemasokUmum = $pemasoks->firstWhere('nama_pemasok', 'Pemasok Umum');
            $pemasokUmumValue = (string) ($pemasokUmum?->id ?? 'Pemasok Umum');
            $pemasokSelectedValue = (string) old('pemasok_id', $pemasokUmumValue);
        @endphp

        {{-- Form Transaksi Barang Masuk Satuan --}}
        <form id="form-barang-masuk" action="{{ route('form.barang-masuk.store') }}" method="POST"
            data-finish-url="{{ route('barang-masuk.index') }}"
            class="form-card shadow-sm border-2 border-emerald-200">
            @csrf

            {{-- Field sekunder tetap dikirim, tetapi otomatis terisi & disembunyikan --}}
            <input type="hidden" name="tgl_masuk" id="input-tgl-masuk" value="{{ old('tgl_masuk', now()->format('Y-m-d')) }}">
            <input type="hidden" name="tgl_kadaluarsa" id="input-tgl-kadaluarsa" value="{{ old('tgl_kadaluarsa') }}">
            <input type="hidden" name="lokasi_input" id="input-lokasi-masuk" value="{{ $defaultLokasi }}">
            <input type="hidden" name="kondisi_input" id="input-kondisi-masuk" value="{{ $defaultKondisi }}">

            <div class="form-header bg-emerald-50 border-b-2 border-emerald-200 p-4 sm:p-6 flex items-center gap-3">
                <div class="icon-box w-12 h-12 rounded-xl bg-emerald-800 text-white flex items-center justify-center font-black flex-shrink-0">
                    <i class="fas fa-arrow-down text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-emerald-950">Catat Bunga Masuk</h2>
                    <p class="text-sm text-slate-600 font-semibold">Cukup isi 3 kolom besar di bawah ini ya Bu/Pak.</p>
                </div>
            </div>

            <div class="form-body p-5 sm:p-7 space-y-6">
                {{-- 1. PILIH BUNGA / KETIK BUNGA BARU --}}
                <div class="form-group">
                    <label for="select-item-masuk" class="text-lg sm:text-xl font-black text-slate-900 mb-2 block">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-700 text-white text-base mr-1">1</span>
                        Pilih Bunga / Ketik Bunga Baru <span class="text-rose-600">*</span>
                    </label>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <div class="min-w-0 flex-1">
                            <select name="id_item" id="select-item-masuk" class="select2 w-full text-lg font-bold">
                                <option value=""></option>
                                @foreach ($items ?? [] as $item)
                                    <option value="{{ $item->id }}" data-stok="{{ $item->total_stok }}" data-satuan="{{ optional($item->satuan)->nama_satuan ?? 'Pcs' }}" {{ old('id_item') == $item->id ? 'selected' : '' }}>
                                        {{ $item->kode_barang }} — {{ $item->nama_barang }} (Stok: {{ $item->total_stok }} {{ optional($item->satuan)->nama_satuan ?? 'Pcs' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" onclick="openQuickItemModal('single')"
                            class="inline-flex h-16 flex-shrink-0 items-center justify-center gap-2 rounded-xl border-2 border-emerald-300 bg-emerald-100 px-5 text-base font-black text-emerald-900 transition hover:bg-emerald-200">
                            <i class="fas fa-plus-circle text-lg"></i> Tambah Item Baru
                        </button>
                    </div>

                    <div id="badge-item-baru"
                        class="mt-3 hidden items-center gap-2 rounded-xl border-2 border-emerald-400 bg-emerald-100 px-4 py-3 text-base font-black text-emerald-900">
                        <span class="text-xl">🌸</span>
                        <span>Bunga Baru (Otomatis Didaftarkan)</span>
                    </div>

                    @error('id_item')
                        <p class="form-error mt-2 text-sm font-bold text-rose-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-sm font-semibold text-slate-500">
                        <i class="fas fa-lightbulb text-amber-500"></i>
                        Belum ada di daftar? Klik <strong class="text-emerald-700">Tambah Item Baru</strong>, atau ketik namanya lalu tekan Enter.
                    </p>
                </div>

                {{-- 2. JUMLAH + 3. HARGA BELI --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="input-jumlah-masuk" class="block text-lg sm:text-xl font-black text-slate-900 mb-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-700 text-white text-base mr-1">2</span>
                            Jumlah Masuk <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" name="jumlah" id="input-jumlah-masuk" min="1" inputmode="numeric"
                            class="w-full rounded-2xl border-2 border-slate-400 focus:border-emerald-700 p-4 text-2xl font-black text-slate-900 bg-white"
                            placeholder="Contoh: 50" value="{{ old('jumlah', 1) }}">
                        @error('jumlah')
                            <p class="text-sm font-bold text-rose-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="input-harga-beli-masuk" class="block text-lg sm:text-xl font-black text-slate-900 mb-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-700 text-white text-base mr-1">3</span>
                            Harga Beli Satuan (Rp) <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" name="harga_beli" id="input-harga-beli-masuk" min="0" step="100" inputmode="numeric"
                            class="w-full rounded-2xl border-2 border-slate-400 focus:border-emerald-700 p-4 text-2xl font-black text-emerald-900 bg-white"
                            placeholder="Contoh: 10000" value="{{ old('harga_beli', 0) }}">
                        @error('harga_beli')
                            <p class="text-sm font-bold text-rose-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PEMASOK: Select2 tags, default Pemasok Umum --}}
                <div class="rounded-2xl border-2 border-emerald-200 bg-emerald-50/60 p-4 sm:p-5">
                    <label for="select-pemasok-masuk" class="block text-lg font-black text-slate-900 mb-2">
                        <i class="fas fa-store text-emerald-700"></i> Pemasok / Supplier
                    </label>
                    <select name="pemasok_id" id="select-pemasok-masuk" class="select2 w-full text-base font-bold">
                        <option value="{{ $pemasokUmumValue }}" {{ $pemasokSelectedValue === $pemasokUmumValue ? 'selected' : '' }}>
                            Pemasok Umum (Default)
                        </option>
                        @foreach ($pemasoks ?? [] as $pem)
                            @continue($pem->nama_pemasok === 'Pemasok Umum')
                            <option value="{{ $pem->id }}" {{ $pemasokSelectedValue === (string) $pem->id ? 'selected' : '' }}>
                                {{ $pem->nama_pemasok }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-sm font-semibold text-slate-600">
                        Sudah terisi <strong>Pemasok Umum</strong>. Kalau pemasok lain, cukup ketik namanya di sini.
                    </p>
                </div>

                {{-- INFO OTOMATIS: tanggal, lokasi, kondisi --}}
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600">
                    <span><i class="fas fa-calendar-day text-emerald-700"></i> Tanggal: <span class="text-emerald-900">Hari ini</span></span>
                    <span><i class="fas fa-warehouse text-emerald-700"></i> Lokasi: <span class="text-emerald-900">{{ $defaultLokasi }}</span></span>
                    <span><i class="fas fa-leaf text-emerald-700"></i> Kondisi: <span class="text-emerald-900">{{ $defaultKondisi }}</span></span>
                    <span class="text-xs font-semibold text-slate-400">(otomatis, tidak perlu diisi)</span>
                </div>
            </div>

            <div class="form-footer bg-emerald-50 border-t-2 border-emerald-200 p-4 sm:p-6 flex flex-col sm:flex-row gap-3">
                <button type="button" id="btn-simpan-lagi"
                    class="w-full sm:flex-1 inline-flex items-center justify-center gap-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-[.99] text-white px-6 py-4 text-lg sm:text-xl font-black shadow-lg shadow-emerald-900/20 transition">
                    <i class="fas fa-save text-xl"></i> SIMPAN &amp; TAMBAH LAGI
                </button>
                <button type="button" id="btn-simpan-selesai"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-3 rounded-2xl bg-sky-700 hover:bg-sky-800 active:scale-[.99] text-white px-6 py-4 text-lg sm:text-xl font-black shadow-lg shadow-sky-900/20 transition">
                    <i class="fas fa-stop-circle text-xl"></i> SIMPAN &amp; SELESAI
                </button>
            </div>
        </form>

        {{-- RINGKASAN RIWAYAT TRANSAKSI MASUK TERAKHIR --}}
        <div class="mt-6 form-card border-2 border-slate-200 shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b-2 border-slate-200 bg-slate-50 p-4 sm:p-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200 text-slate-700">
                        <i class="fas fa-clock-rotate-left text-lg"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Riwayat Masuk Terakhir</h3>
                        <p class="text-xs font-semibold text-slate-500">Otomatis diperbarui setiap kali menyimpan.</p>
                    </div>
                </div>
                <span id="riwayat-masuk-jumlah" class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800">
                    {{ count($riwayatMasuk ?? []) }} transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left">
                    <thead>
                        <tr class="bg-slate-100 text-xs font-black uppercase tracking-wide text-slate-600">
                            <th class="px-4 py-3">Bunga</th>
                            <th class="px-3 py-3 text-center">Jumlah</th>
                            <th class="px-3 py-3 text-right">Harga Satuan</th>
                            <th class="px-3 py-3 text-right">Total</th>
                            <th class="px-3 py-3">Pemasok</th>
                            <th class="px-4 py-3 text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody id="riwayat-masuk-body" class="divide-y divide-slate-200 text-sm font-semibold text-slate-800">
                        @forelse ($riwayatMasuk ?? [] as $riwayat)
                            <tr>
                                <td class="px-4 py-3">
                                    <span class="font-black text-slate-900">{{ $riwayat['nama_barang'] }}</span>
                                    <span class="block text-xs text-slate-500">{{ $riwayat['kode_barang'] }}</span>
                                </td>
                                <td class="px-3 py-3 text-center font-black text-emerald-800">{{ $riwayat['jumlah'] }} {{ $riwayat['satuan'] }}</td>
                                <td class="px-3 py-3 text-right">Rp {{ number_format($riwayat['harga_satuan'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3 text-right font-black text-slate-900">Rp {{ number_format($riwayat['total_harga'], 0, ',', '.') }}</td>
                                <td class="px-3 py-3">{{ $riwayat['pemasok'] }}</td>
                                <td class="px-4 py-3 text-right text-slate-500">{{ $riwayat['tanggal'] }}</td>
                            </tr>
                        @empty
                            <tr id="riwayat-masuk-kosong">
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                    <i class="fas fa-inbox text-2xl text-slate-300"></i>
                                    <p class="mt-2 font-bold">Belum ada transaksi masuk. Yuk catat yang pertama!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 2. MODE INPUT MASSAL (NOTA RESTOCK / SPREADSHEET TABLE)   --}}
    {{-- ======================================================== --}}
    <div id="container-masuk-bulk" class="hidden">
        <form id="form-masuk-bulk" action="{{ route('form.barang-masuk.store-bulk') }}" method="POST" class="form-card border border-slate-200 shadow-sm">
            @csrf

            {{-- HEADER NOTA (BERGAYA NOTA RESTOCK KERTAS) --}}
            <div class="border-b-2 border-emerald-300 bg-[#fbfdfa] p-5 sm:p-6">
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-emerald-200 pb-3">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-200 px-3 py-1 text-xs font-black text-emerald-900 mb-1">
                            <i class="fas fa-receipt"></i> Nota Restock Masuk
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black text-emerald-950">Informasi Nota / Faktur Pembelian</h2>
                    </div>
                    <div class="text-xs text-slate-500 italic">
                        *Cukup isi informasi nota 1x untuk seluruh baris barang di bawah.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    {{-- Tanggal Masuk --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">Tanggal Masuk <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-2.5 text-base font-bold text-slate-900 bg-white">
                    </div>

                    {{-- Pemasok: default Pemasok Umum, bisa ketik nama baru --}}
                    <div>
                        <label for="select-pemasok-bulk" class="block text-xs font-black uppercase text-slate-700 mb-1">Pemasok / Supplier <span class="text-rose-500">*</span></label>
                        <select name="pemasok_id" id="select-pemasok-bulk" class="select2 w-full text-base font-bold">
                            <option value="{{ $pemasokUmumValue }}" {{ $pemasokSelectedValue === $pemasokUmumValue ? 'selected' : '' }}>Pemasok Umum (Default)</option>
                            @foreach ($pemasoks ?? [] as $pem)
                                @continue(strtolower($pem->nama_pemasok) === 'pemasok umum')
                                <option value="{{ $pem->id }}" {{ $pemasokSelectedValue === (string) $pem->id ? 'selected' : '' }}>{{ $pem->nama_pemasok }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lokasi Penyimpanan --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">Lokasi Masuk</label>
                        <select name="id_lokasi" class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-2.5 text-base font-bold text-slate-900 bg-white">
                            @foreach ($lokasis ?? [] as $lok)
                                <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kondisi Barang --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">Kondisi</label>
                        <select name="id_kondisi" class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-2.5 text-base font-bold text-slate-900 bg-white">
                            @foreach ($kondisis ?? [] as $kon)
                                <option value="{{ $kon->id }}">{{ $kon->nama_kondisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Catatan Nota --}}
                <div class="mt-3">
                    <input type="text" name="catatan" placeholder="Nomor faktur / surat jalan / catatan nota (opsional)"
                        class="w-full rounded-xl border border-slate-300 focus:border-emerald-700 p-2 text-sm text-slate-700 bg-white">
                </div>
            </div>

            {{-- TABEL DAFTAR BARANG NOTA --}}
            <div class="p-4 sm:p-6 bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h3 class="text-base sm:text-lg font-black text-emerald-950 flex items-center gap-2">
                        <i class="fas fa-list-ul text-emerald-700"></i> Rincian Barang yang Diterima
                    </h3>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openQuickItemModal('bulk')"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 px-4 py-2.5 text-sm font-black border border-emerald-300 transition shadow-sm">
                            <i class="fas fa-plus-circle text-emerald-700 text-base"></i> + Bunga / Item Baru
                        </button>
                        <button type="button" onclick="addMasukRow()"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white px-4 py-2.5 text-sm font-black shadow transition">
                            <i class="fas fa-plus text-sm"></i> + Tambah Baris
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border-2 border-emerald-200 bg-white shadow-sm">
                    <table class="w-full text-left border-collapse min-w-[860px]" id="table-masuk-bulk">
                        <thead>
                            <tr class="bg-emerald-900 text-white text-sm font-black uppercase tracking-wider">
                                <th class="py-3.5 px-3 text-center w-12">No</th>
                                <th class="py-3.5 px-4 min-w-[320px]">Pilih Bunga / Barang Master <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-3 w-32 text-center">Stok Saat Ini</th>
                                <th class="py-3.5 px-3 w-36 text-center">Qty Masuk <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-48 text-right">Harga Beli Satuan (Rp) <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-48 text-right">Subtotal (Rp)</th>
                                <th class="py-3.5 px-2 text-center w-14">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-masuk-bulk" class="divide-y divide-slate-200 text-slate-800">
                            {{-- Baris diisi oleh JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- PETUNJUK & QUICK BUTTON --}}
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 italic">
                        <i class="fas fa-info-circle text-emerald-600"></i> Pilih barang, isi jumlah dan harga beli satuan. Subtotal otomatis terhitung seketika!
                    </p>
                    <button type="button" onclick="addMasukRow()"
                        class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-5 py-2.5 text-sm font-black text-emerald-900 transition">
                        <i class="fas fa-plus"></i> + Tambah Baris Baru
                    </button>
                </div>
            </div>

            {{-- FOOTER TOTAL & TOMBOL SIMPAN --}}
            <div class="form-footer flex flex-col sm:flex-row items-center justify-between gap-6 p-5 sm:p-6 bg-slate-50 border-t-2 border-slate-200">
                <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-start">
                    <div class="bg-emerald-100/90 border border-emerald-300 px-5 py-3 rounded-2xl shadow-sm">
                        <p class="text-xs font-black uppercase text-emerald-800 tracking-wider">TOTAL BELANJA NOTA</p>
                        <p id="masuk-bulk-grand-total" class="text-2xl sm:text-3xl font-black text-emerald-950">Rp 0</p>
                    </div>
                    <div class="text-sm font-bold text-slate-600">
                        Total Qty: <span id="masuk-bulk-total-qty" class="font-black text-emerald-800 text-xl">0</span> unit
                    </div>
                </div>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white px-8 py-4 text-lg font-black shadow-lg shadow-emerald-900/20 transition-all transform active:scale-95">
                    <i class="fas fa-save text-xl"></i> Simpan Semua Transaksi Masuk
                </button>
            </div>
        </form>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL CEPAT: INPUT ITEM / BUNGA BARU LANGSUNG            --}}
    {{-- ======================================================== --}}
    <div id="modal-quick-item" data-quick-store-url="{{ route('items.quick-store') }}" class="fixed inset-0 z-[99999] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl border-2 border-emerald-200 shadow-2xl w-full max-w-md overflow-hidden transform transition-all animate-fadeIn">
            <div class="bg-emerald-900 text-white p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-emerald-800 flex items-center justify-center text-emerald-200">
                        <i class="fas fa-seedling text-lg"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-black leading-tight">Tambah Bunga / Item Baru</h3>
                        <p class="text-xs text-emerald-200">Daftarkan bunga baru langsung tanpa reload halaman</p>
                    </div>
                </div>
                <button type="button" onclick="closeQuickItemModal()" class="text-emerald-200 hover:text-white text-xl p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="form-quick-item" onsubmit="submitQuickItem(event)" class="p-6 space-y-4">
                <div>
                    <label for="quick-item-nama" class="block text-sm font-black text-slate-800 uppercase mb-1">
                        Nama Bunga / Item <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="quick-item-nama" required
                        placeholder="Contoh: Mawar Avalanche Putih"
                        class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white">
                </div>

                <div>
                    <label for="quick-item-satuan" class="block text-sm font-black text-slate-800 uppercase mb-1">
                        Satuan <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $quickSatuanOptions = collect(['Tangkai', 'Pot', 'Ikat', 'Pcs', 'Buket', 'Box', 'Lusin', 'Batang'])
                            ->merge(($satuans ?? collect())->pluck('nama_satuan'))
                            ->filter()
                            ->unique()
                            ->values();
                        $quickSatuanSelected = ($satuans ?? collect())->first()?->nama_satuan ?? 'Tangkai';
                    @endphp
                    <select id="quick-item-satuan" class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white">
                        @foreach ($quickSatuanOptions as $namaSatuan)
                            <option value="{{ $namaSatuan }}" {{ $namaSatuan === $quickSatuanSelected ? 'selected' : '' }}>
                                {{ $namaSatuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeQuickItemModal()"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 text-sm transition">
                        Batal
                    </button>
                    <button type="submit" id="btn-save-quick-item"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-black text-sm transition shadow-md">
                        <i class="fas fa-check"></i> Simpan & Pilih Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT KHUSUS BARANG MASUK --}}
    <script>
        // Data Master Item di Client
        let ALL_ITEMS_MASUK = @json($itemsJson ?? []);
        let quickItemTarget = { mode: 'single', rowIdx: null };

        // ===== QUICK ITEM MODAL FUNCTIONS =====
        window.openQuickItemModal = function(mode, rowIdx = null, initialName = '') {
            quickItemTarget = { mode, rowIdx };
            const modal = document.getElementById('modal-quick-item');
            const inputNama = document.getElementById('quick-item-nama');
            if (modal) modal.classList.remove('hidden');
            if (inputNama) {
                inputNama.value = initialName || '';
                setTimeout(() => inputNama.focus(), 100);
            }
        };

        window.closeQuickItemModal = function() {
            const modal = document.getElementById('modal-quick-item');
            if (modal) modal.classList.add('hidden');
            const inputNama = document.getElementById('quick-item-nama');
            if (inputNama) inputNama.value = '';
        };

        window.submitQuickItem = async function(e) {
            e.preventDefault();
            const namaItem = document.getElementById('quick-item-nama')?.value.trim();
            const satuan = document.getElementById('quick-item-satuan')?.value || 'Tangkai';
            const btnSave = document.getElementById('btn-save-quick-item');

            if (!namaItem) {
                alert('Nama bunga / item wajib diisi.');
                return;
            }

            if (btnSave) {
                btnSave.disabled = true;
                btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const quickStoreUrl = document.getElementById('modal-quick-item')?.dataset.quickStoreUrl || '/items/quick-store';
                const response = await fetch(quickStoreUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        nama_item: namaItem,
                        satuan: satuan
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.status) {
                    throw new Error(data.message || 'Gagal menyimpan item');
                }

                const newItem = data.item;

                hideNewItemBadge();

                // Tambahkan item ke katalog lokal jika belum ada
                const exists = ALL_ITEMS_MASUK.find(it => it.id === newItem.id);
                if (!exists) {
                    ALL_ITEMS_MASUK.push({
                        id: newItem.id,
                        kode: newItem.kode_barang,
                        nama: newItem.nama_item,
                        stok: 0,
                        satuan: newItem.satuan
                    });
                }

                // Tambahkan option baru ke semua select barang masuk
                const optionHtml = new Option(
                    `${newItem.kode_barang} — ${newItem.nama_item} (Stok: 0 ${newItem.satuan})`,
                    newItem.id,
                    true,
                    true
                );
                optionHtml.dataset.harga = 0;
                optionHtml.dataset.stok = 0;
                optionHtml.dataset.satuan = newItem.satuan;
                optionHtml.dataset.nama = newItem.nama_item;

                // Terapkan ke dropdown target
                if (quickItemTarget.mode === 'single') {
                    const selectSingle = $('#select-item-masuk');
                    if (selectSingle.length) {
                        selectSingle.append($(optionHtml)).val(newItem.id).trigger('change');
                        document.getElementById('input-jumlah-masuk')?.focus();
                    }
                } else if (quickItemTarget.mode === 'bulk' && quickItemTarget.rowIdx !== null) {
                    const rowSelect = $(`#row-masuk-${quickItemTarget.rowIdx} select[name="rows[${quickItemTarget.rowIdx}][item_id]"]`);
                    if (rowSelect.length) {
                        rowSelect.append($(optionHtml)).val(newItem.id).trigger('change');
                    }
                } else {
                    // Jika dibuka dari tombol bulk tanpa baris spesifik, tambahkan baris baru dengan item ini terpilih
                    addMasukRow(newItem.id);
                }

                closeQuickItemModal();

                if (typeof window.toast === 'function') {
                    window.toast(`Bunga "${newItem.nama_item}" berhasil ditambahkan & dipilih!`, 'success');
                }
            } catch (err) {
                console.error('Error quickStoreItem:', err);
                alert('Gagal: ' + err.message);
            } finally {
                if (btnSave) {
                    btnSave.disabled = false;
                    btnSave.innerHTML = '<i class="fas fa-check"></i> Simpan & Pilih Item';
                }
            }
        };

        // ===== SWITCHER MODE MASUK =====
        function switchMasukMode(mode) {
            const btnSingle = document.getElementById('btn-mode-masuk-single');
            const btnBulk   = document.getElementById('btn-mode-masuk-bulk');
            const contSingle = document.getElementById('container-masuk-single');
            const contBulk   = document.getElementById('container-masuk-bulk');

            if (mode === 'bulk') {
                btnBulk.classList.add('bg-white', 'text-emerald-800', 'shadow-sm');
                btnBulk.classList.remove('text-slate-600');
                btnSingle.classList.remove('bg-white', 'text-emerald-800', 'shadow-sm');
                btnSingle.classList.add('text-slate-600');

                contSingle.classList.add('hidden');
                contBulk.classList.remove('hidden');

                if (document.getElementById('tbody-masuk-bulk').children.length === 0) {
                    for (let i = 0; i < 3; i++) addMasukRow();
                } else {
                    if (window.jQuery) {
                        $(window).trigger('resize');
                    }
                }
            } else {
                btnSingle.classList.add('bg-white', 'text-emerald-800', 'shadow-sm');
                btnSingle.classList.remove('text-slate-600');
                btnBulk.classList.remove('bg-white', 'text-emerald-800', 'shadow-sm');
                btnBulk.classList.add('text-slate-600');

                contBulk.classList.add('hidden');
                contSingle.classList.remove('hidden');

                if (window.jQuery) {
                    $(window).trigger('resize');
                }
            }
        }

        let masukRowCounter = 0;

        function initMasukRowSelect2(idx) {
            const selectEl = document.querySelector(`#row-masuk-${idx} select[name="rows[${idx}][item_id]"]`);
            if (!selectEl || !window.jQuery || !$.fn.select2) return;

            $(selectEl).select2({
                placeholder: '🔍 Cari / Pilih Bunga Master...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('body'),
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return {
                        id: 'NEW:' + term,
                        text: '➕ Tambah Baru: "' + term + '"',
                        newTag: true,
                        term: term
                    };
                },
                language: {
                    noResults: function() { return 'Bunga tidak ditemukan. Tekan Enter untuk menambah baru.'; },
                    searching: function() { return 'Mencari...'; }
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                if (data.newTag || (String(data.id).startsWith('NEW:'))) {
                    // Bersihkan pilihan sementara
                    $(selectEl).val('').trigger('change');
                    const flowerName = data.term || data.id.replace('NEW:', '');
                    openQuickItemModal('bulk', idx, flowerName);
                    return;
                }
                onMasukItemChange(idx, this);
            }).on('select2:unselect', function() {
                onMasukItemChange(idx, this);
            });
        }

        function addMasukRow(selectedItemId = null) {
            const tbody = document.getElementById('tbody-masuk-bulk');
            masukRowCounter++;
            const idx = masukRowCounter;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/40 transition-colors';
            tr.id = `row-masuk-${idx}`;

            let itemOptionsHtml = '<option value=""></option>';
            ALL_ITEMS_MASUK.forEach(it => {
                const isSelected = selectedItemId && selectedItemId === it.id ? 'selected' : '';
                const stokText = it.stok !== undefined ? ` (Stok: ${it.stok} ${it.satuan || ''})` : '';
                itemOptionsHtml += `<option value="${it.id}" ${isSelected}
                    data-stok="${it.stok}"
                    data-nama="${it.nama}"
                    data-satuan="${it.satuan}">
                    ${it.kode} — ${it.nama}${stokText}
                </option>`;
            });

            tr.innerHTML = `
                <td class="py-3 px-3 text-center font-black text-slate-400 row-number text-base"></td>
                <td class="py-3 px-4" style="min-width:320px">
                    <div class="flex items-center gap-1.5 w-full">
                        <select name="rows[${idx}][item_id]" required
                            class="w-full select-masuk-item text-base">
                            ${itemOptionsHtml}
                        </select>
                        <button type="button" onclick="openQuickItemModal('bulk', ${idx})"
                            class="flex-shrink-0 w-8 h-8 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 flex items-center justify-center text-sm font-black transition"
                            title="Tambah Bunga Baru untuk Baris Ini">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </td>
                <td class="py-3 px-3 text-center">
                    <span id="stok-masuk-badge-${idx}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-slate-100 text-slate-700" data-stok="0">
                        -
                    </span>
                </td>
                <td class="py-3 px-3 text-center">
                    <input type="number" name="rows[${idx}][jumlah]" min="1" value="1" required
                        oninput="calcMasukRow(${idx})"
                        class="w-28 mx-auto text-center rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-2 text-lg font-black text-slate-900 bg-white">
                </td>
                <td class="py-3 px-4 text-right">
                    <input type="number" name="rows[${idx}][harga_satuan]" min="0" step="100" value="0" required
                        oninput="calcMasukRow(${idx})"
                        class="w-full text-right rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-2 text-lg font-black text-emerald-900 bg-white">
                </td>
                <td class="py-3 px-4 text-right">
                    <span id="subtotal-masuk-${idx}" class="text-base font-black text-emerald-950 subtotal-masuk-val">Rp 0</span>
                </td>
                <td class="py-3 px-2 text-center">
                    <button type="button" onclick="removeMasukRow('${idx}')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-rose-500 hover:bg-rose-100 hover:text-rose-700 transition"
                        title="Hapus Baris Ini">
                        <i class="fas fa-trash-alt text-base"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            initMasukRowSelect2(idx);
            reindexMasukRows();

            if (selectedItemId) {
                const sel = document.querySelector(`#row-masuk-${idx} select`);
                if (sel) onMasukItemChange(idx, sel);
            }

            calcMasukGrandTotal();
        }

        function removeMasukRow(idx) {
            const row = document.getElementById(`row-masuk-${idx}`);
            if (row) {
                const selectEl = $(row).find('select');
                if (selectEl.length && selectEl.data('select2')) {
                    selectEl.select2('destroy');
                }
                row.remove();
                reindexMasukRows();
                calcMasukGrandTotal();
            }
        }

        function reindexMasukRows() {
            const rows = document.querySelectorAll('#tbody-masuk-bulk tr');
            rows.forEach((r, i) => {
                const numCell = r.querySelector('.row-number');
                if (numCell) numCell.textContent = i + 1;
            });
        }

        function onMasukItemChange(idx, selectEl) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const stok = selectedOpt && selectedOpt.dataset.stok !== undefined ? selectedOpt.dataset.stok : null;
            const satuan = selectedOpt && selectedOpt.dataset.satuan ? selectedOpt.dataset.satuan : '';

            const row = document.getElementById(`row-masuk-${idx}`);
            if (!row) return;

            const stokBadge = document.getElementById(`stok-masuk-badge-${idx}`);
            if (stokBadge) {
                if (selectEl.value && stok !== null) {
                    stokBadge.textContent = `${stok} ${satuan}`;
                    stokBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-emerald-100 text-emerald-800';
                } else {
                    stokBadge.textContent = '-';
                    stokBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-slate-100 text-slate-700';
                }
            }

            calcMasukRow(idx);
        }

        function calcMasukRow(idx) {
            const row = document.getElementById(`row-masuk-${idx}`);
            if (!row) return;

            const qtyInput = row.querySelector(`input[name="rows[${idx}][jumlah]"]`);
            const hargaInput = row.querySelector(`input[name="rows[${idx}][harga_satuan]"]`);
            const subtotalSpan = document.getElementById(`subtotal-masuk-${idx}`);

            const qty = qtyInput ? Math.max(0, parseInt(qtyInput.value) || 0) : 0;
            const harga = hargaInput ? Math.max(0, parseFloat(hargaInput.value) || 0) : 0;
            const subtotal = qty * harga;

            if (subtotalSpan) {
                subtotalSpan.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
                subtotalSpan.dataset.amount = subtotal;
            }

            calcMasukGrandTotal();
        }

        function calcMasukGrandTotal() {
            const rows = document.querySelectorAll('#tbody-masuk-bulk tr');
            let grandTotal = 0;
            let totalQty = 0;

            rows.forEach(r => {
                const qtyInput = r.querySelector('input[name*="[jumlah]"]');
                const hargaInput = r.querySelector('input[name*="[harga_satuan]"]');
                const itemSelect = r.querySelector('select[name*="[item_id]"]');

                if (itemSelect && itemSelect.value) {
                    const qty = qtyInput ? (parseInt(qtyInput.value) || 0) : 0;
                    const harga = hargaInput ? (parseFloat(hargaInput.value) || 0) : 0;
                    grandTotal += (qty * harga);
                    totalQty += qty;
                }
            });

            const display = document.getElementById('masuk-bulk-grand-total');
            if (display) {
                display.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
            }

            const badge = document.getElementById('masuk-bulk-total-qty');
            if (badge) {
                badge.textContent = totalQty;
            }
        }

        // Form submit validation untuk Masuk Massal
        document.getElementById('form-masuk-bulk')?.addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('#tbody-masuk-bulk tr');
            let validCount = 0;
            rows.forEach((r) => {
                const sel = r.querySelector('select[name*="[item_id]"]');
                const qty = r.querySelector('input[name*="[jumlah]"]');
                if (sel && sel.value && parseInt(qty?.value || 0) > 0) {
                    validCount++;
                }
            });

            if (validCount === 0) {
                e.preventDefault();
                alert('Mohon pilih minimal satu barang dan masukkan jumlah masuk yang valid.');
                return false;
            }
        });

        // Inisialisasi saat Document Ready
        $(document).ready(function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const escapeHtml = (value = '') => String(value).replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[char]));

            const formatRupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(value) || 0);

            function showBigToast(message, type = 'success') {
                const palette = type === 'success'
                    ? 'bg-emerald-500 text-white ring-emerald-200'
                    : 'bg-rose-500 text-white ring-rose-200';

                const node = document.createElement('div');
                node.className = `fixed top-5 right-5 z-[999999] max-w-sm rounded-2xl px-6 py-4 text-lg font-black shadow-2xl ring-4 ${palette} transition-all duration-300 -translate-y-2 opacity-0`;
                node.setAttribute('role', 'status');
                node.textContent = message;
                document.body.appendChild(node);

                requestAnimationFrame(() => node.classList.remove('-translate-y-2', 'opacity-0'));
                setTimeout(() => {
                    node.classList.add('-translate-y-2', 'opacity-0');
                    setTimeout(() => node.remove(), 300);
                }, 3200);
            }

            function showNewItemBadge(name = '') {
                const badge = document.getElementById('badge-item-baru');
                if (!badge) return;
                badge.innerHTML = `<span class="text-xl">🌸</span><span>Bunga Baru${name ? ': &ldquo;' + escapeHtml(name) + '&rdquo;' : ''} (Otomatis Didaftarkan)</span>`;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            }

            function hideNewItemBadge() {
                const badge = document.getElementById('badge-item-baru');
                if (!badge) return;
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }

            function focusItemSelect(openDropdown = false) {
                setTimeout(() => {
                    if (window.jQuery && $.fn.select2) {
                        if (openDropdown) $('#select-item-masuk').select2('open');
                        $('#select-item-masuk').trigger('focus');
                        setTimeout(() => {
                            document.querySelector('.select2-container--open .select2-search__field')?.focus();
                        }, 70);
                    } else {
                        document.getElementById('select-item-masuk')?.focus();
                    }
                }, 80);
            }

            function addItemToCatalog(item) {
                if (!item || !item.id) return;

                const itemId = String(item.id);
                const nama = item.nama || item.nama_item || 'Bunga Baru';
                const kode = item.kode_barang || '';
                const satuan = item.satuan || 'Tangkai';
                const stok = item.stok ?? 0;
                if (!ALL_ITEMS_MASUK.find((it) => String(it.id) === itemId)) {
                    ALL_ITEMS_MASUK.push({
                        id: item.id,
                        kode: kode,
                        nama: nama,
                        stok: stok,
                        satuan: satuan
                    });
                }

                const selects = document.querySelectorAll('#select-item-masuk, select.select-masuk-item');
                selects.forEach((select) => {
                    if (select.querySelector(`option[value="${itemId}"]`)) return;

                    const option = new Option(
                        `${kode}${kode ? ' — ' : ''}${nama} (Stok: ${stok} ${satuan})`,
                        itemId,
                        false,
                        false
                    );
                    option.dataset.stok = stok;
                    option.dataset.satuan = satuan;
                    option.dataset.nama = nama;
                    select.appendChild(option);
                });
            }

            function renderRiwayat(rows) {
                const tbody = document.getElementById('riwayat-masuk-body');
                if (!tbody) return;

                const badge = document.getElementById('riwayat-masuk-jumlah');

                if (!Array.isArray(rows) || rows.length === 0) {
                    tbody.innerHTML = `
                        <tr id="riwayat-masuk-kosong">
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                <i class="fas fa-inbox text-2xl text-slate-300"></i>
                                <p class="mt-2 font-bold">Belum ada transaksi masuk. Yuk catat yang pertama!</p>
                            </td>
                        </tr>`;
                    if (badge) badge.textContent = '0 transaksi';
                    return;
                }

                tbody.innerHTML = rows.map((row) => `
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-black text-slate-900">${escapeHtml(row.nama_barang)}</span>
                            <span class="block text-xs text-slate-500">${escapeHtml(row.kode_barang)}</span>
                        </td>
                        <td class="px-3 py-3 text-center font-black text-emerald-800">${escapeHtml(row.jumlah)} ${escapeHtml(row.satuan)}</td>
                        <td class="px-3 py-3 text-right">${formatRupiah(row.harga_satuan)}</td>
                        <td class="px-3 py-3 text-right font-black text-slate-900">${formatRupiah(row.total_harga)}</td>
                        <td class="px-3 py-3">${escapeHtml(row.pemasok)}</td>
                        <td class="px-4 py-3 text-right text-slate-500">${escapeHtml(row.tanggal)}</td>
                    </tr>
                `).join('');

                if (badge) badge.textContent = `${rows.length} transaksi`;
            }

            function resetFormMasuk() {
                const form = document.getElementById('form-barang-masuk');
                if (!form) return;

                if (window.jQuery && $.fn.select2) {
                    $('#select-item-masuk').val(null).trigger('change');
                } else {
                    const select = form.querySelector('[name="id_item"]');
                    if (select) select.value = '';
                }

                document.getElementById('input-jumlah-masuk').value = '';
                document.getElementById('input-harga-beli-masuk').value = '';
                hideNewItemBadge();
                focusItemSelect(true);
            }

            const saveButtons = ['btn-simpan-lagi', 'btn-simpan-selesai']
                .map((id) => document.getElementById(id))
                .filter(Boolean);

            function setBusy(busy, activeId = null) {
                saveButtons.forEach((button) => {
                    button.disabled = busy;

                    if (busy && button.id === activeId) {
                        button.dataset.originalHtml = button.innerHTML;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                    } else if (!busy && button.dataset.originalHtml) {
                        button.innerHTML = button.dataset.originalHtml;
                        delete button.dataset.originalHtml;
                    }
                });
            }

            async function submitMasuk(finishAfter = false) {
                const form = document.getElementById('form-barang-masuk');
                if (!form) return;

                const itemVal = $('#select-item-masuk').val();
                const jumlahInput = document.getElementById('input-jumlah-masuk');
                const hargaInput = document.getElementById('input-harga-beli-masuk');
                const jumlah = parseInt(jumlahInput?.value, 10);
                const harga = parseFloat(hargaInput?.value);

                if (!itemVal) {
                    showBigToast('Mohon pilih atau ketik nama bunga terlebih dahulu ya Bu/Pak.', 'error');
                    focusItemSelect(true);
                    return;
                }

                if (!jumlahInput?.value || Number.isNaN(jumlah) || jumlah < 1) {
                    showBigToast('Harap isi jumlah bunga terlebih dahulu ya Bu/Pak.', 'error');
                    jumlahInput?.focus();
                    return;
                }

                if (hargaInput?.value === '' || Number.isNaN(harga) || harga < 0) {
                    showBigToast('Harap isi harga beli satuan terlebih dahulu ya Bu/Pak.', 'error');
                    hargaInput?.focus();
                    return;
                }

                const activeId = finishAfter ? 'btn-simpan-selesai' : 'btn-simpan-lagi';
                setBusy(true, activeId);

                try {
                    const formData = new FormData(form);
                    formData.set('id_item', itemVal);
                    formData.set('jumlah', jumlah);
                    formData.set('harga_beli', harga);

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json().catch(() => null);

                    if (!response.ok || !(data && (data.status || data.success))) {
                        const firstError = data?.errors ? Object.values(data.errors)?.[0]?.[0] : null;
                        throw new Error(data?.message || firstError || 'Maaf, data belum bisa disimpan. Coba lagi ya Bu/Pak.');
                    }

                    if (data.item) addItemToCatalog(data.item);
                    if (Array.isArray(data.riwayat)) renderRiwayat(data.riwayat);

                    showBigToast(data.message || '✅ Berhasil ditambahkan!');

                    if (finishAfter) {
                        setTimeout(() => {
                            window.location.href = form.dataset.finishUrl;
                        }, 900);
                    } else {
                        resetFormMasuk();
                    }
                } catch (error) {
                    showBigToast(error.message || 'Maaf, data belum bisa disimpan. Coba lagi ya Bu/Pak.', 'error');
                } finally {
                    setBusy(false, activeId);
                }
            }

            // Select2 Bunga: pencarian + pembuatan bunga baru langsung (inline)
            if ($.fn.select2) {
                $('#select-item-masuk').select2({
                    placeholder: '🔍 Ketik nama bunga di sini...',
                    allowClear: true,
                    width: '100%',
                    tags: true,
                    createTag: function (params) {
                        const term = $.trim(params.term);
                        if (term === '') return null;
                        return {
                            id: 'NEW:' + term,
                            text: term,
                            newTag: true,
                            term: term
                        };
                    },
                    templateResult: function (data) {
                        if (data.newTag) {
                            return $('<span class="font-bold text-emerald-800"><i class="fas fa-plus-circle mr-1"></i> Tambah Baru: <strong>' + escapeHtml(data.term) + '</strong></span>');
                        }
                        return data.text;
                    },
                    language: {
                        noResults: function () { return 'Bunga belum ada. Ketik namanya lalu tekan Enter untuk mendaftarkan otomatis.'; },
                        searching: function () { return 'Mencari...'; }
                    }
                }).on('select2:select', function (e) {
                    const data = e.params.data;

                    if (data.newTag || String(data.id).startsWith('NEW:')) {
                        const namaBaru = data.term || String(data.id).replace('NEW:', '');
                        $(this).val(null).trigger('change');
                        hideNewItemBadge();
                        openQuickItemModal('single', null, namaBaru);
                        return;
                    }

                    hideNewItemBadge();

                }).on('select2:unselect select2:clear', function () {
                    hideNewItemBadge();
                }).on('select2:open', function () {
                    setTimeout(function () {
                        document.querySelector('.select2-container--open .select2-search__field')?.focus();
                    }, 60);
                });

                // Select2 Pemasok: default Pemasok Umum + bisa ketik nama baru
                $('#select-pemasok-masuk').select2({
                    placeholder: 'Pilih atau ketik nama pemasok...',
                    width: '100%',
                    tags: true,
                    allowClear: false,
                    createTag: function (params) {
                        const term = $.trim(params.term);
                        if (term === '') return null;
                        return {
                            id: term,
                            text: term,
                            newTag: true,
                            term: term
                        };
                    },
                    templateResult: function (data) {
                        if (data.newTag) {
                            return $('<span class="font-bold text-emerald-800"><i class="fas fa-plus-circle mr-1"></i> Pemasok Baru: <strong>' + escapeHtml(data.term) + '</strong></span>');
                        }
                        return data.text;
                    },
                    language: {
                        noResults: function () { return 'Pemasok belum ada. Ketik namanya lalu tekan Enter.'; },
                        searching: function () { return 'Mencari...'; }
                    }
                });

                // Pemasok pada mode input massal (nota restock)
                $('#select-pemasok-bulk').select2({
                    placeholder: 'Pilih atau ketik nama pemasok...',
                    width: '100%',
                    tags: true,
                    allowClear: false,
                    createTag: function (params) {
                        const term = $.trim(params.term);
                        if (term === '') return null;
                        return {
                            id: term,
                            text: term,
                            newTag: true,
                            term: term
                        };
                    },
                    templateResult: function (data) {
                        if (data.newTag) {
                            return $('<span class="font-bold text-emerald-800"><i class="fas fa-plus-circle mr-1"></i> Pemasok Baru: <strong>' + escapeHtml(data.term) + '</strong></span>');
                        }
                        return data.text;
                    },
                    language: {
                        noResults: function () { return 'Pemasok belum ada. Ketik namanya lalu tekan Enter.'; },
                        searching: function () { return 'Mencari...'; }
                    }
                });
            }

            saveButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    submitMasuk(button.id === 'btn-simpan-selesai');
                });
            });

            document.getElementById('form-barang-masuk')?.addEventListener('submit', function (event) {
                event.preventDefault();
                submitMasuk(false);
            });

            // Voice Assistant Handler Satuan
            const btnVoice = document.getElementById('btn-voice-masuk');
            const voiceIcon = document.getElementById('voice-icon-masuk');
            const voiceStatus = document.getElementById('voice-status-masuk');
            const transcriptBox = document.getElementById('voice-transcript-masuk');
            const transcriptText = document.getElementById('voice-text-masuk');

            if (btnVoice) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                let mediaRecorder = null;
                let audioChunks = [];
                let isRecording = false;

                function updateVoiceUI(status, message = '') {
                    btnVoice.disabled = false;
                    btnVoice.classList.remove('recording', 'processing');

                    if (status === 'recording') {
                        btnVoice.classList.add('recording');
                        if (voiceIcon) voiceIcon.className = 'fas fa-stop';
                        if (voiceStatus) voiceStatus.textContent = 'Rekam... (Stop)';
                        if (transcriptBox) {
                            transcriptBox.classList.add('show');
                            transcriptBox.style.display = 'block';
                        }
                        if (transcriptText) transcriptText.textContent = 'Sedang merekam suara Anda...';
                    } else if (status === 'processing') {
                        btnVoice.disabled = true;
                        btnVoice.classList.add('processing');
                        if (voiceIcon) voiceIcon.className = 'fas fa-spinner fa-spin';
                        if (voiceStatus) voiceStatus.textContent = message || 'AI Mendengarkan...';
                        if (transcriptText) transcriptText.textContent = 'Mengirim rekaman suara ke AI...';
                    } else {
                        btnVoice.classList.remove('recording', 'processing');
                        if (voiceIcon) voiceIcon.className = 'fas fa-microphone';
                        if (voiceStatus) voiceStatus.textContent = message || 'Mulai Bicara';
                    }
                }

                async function startRecording() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        mediaRecorder = new MediaRecorder(stream);
                        audioChunks = [];

                        mediaRecorder.ondataavailable = event => {
                            if (event.data.size > 0) audioChunks.push(event.data);
                        };

                        mediaRecorder.onstop = async () => {
                            stream.getTracks().forEach(track => track.stop());
                            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                            await sendAudioToAI(audioBlob);
                        };

                        mediaRecorder.start();
                        isRecording = true;
                        updateVoiceUI('recording');

                    } catch (err) {
                        console.error('Microphone error:', err);
                        updateVoiceUI('idle', 'Akses Mic Ditolak');
                        alert('Gagal mengakses mikrofon. Pastikan izin mikrofon telah diberikan.');
                    }
                }

                function stopRecording() {
                    if (mediaRecorder && isRecording) {
                        mediaRecorder.stop();
                        isRecording = false;
                        updateVoiceUI('processing', 'Menganalisis Suara...');
                    }
                }

                async function sendAudioToAI(blob) {
                    const formData = new FormData();
                    formData.append('audio', blob, 'recording_masuk.webm');

                    try {
                        const response = await fetch('{{ route('form.barang-masuk.parse-voice') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal memproses AI');
                        }

                        if (result.transcript_raw && transcriptText) {
                            transcriptText.textContent = `"${result.transcript_raw}"`;
                        }

                        applyAiParsedData(result.data);
                        updateVoiceUI('idle', 'Selesai! Form Terisi');

                        if (typeof window.toast === 'function') {
                            window.toast('Form berhasil terisi dari suara!', 'success');
                        }

                    } catch (err) {
                        console.error('Audio processing error:', err);
                        updateVoiceUI('idle', 'Gagal Memproses');
                        if (transcriptText) transcriptText.textContent = 'Error: ' + err.message;
                    }
                }

                btnVoice.addEventListener('click', function() {
                    if (!isRecording) {
                        startRecording();
                    } else {
                        stopRecording();
                    }
                });
            }

            function applyAiParsedData(data) {
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el && val !== null && val !== undefined) el.value = val;
                };

                if (data.id_item && $.fn.select2) {
                    // Item sudah ada di katalog
                    $('#select-item-masuk').val(data.id_item).trigger('change');
                    hideNewItemBadge();
                } else if (data.nama_barang && !data.id_item) {
                    // Item baru: pilih tag "NEW:" agar dibuat otomatis saat disimpan
                    const select = $('#select-item-masuk');
                    const value = 'NEW:' + data.nama_barang;
                    const optionExists = select.find('option').filter(function () {
                        return this.value === value;
                    }).length > 0;

                    if (!optionExists) {
                        select.append(new Option(data.nama_barang, value, true, true));
                    }

                    select.val(value).trigger('change');
                    showNewItemBadge(data.nama_barang);
                }

                setVal('input-jumlah-masuk', data.jumlah);
                setVal('input-harga-beli-masuk', data.harga_beli);
                setVal('input-tgl-masuk', data.tgl_masuk || data.tanggal_masuk);
                setVal('input-tgl-kadaluarsa', data.tgl_kadaluarsa || data.tanggal_kadaluarsa);
                setVal('input-lokasi-masuk', data.lokasi_input);
                setVal('input-kondisi-masuk', data.kondisi_input);

                if (data.pemasok_input && $.fn.select2) {
                    const select = $('#select-pemasok-masuk');
                    const namaPemasok = String(data.pemasok_input).trim();
                    let value = namaPemasok;

                    select.find('option').each(function () {
                        if ($(this).text().trim().toLowerCase() === namaPemasok.toLowerCase()) {
                            value = $(this).val();
                        }
                    });

                    const optionExists = select.find('option').filter(function () {
                        return this.value === value;
                    }).length > 0;

                    if (!optionExists) {
                        select.append(new Option(namaPemasok, namaPemasok, true, true));
                    }

                    select.val(value).trigger('change');
                }
            }
        });
    </script>

</section>
