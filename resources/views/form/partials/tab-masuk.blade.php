<section id="masuk" class="tab-content" role="tabpanel">

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

        {{-- Form Transaksi Barang Masuk Satuan --}}
        <form id="form-barang-masuk" action="{{ route('form.barang-masuk.store') }}" method="POST" class="form-card shadow-sm border border-slate-200">
            @csrf

            <div class="form-header bg-slate-50 border-b border-slate-200 p-4 sm:p-5 flex items-center gap-3">
                <div class="icon-box w-10 h-10 rounded-xl bg-emerald-800 text-white flex items-center justify-center font-black">
                    <i class="fas fa-arrow-down text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black text-emerald-950">Transaksi Barang Masuk (Satuan)</h2>
                    <p class="text-xs text-slate-500">Catat penerimaan item barang ke dalam inventori toko.</p>
                </div>
            </div>

            <div class="form-body p-5 sm:p-6 space-y-5">
                {{-- PILIH BARANG MASTER + TOMBOL ITEM BARU --}}
                <div class="form-group">
                    <div class="flex items-center justify-between mb-2">
                        <label for="select-item-masuk" class="text-base font-black text-slate-800">
                            Pilih Bunga / Barang Master <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="openQuickItemModal('single')"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-900 px-3 py-1.5 text-sm font-black transition shadow-sm border border-emerald-300">
                            <i class="fas fa-plus-circle text-emerald-700 text-base"></i> + Bunga / Item Baru
                        </button>
                    </div>

                    <select name="id_item" id="select-item-masuk" required class="select2 w-full text-base font-bold">
                        <option value="">-- Cari atau Ketik Nama Bunga --</option>
                        @foreach ($items ?? [] as $item)
                            <option value="{{ $item->id }}" data-harga="{{ $item->harga_dasar }}" data-stok="{{ $item->total_stok }}" data-satuan="{{ optional($item->satuan)->nama_satuan ?? 'Pcs' }}" {{ old('id_item') == $item->id ? 'selected' : '' }}>
                                {{ $item->kode_barang }} — {{ $item->nama_barang }} (Stok: {{ $item->total_stok }} {{ optional($item->satuan)->nama_satuan ?? 'Pcs' }})
                            </option>
                        @endforeach
                    </select>

                    @error('id_item')
                        <p class="form-error mt-1 text-sm font-bold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- JUMLAH + HARGA BELI --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="input-jumlah-masuk" class="block text-sm font-black uppercase text-slate-700 mb-1">
                            Jumlah Masuk <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="jumlah" id="input-jumlah-masuk" min="1" required
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-lg font-black text-slate-900 bg-white"
                            placeholder="Contoh: 50" value="{{ old('jumlah', 1) }}">
                        @error('jumlah')
                            <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="input-harga-beli-masuk" class="block text-sm font-black uppercase text-slate-700 mb-1">
                            Harga Beli Satuan (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="harga_beli" id="input-harga-beli-masuk" min="0" step="100" required
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-lg font-black text-emerald-900 bg-white"
                            placeholder="0" value="{{ old('harga_beli', 0) }}">
                        @error('harga_beli')
                            <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- TANGGAL MASUK + TANGGAL KADALUARSA --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="input-tgl-masuk" class="block text-sm font-black uppercase text-slate-700 mb-1">
                            Tanggal Masuk <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tgl_masuk" id="input-tgl-masuk" required
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white"
                            value="{{ old('tgl_masuk', date('Y-m-d')) }}">
                        @error('tgl_masuk')
                            <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="input-tgl-kadaluarsa" class="block text-sm font-black uppercase text-slate-700 mb-1">
                            Tanggal Kadaluarsa <span class="text-xs text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="date" name="tgl_kadaluarsa" id="input-tgl-kadaluarsa"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white"
                            value="{{ old('tgl_kadaluarsa') }}">
                        @error('tgl_kadaluarsa')
                            <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PEMASOK + LOKASI + KONDISI --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- 1 Inputan Simpel Pemasok --}}
                    <div>
                        <label for="input-pemasok-masuk" class="block text-sm font-black uppercase text-slate-700 mb-1">
                            Pemasok / Supplier <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="pemasok_input" id="input-pemasok-masuk" list="datalist-pemasok-single" required
                            placeholder="Ketik / pilih pemasok"
                            value="{{ old('pemasok_input', $pemasoks->first()?->nama_pemasok ?? 'Pemasok Umum') }}"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white"
                            autocomplete="off">
                        <datalist id="datalist-pemasok-single">
                            @foreach ($pemasoks ?? [] as $pem)
                                <option value="{{ $pem->nama_pemasok }}">
                            @endforeach
                        </datalist>
                        @error('pemasok_input')
                            <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi Penyimpanan --}}
                    <div>
                        <label for="input-lokasi-masuk" class="block text-sm font-black uppercase text-slate-700 mb-1">
                            Lokasi Simpan
                        </label>
                        <select name="lokasi_input" id="input-lokasi-masuk"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white">
                            @foreach ($lokasis ?? [] as $lok)
                                <option value="{{ $lok->nama_lokasi }}" {{ (old('lokasi_input') == $lok->nama_lokasi || $lok->nama_lokasi == 'Gudang Utama') ? 'selected' : '' }}>
                                    {{ $lok->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kondisi Barang --}}
                    <div>
                        <label for="input-kondisi-masuk" class="block text-sm font-black uppercase text-slate-700 mb-1">
                            Kondisi Barang
                        </label>
                        <select name="kondisi_input" id="input-kondisi-masuk"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white">
                            @foreach ($kondisis ?? [] as $kon)
                                <option value="{{ $kon->nama_kondisi }}" {{ (old('kondisi_input') == $kon->nama_kondisi || $kon->nama_kondisi == 'Segar') ? 'selected' : '' }}>
                                    {{ $kon->nama_kondisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- CATATAN TRANSAKSI --}}
                <div>
                    <label for="input-catatan-masuk" class="block text-sm font-black uppercase text-slate-700 mb-1">
                        Catatan Transaksi <span class="text-xs text-slate-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <input type="text" name="catatan" id="input-catatan-masuk"
                        placeholder="Catatan tambahan faktur atau surat jalan..."
                        value="{{ old('catatan') }}"
                        class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base text-slate-800 bg-white">
                </div>
            </div>

            <div class="form-footer bg-slate-50 border-t border-slate-200 p-4 sm:p-5 flex justify-end">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white px-8 py-3.5 text-base font-black shadow-md transition">
                    <i class="fas fa-save text-lg"></i> Simpan Transaksi Masuk
                </button>
            </div>
        </form>
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

                    {{-- Pemasok (1 inputan simpel) --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-700 mb-1">Pemasok / Supplier <span class="text-rose-500">*</span></label>
                        <input type="text" name="pemasok_input" list="datalist-pemasok-bulk" required
                            placeholder="-- Pilih / Ketik Pemasok --"
                            value="{{ $pemasoks->first()?->nama_pemasok ?? 'Pemasok Umum' }}"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-2.5 text-base font-bold text-slate-900 bg-white">
                        <datalist id="datalist-pemasok-bulk">
                            @foreach ($pemasoks ?? [] as $pem)
                                <option value="{{ $pem->nama_pemasok }}">
                            @endforeach
                        </datalist>
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
    <div id="modal-quick-item" class="fixed inset-0 z-[99999] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
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
                    <select id="quick-item-satuan" class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-700 p-3 text-base font-bold text-slate-900 bg-white">
                        <option value="Tangkai" selected>Tangkai</option>
                        <option value="Pot">Pot</option>
                        <option value="Ikat">Ikat</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Buket">Buket</option>
                        <option value="Box">Box</option>
                        <option value="Lusin">Lusin</option>
                        <option value="Batang">Batang</option>
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
                const response = await fetch('/items/quick-store', {
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

                // Tambahkan item ke katalog lokal jika belum ada
                const exists = ALL_ITEMS_MASUK.find(it => it.id === newItem.id);
                if (!exists) {
                    ALL_ITEMS_MASUK.push({
                        id: newItem.id,
                        kode: newItem.kode_barang,
                        nama: newItem.nama_item,
                        harga_dasar: 0,
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
                    data-harga="${it.harga_dasar}"
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
            const defaultHarga = selectedOpt && selectedOpt.dataset.harga ? parseFloat(selectedOpt.dataset.harga) : 0;
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

            const hargaInput = row.querySelector(`input[name="rows[${idx}][harga_satuan]"]`);
            if (hargaInput && (parseFloat(hargaInput.value) === 0 || !hargaInput.value)) {
                hargaInput.value = defaultHarga;
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
            // Select2 untuk Single Mode dengan Tagging Cepat
            if ($.fn.select2) {
                $('#select-item-masuk').select2({
                    placeholder: '🔍 Cari atau ketik nama bunga...',
                    allowClear: true,
                    width: '100%',
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
                    if (data.newTag || String(data.id).startsWith('NEW:')) {
                        $('#select-item-masuk').val('').trigger('change');
                        const flowerName = data.term || data.id.replace('NEW:', '');
                        openQuickItemModal('single', null, flowerName);
                        return;
                    }

                    const opt = this.options[this.selectedIndex];
                    const hargaBeliInput = document.getElementById('input-harga-beli-masuk');
                    if (opt && opt.dataset.harga && hargaBeliInput && (!hargaBeliInput.value || parseFloat(hargaBeliInput.value) === 0)) {
                        hargaBeliInput.value = opt.dataset.harga;
                    }
                });
            }

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
                    $('#select-item-masuk').val(data.id_item).trigger('change');
                } else if (data.nama_barang && !data.id_item) {
                    // Jika barang tidak ditemukan di master, buka modal quick-add
                    openQuickItemModal('single', null, data.nama_barang);
                }

                setVal('input-jumlah-masuk', data.jumlah);
                setVal('input-harga-beli-masuk', data.harga_beli);
                setVal('input-tgl-masuk', data.tgl_masuk || data.tanggal_masuk);
                setVal('input-tgl-kadaluarsa', data.tgl_kadaluarsa || data.tanggal_kadaluarsa);
                setVal('input-pemasok-masuk', data.pemasok_input);
                setVal('input-lokasi-masuk', data.lokasi_input);
                setVal('input-kondisi-masuk', data.kondisi_input);
                setVal('input-catatan-masuk', data.catatan);
            }
        });
    </script>

</section>
