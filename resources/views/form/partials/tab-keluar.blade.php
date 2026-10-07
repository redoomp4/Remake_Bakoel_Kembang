<section id="keluar" class="tab-content" role="tabpanel">

    {{-- SWITCHER MODE INPUT (SATUAN vs MASSAL) --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border-2 border-emerald-200 bg-white p-3 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 font-black">
                <i class="fas fa-cash-register text-lg"></i>
            </span>
            <div>
                <h3 class="text-base font-black text-emerald-950">Mode Transaksi Barang Keluar</h3>
                <p class="text-xs text-slate-500">Pilih ingin catat 1 barang atau catat nota penjualan kasir banyak barang sekaligus</p>
            </div>
        </div>
        <div class="flex rounded-xl bg-slate-100 p-1">
            <button type="button" id="btn-mode-keluar-single" onclick="switchKeluarMode('single')"
                class="mode-switch-btn flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-black transition-all bg-white text-emerald-800 shadow-sm">
                <i class="fas fa-microphone"></i> Satu per Satu / Suara
            </button>
            <button type="button" id="btn-mode-keluar-bulk" onclick="switchKeluarMode('bulk')"
                class="mode-switch-btn flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-black transition-all text-slate-600 hover:text-emerald-800">
                <i class="fas fa-cash-register"></i> Input Massal (Kasir / Nota Penjualan)
            </button>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 1. MODE SATU PER SATU / SUARA --}}
    {{-- ======================================================== --}}
    <div id="container-keluar-single">
        {{-- Voice Assistant --}}
        <div class="voice-box">
            <div>
                <div class="title">
                    <i class="fas fa-robot"></i> Asisten Suara (Barang Keluar)
                </div>
                <div class="desc">
                    Tekan tombol mic, lalu sebutkan transaksi barang keluar.<br>
                    <em>Contoh:</em> "Keluar Mawar Merah 20 tangkai harga 15 ribu untuk Pelanggan Ani, transaksi Penjualan, ke Toko Cabang"
                </div>
            </div>
            <button type="button" id="btn-voice-keluar" class="btn-voice">
                <i class="fas fa-microphone" id="voice-icon-keluar"></i>
                <span id="voice-status-keluar">Mulai Bicara</span>
            </button>
            <div id="voice-transcript-keluar" class="voice-transcript">
                <span class="label">Terdengar:</span> <span id="voice-text-keluar" class="italic">...</span>
            </div>
        </div>

        {{-- Form Barang Keluar Satuan --}}
        <form method="POST" action="{{ route('form.barang-keluar.store') }}" onsubmit="return confirmSimpanKeluar();" class="form-card">
            @csrf

            <div class="form-header">
                <div class="icon-box">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div>
                    <h2>Informasi Barang Keluar (Satuan)</h2>
                    <p>Pilih barang dan masukkan detail pengeluaran.</p>
                </div>
            </div>

            <div class="form-body">
                {{-- SELECT BARANG --}}
                <div class="form-group">
                    <label for="kode_lokasi_kondisi" class="form-label">Pilih Barang <span class="required">*</span></label>
                    <select name="kode_lokasi_kondisi" id="kode_lokasi_kondisi" required class="form-control">
                        <option value="" disabled selected>-- Pilih Barang --</option>
                    </select>
                    @error('kode_lokasi_kondisi')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- HIDDEN INPUTS --}}
                <input type="hidden" name="id_lokasi" id="id_lokasi">
                <input type="hidden" name="id_kondisi" id="id_kondisi">
                <input type="hidden" name="item_id" id="item_id">

                {{-- INFORMASI BARANG (READONLY) --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" id="nama_barang" readonly class="form-control" placeholder="-">
                    </div>
                    <div class="form-group">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" id="satuan" readonly class="form-control" placeholder="-">
                    </div>
                    <div class="form-group">
                        <label for="stok_tersedia" class="form-label">Stok Tersedia</label>
                        <input type="text" id="stok_tersedia" readonly class="form-control" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label for="harga_dasar" class="form-label">Harga Rata-Rata</label>
                        <input type="text" id="harga_dasar" readonly class="form-control" placeholder="0">
                    </div>
                </div>

                {{-- JUMLAH + HARGA JUAL --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="jumlah_keluar" class="form-label">Jumlah Keluar <span class="required">*</span></label>
                        <input type="number" name="jumlah_keluar" id="jumlah_keluar" min="1" required class="form-control" placeholder="Contoh: 5">
                        <p id="jumlah_warning" class="form-error"></p>
                    </div>
                    <div class="form-group">
                        <label for="harga_jual" class="form-label">Harga Jual / Unit <span class="required">*</span></label>
                        <input type="number" name="harga_jual" id="harga_jual" min="0" step="0.01" required class="form-control" placeholder="0">
                    </div>
                </div>

                {{-- TOTAL NILAI --}}
                <div class="form-group">
                    <div class="total-box">
                        <div>
                            <p class="label">Total Nilai Barang</p>
                            <p class="sub-label">Jumlah × Harga Jual</p>
                        </div>
                        <div style="text-align:right;">
                            <p style="color:#059669;font-size:.7rem;font-weight:800;margin:0;">TOTAL</p>
                            <p id="total_harga_display" class="amount">Rp 0</p>
                        </div>
                    </div>
                    <input type="hidden" id="total_harga_jual" name="total_harga_jual" value="0">
                </div>

                {{-- PENERIMA + JENIS TRANSAKSI + LOKASI TUJUAN --}}
                <div class="grid-3">
                    <div class="form-group">
                        <label for="penerima" class="form-label">Penerima <span class="required">*</span></label>
                        <input type="text" name="penerima" id="penerima" value="{{ old('penerima') }}" required class="form-control" placeholder="Nama penerima">
                        @error('penerima')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_transaksi" class="form-label">Jenis Transaksi <span class="required">*</span></label>
                        <select name="jenis_transaksi" id="jenis_transaksi" required class="form-control">
                            <option value="" disabled {{ old('jenis_transaksi') ? '' : 'selected' }}>-- Pilih Jenis Transaksi --</option>
                            <option value="Penjualan" {{ old('jenis_transaksi') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                            <option value="Donasi" {{ old('jenis_transaksi') == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                            <option value="Pemakaian Internal" {{ old('jenis_transaksi') == 'Pemakaian Internal' ? 'selected' : '' }}>Pemakaian Internal</option>
                            <option value="Pemindahan Barang" {{ old('jenis_transaksi') == 'Pemindahan Barang' ? 'selected' : '' }}>Pemindahan Barang</option>
                            <option value="Retur ke Supplier" {{ old('jenis_transaksi') == 'Retur ke Supplier' ? 'selected' : '' }}>Retur ke Supplier</option>
                            <option value="Penghapusan" {{ old('jenis_transaksi') == 'Penghapusan' ? 'selected' : '' }}>Penghapusan</option>
                            <option value="Lainnya" {{ old('jenis_transaksi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('jenis_transaksi')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="lokasi_tujuan" class="form-label">Lokasi Tujuan <span class="required">*</span></label>
                        <input type="text" name="lokasi_tujuan" id="lokasi_tujuan" value="{{ old('lokasi_tujuan') }}" required class="form-control" placeholder="Contoh: Toko Cabang / Pelanggan">
                        @error('lokasi_tujuan')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" maxlength="255" class="form-control" placeholder="Contoh: Penerimaan Penjualan, Penghapusan, Retur...">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" id="submitBtn" class="btn-submit text-base py-3 px-6">
                    <i class="fas fa-save text-lg"></i> Simpan Barang Keluar
                </button>
            </div>
        </form>
    </div>

    {{-- ======================================================== --}}
    {{-- 2. MODE INPUT MASSAL (KASIR / NOTA PENJUALAN SPREADSHEET) --}}
    {{-- ======================================================== --}}
    <div id="container-keluar-bulk" class="hidden">
        <form id="form-keluar-bulk" action="{{ route('form.barang-keluar.store-bulk') }}" method="POST" class="form-card">
            @csrf

            {{-- HEADER NOTA PENJUALAN / KASIR --}}
            <div class="border-b-2 border-emerald-200 bg-emerald-50/80 p-5 sm:p-6">
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-200 px-3 py-1 text-xs font-black text-emerald-900 mb-1">
                            <i class="fas fa-cash-register"></i> Kasir Nota Penjualan
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black text-emerald-950">Informasi Nota Transaksi Penjualan</h2>
                    </div>
                    <div class="text-xs text-slate-500 italic">
                        *Isi nama pembeli & tujuan 1x untuk seluruh barang belanjaan di bawah.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                    {{-- Tanggal Keluar --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_keluar" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                    </div>

                    {{-- Nama Pembeli / Penerima --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Nama Pembeli / Penerima <span class="text-rose-500">*</span></label>
                        <input type="text" name="penerima" required value="Pelanggan Toko" placeholder="Contoh: Ibu Rina / Pelanggan"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                    </div>

                    {{-- Jenis Transaksi --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Jenis Transaksi <span class="text-rose-500">*</span></label>
                        <select name="jenis_transaksi" required
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                            <option value="Penjualan" selected>Penjualan</option>
                            <option value="Donasi">Donasi</option>
                            <option value="Pemakaian Internal">Pemakaian Internal</option>
                            <option value="Pemindahan Barang">Pemindahan Barang</option>
                            <option value="Retur ke Supplier">Retur ke Supplier</option>
                            <option value="Penghapusan">Penghapusan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    {{-- Lokasi Tujuan --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Tujuan / Kirim Ke <span class="text-rose-500">*</span></label>
                        <input type="text" name="lokasi_tujuan" required value="Pelanggan Langsung" placeholder="Contoh: Pelanggan / Toko Cabang"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                    </div>

                    {{-- Catatan Nota --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Catatan Nota (Opsional)</label>
                        <input type="text" name="catatan" placeholder="Catatan kasir / nomor nota"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                    </div>
                </div>
            </div>

            {{-- TABEL RINCIAN KASIR --}}
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <h3 class="text-base sm:text-lg font-black text-emerald-950">
                        <i class="fas fa-shopping-cart mr-1 text-emerald-600"></i> Daftar Barang yang Dijual / Dikeluarkan
                    </h3>
                    <button type="button" onclick="addKeluarRow()"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-sm font-black shadow transition">
                        <i class="fas fa-plus-circle text-base"></i> + Tambah Baris Kasir
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border-2 border-emerald-100 bg-white">
                    <table class="w-full text-left border-collapse min-w-[800px]" id="table-keluar-bulk">
                        <thead>
                            <tr class="bg-emerald-900 text-white text-sm font-black uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4 min-w-[260px]">Pilih Bunga / Barang <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-32 text-center">Sisa Stok</th>
                                <th class="py-3.5 px-4 w-36 text-center">Qty Jual <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-44 text-right">Harga Jual (Rp) <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-48 text-right">Subtotal (Rp)</th>
                                <th class="py-3.5 px-3 text-center w-14">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-keluar-bulk" class="divide-y divide-slate-200 text-slate-800">
                            {{-- Baris diisi oleh JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- PETUNJUK & QUICK BUTTON --}}
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 italic">
                        <i class="fas fa-info-circle text-emerald-600"></i> Sistem otomatis mengecek kecukupan stok saat Anda memilih barang dan mengetik jumlah.
                    </p>
                    <button type="button" onclick="addKeluarRow()"
                        class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-800 transition">
                        <i class="fas fa-plus"></i> + Tambah Baris Baru
                    </button>
                </div>
            </div>

            {{-- FOOTER TOTAL & TOMBOL SIMPAN --}}
            <div class="form-footer flex flex-col sm:flex-row items-center justify-between gap-6 p-6 bg-slate-50 border-t-2 border-slate-200">
                <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-start">
                    <div class="bg-emerald-100/90 border border-emerald-300 px-5 py-3 rounded-2xl">
                        <p class="text-xs font-black uppercase text-emerald-800">TOTAL PENJUALAN NOTA</p>
                        <p id="keluar-bulk-grand-total" class="text-2xl sm:text-3xl font-black text-emerald-950">Rp 0</p>
                    </div>
                    <div class="text-sm font-bold text-slate-600">
                        Total: <span id="keluar-bulk-total-qty" class="font-black text-emerald-800 text-lg">0</span> item keluar
                    </div>
                </div>
                <button type="submit" id="btn-submit-keluar-bulk" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white px-8 py-4 text-lg font-black shadow-lg shadow-emerald-900/20 transition-all transform active:scale-95">
                    <i class="fas fa-save text-xl"></i> Simpan Transaksi Penjualan (Kasir)
                </button>
            </div>
        </form>
    </div>

    {{-- SCRIPT JAVASCRIPT KHUSUS BARANG KELUAR --}}
    <script>
        // Data Items Keluar dari Server dengan dynamic stock
        const ALL_ITEMS_KELUAR = @json($itemsJson ?? []);
        let PILIHAN_BARANG_KELUAR = @json($barangKeluarItemsJson ?? []);

        function switchKeluarMode(mode) {
            const btnSingle = document.getElementById('btn-mode-keluar-single');
            const btnBulk   = document.getElementById('btn-mode-keluar-bulk');
            const contSingle = document.getElementById('container-keluar-single');
            const contBulk   = document.getElementById('container-keluar-bulk');

            if (mode === 'bulk') {
                btnBulk.classList.add('bg-white', 'text-emerald-800', 'shadow-sm');
                btnBulk.classList.remove('text-slate-600');
                btnSingle.classList.remove('bg-white', 'text-emerald-800', 'shadow-sm');
                btnSingle.classList.add('text-slate-600');

                contSingle.classList.add('hidden');
                contBulk.classList.remove('hidden');

                if (document.getElementById('tbody-keluar-bulk').children.length === 0) {
                    for (let i = 0; i < 3; i++) addKeluarRow();
                } else {
                    // Trigger resize untuk Select2 agar fit container
                    if (window.jQuery) $(window).trigger('resize');
                }
            } else {
                btnSingle.classList.add('bg-white', 'text-emerald-800', 'shadow-sm');
                btnSingle.classList.remove('text-slate-600');
                btnBulk.classList.remove('bg-white', 'text-emerald-800', 'shadow-sm');
                btnBulk.classList.add('text-slate-600');

                contBulk.classList.add('hidden');
                contSingle.classList.remove('hidden');
            }
        }

        let keluarRowCounter = 0;

        function initKeluarRowSelect2(idx) {
            const selectEl = document.querySelector(`#row-keluar-${idx} .select-keluar-item`);
            if (!selectEl || !window.jQuery || !$.fn.select2) return;

            $(selectEl).select2({
                placeholder: '🔍 Cari / Pilih Barang Tersedia...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('body'),
                language: {
                    noResults: function() { return 'Barang tidak ditemukan atau stok kosong'; },
                    searching: function() { return 'Mencari...'; }
                }
            }).on('select2:select select2:unselect', function() {
                onKeluarItemChange(idx, this);
            });
        }

        function addKeluarRow() {
            const tbody = document.getElementById('tbody-keluar-bulk');
            keluarRowCounter++;
            const idx = keluarRowCounter;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/40 transition-colors';
            tr.id = `row-keluar-${idx}`;

            let itemOptionsHtml = '<option value=""></option>';
            if (PILIHAN_BARANG_KELUAR && PILIHAN_BARANG_KELUAR.length > 0) {
                PILIHAN_BARANG_KELUAR.forEach(it => {
                    itemOptionsHtml += `<option value="${it.item_id}|${it.lokasi_id}|${it.kondisi_id}"
                        data-item-id="${it.item_id}"
                        data-lokasi-id="${it.lokasi_id}"
                        data-kondisi-id="${it.kondisi_id}"
                        data-stok="${it.stok}"
                        data-harga="${it.harga_dasar}"
                        data-nama="${it.nama_barang}"
                        data-satuan="${it.satuan}">
                        ${it.kode} — ${it.nama_barang} (Lokasi: ${it.lokasi} · Kondisi: ${it.kondisi}) — Sisa Stok: ${it.stok} ${it.satuan}
                    </option>`;
                });
            } else {
                itemOptionsHtml += '<option value="" disabled>-- Belum ada stok barang (Belum ada Barang Masuk) --</option>';
            }

            tr.innerHTML = `
                <td class="py-3 px-3 text-center font-black text-slate-400 row-number text-base"></td>
                <td class="py-3 px-3" style="min-width:180px; max-width:280px;">
                    <select name="rows[${idx}][kode_lokasi_kondisi]" required
                        class="w-full select-keluar-item">
                        ${itemOptionsHtml}
                    </select>
                    <input type="hidden" name="rows[${idx}][item_id]" id="input-item-id-${idx}">
                    <input type="hidden" name="rows[${idx}][id_lokasi]" id="input-lokasi-id-${idx}">
                    <input type="hidden" name="rows[${idx}][id_kondisi]" id="input-kondisi-id-${idx}">
                </td>
                <td class="py-3 px-3 text-center">
                    <span id="stok-tersedia-badge-${idx}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-slate-100 text-slate-700" data-stok="0">
                        -
                    </span>
                </td>
                <td class="py-3 px-3 text-center">
                    <input type="number" name="rows[${idx}][jumlah_keluar]" min="1" value="1" required
                        oninput="calcKeluarRow(${idx})"
                        class="w-28 mx-auto text-center rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-lg font-black text-slate-900 bg-white input-qty-keluar">
                    <p id="warning-keluar-${idx}" class="text-xs font-bold text-rose-600 mt-1 hidden"></p>
                </td>
                <td class="py-3 px-3 text-right">
                    <input type="number" name="rows[${idx}][harga_jual]" min="0" step="100" value="0" required
                        oninput="calcKeluarRow(${idx})" 
                        class="w-full text-right rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-lg font-black text-emerald-900 bg-white">
                </td>
                <td class="py-3 px-3 text-right">
                    <span id="subtotal-keluar-${idx}" class="text-base font-black text-emerald-950">Rp 0</span>
                </td>
                <td class="py-3 px-2 text-center">
                    <button type="button" onclick="removeKeluarRow('${idx}')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-rose-500 hover:bg-rose-100 hover:text-rose-700 transition"
                        title="Hapus Baris Ini">
                        <i class="fas fa-trash-alt text-base"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            // Inisialisasi Select2 pada baris baru
            initKeluarRowSelect2(idx);
            reindexKeluarRows();
            calcKeluarGrandTotal();
        }

        function removeKeluarRow(idx) {
            const row = document.getElementById(`row-keluar-${idx}`);
            if (row) {
                const selectEl = $(row).find('.select-keluar-item');
                if (selectEl.length && selectEl.data('select2')) {
                    selectEl.select2('destroy');
                }
                row.remove();
                reindexKeluarRows();
                calcKeluarGrandTotal();
            }
        }

        function reindexKeluarRows() {
            const rows = document.querySelectorAll('#tbody-keluar-bulk tr');
            rows.forEach((r, i) => {
                const numCell = r.querySelector('.row-number');
                if (numCell) numCell.textContent = i + 1;
            });
        }

        function onKeluarItemChange(idx, selectEl) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            const val = selectEl.value;

            const row = document.getElementById(`row-keluar-${idx}`);
            if (!row) return;

            const hiddenItemId    = document.getElementById(`input-item-id-${idx}`);
            const hiddenLokasiId  = document.getElementById(`input-lokasi-id-${idx}`);
            const hiddenKondisiId = document.getElementById(`input-kondisi-id-${idx}`);
            const hargaInput      = row.querySelector(`input[name="rows[${idx}][harga_jual]"]`);
            const stokBadge       = document.getElementById(`stok-tersedia-badge-${idx}`);

            if (!val || !selectedOpt) {
                if (hiddenItemId) hiddenItemId.value = '';
                if (hiddenLokasiId) hiddenLokasiId.value = '';
                if (hiddenKondisiId) hiddenKondisiId.value = '';

                if (stokBadge) {
                    stokBadge.textContent = '-';
                    stokBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-slate-100 text-slate-700';
                    stokBadge.dataset.stok = 0;
                }
                calcKeluarRow(idx);
                return;
            }

            const itemId       = selectedOpt.dataset.itemId || (val.split('|')[0] ?? '');
            const lokasiId     = selectedOpt.dataset.lokasiId || (val.split('|')[1] ?? '');
            const kondisiId    = selectedOpt.dataset.kondisiId || (val.split('|')[2] ?? '');
            const defaultHarga = selectedOpt.dataset.harga ? parseFloat(selectedOpt.dataset.harga) : 0;
            const stokVal      = selectedOpt.dataset.stok !== undefined ? parseInt(selectedOpt.dataset.stok) : 0;
            const satuan       = selectedOpt.dataset.satuan || '';

            if (hiddenItemId) hiddenItemId.value = itemId;
            if (hiddenLokasiId) hiddenLokasiId.value = lokasiId;
            if (hiddenKondisiId) hiddenKondisiId.value = kondisiId;

            if (stokBadge) {
                stokBadge.dataset.stok = stokVal;
                if (stokVal > 0) {
                    stokBadge.textContent = `${stokVal} ${satuan}`;
                    stokBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-emerald-100 text-emerald-800';
                } else {
                    stokBadge.textContent = '0 (Habis)';
                    stokBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-rose-100 text-rose-800';
                }
            }

            if (hargaInput && (parseFloat(hargaInput.value) === 0 || !hargaInput.value)) {
                hargaInput.value = defaultHarga;
            }

            calcKeluarRow(idx);
        }

        function calcKeluarRow(idx) {
            const row = document.getElementById(`row-keluar-${idx}`);
            if (!row) return;

            const qtyInput     = row.querySelector(`input[name="rows[${idx}][jumlah_keluar]"]`);
            const hargaInput   = row.querySelector(`input[name="rows[${idx}][harga_jual]"]`);
            const subtotalSpan = document.getElementById(`subtotal-keluar-${idx}`);
            const stokBadge    = document.getElementById(`stok-tersedia-badge-${idx}`);
            const warningP     = document.getElementById(`warning-keluar-${idx}`);
            const selectEl     = row.querySelector('.select-keluar-item');

            const hasItem  = selectEl && selectEl.value;
            const qty      = qtyInput ? Math.max(0, parseInt(qtyInput.value) || 0) : 0;
            const harga    = hargaInput ? Math.max(0, parseFloat(hargaInput.value) || 0) : 0;
            const subtotal = qty * harga;
            const stok     = stokBadge && stokBadge.dataset.stok !== undefined ? parseInt(stokBadge.dataset.stok) : 0;

            if (subtotalSpan) {
                subtotalSpan.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
            }

            // Validasi kecukupan stok real-time
            if (hasItem) {
                if (stok <= 0) {
                    if (warningP) {
                        warningP.textContent = `Stok barang kosong (0)!`;
                        warningP.classList.remove('hidden');
                    }
                    qtyInput?.classList.add('border-rose-500', 'bg-rose-50');
                } else if (qty > stok) {
                    if (warningP) {
                        warningP.textContent = `Jumlah melebihi stok (${stok})!`;
                        warningP.classList.remove('hidden');
                    }
                    qtyInput?.classList.add('border-rose-500', 'bg-rose-50');
                } else {
                    if (warningP) warningP.classList.add('hidden');
                    qtyInput?.classList.remove('border-rose-500', 'bg-rose-50');
                }
            } else {
                if (warningP) warningP.classList.add('hidden');
                qtyInput?.classList.remove('border-rose-500', 'bg-rose-50');
            }

            calcKeluarGrandTotal();
        }

        function calcKeluarGrandTotal() {
            const rows = document.querySelectorAll('#tbody-keluar-bulk tr');
            let grandTotal = 0;
            let totalQty = 0;
            let hasAnyError = false;
            let validCount = 0;

            rows.forEach(r => {
                const qtyInput   = r.querySelector('input[name*="[jumlah_keluar]"]');
                const hargaInput = r.querySelector('input[name*="[harga_jual]"]');
                const itemSelect = r.querySelector('.select-keluar-item');
                const stokBadge  = r.querySelector('[id^="stok-tersedia-badge-"]');

                if (itemSelect && itemSelect.value) {
                    validCount++;
                    const qty   = qtyInput ? (parseInt(qtyInput.value) || 0) : 0;
                    const harga = hargaInput ? (parseFloat(hargaInput.value) || 0) : 0;
                    const stok  = stokBadge && stokBadge.dataset.stok !== undefined ? parseInt(stokBadge.dataset.stok) : 0;

                    grandTotal += (qty * harga);
                    totalQty += qty;

                    if (stok <= 0 || qty > stok) {
                        hasAnyError = true;
                    }
                }
            });

            const display = document.getElementById('keluar-bulk-grand-total');
            if (display) {
                display.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
            }

            const badge = document.getElementById('keluar-bulk-total-qty');
            if (badge) {
                badge.textContent = totalQty;
            }

            // Real-time disable tombol Submit jika ada error stok
            const submitBtn = document.getElementById('btn-submit-keluar-bulk');
            if (submitBtn) {
                if (hasAnyError) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        // Form submit validation for bulk
        document.getElementById('form-keluar-bulk')?.addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('#tbody-keluar-bulk tr');
            let hasError = false;
            let errorMsg = '';
            let validCount = 0;

            rows.forEach((r, i) => {
                const itemSelect = r.querySelector('.select-keluar-item');
                const qtyInput   = r.querySelector('input[name*="[jumlah_keluar]"]');
                const stokBadge  = r.querySelector('[id^="stok-tersedia-badge-"]');

                if (itemSelect && itemSelect.value) {
                    validCount++;
                    const qty = parseInt(qtyInput?.value) || 0;
                    const stok = stokBadge && stokBadge.dataset.stok !== undefined ? parseInt(stokBadge.dataset.stok) : 0;
                    if (stok <= 0) {
                        hasError = true;
                        errorMsg = `Baris ${i + 1}: Stok barang yang dipilih kosong/habis!`;
                    } else if (qty > stok) {
                        hasError = true;
                        errorMsg = `Baris ${i + 1}: Jumlah keluar (${qty}) melebihi stok tersedia (${stok})!`;
                    }
                }
            });

            if (validCount === 0) {
                e.preventDefault();
                alert('Pilih minimal satu barang untuk disimpan.');
                return false;
            }

            if (hasError) {
                e.preventDefault();
                alert(errorMsg);
                return false;
            }
        });

        // Voice Assistant & Single Mode Handler
        (function() {
            let mediaRecorder = null;
            let audioChunks = [];
            let isRecording = false;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            function num(value) {
                const number = parseFloat(String(value ?? '').replace(/[^\d.-]/g, ''));
                return isNaN(number) ? 0 : number;
            }

            function pickHarga(data) {
                return num(
                    data?.harga_dasar ??
                    data?.harga ??
                    data?.harga_satuan ??
                    data?.item?.harga_dasar
                );
            }

            const btnVoice = document.getElementById('btn-voice-keluar');
            const voiceIcon = document.getElementById('voice-icon-keluar');
            const voiceStatus = document.getElementById('voice-status-keluar');
            const transcriptBox = document.getElementById('voice-transcript-keluar');
            const transcriptText = document.getElementById('voice-text-keluar');

            function updateVoiceUI(status, message = '') {
                if (!btnVoice) return;
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
                formData.append('audio', blob, 'recording.webm');

                try {
                    const response = await fetch("{{ route('form.barang-keluar.parse-voice') }}", {
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

            if (btnVoice) {
                btnVoice.addEventListener('click', function() {
                    if (!isRecording) {
                        startRecording();
                    } else {
                        stopRecording();
                    }
                });
            }

            async function loadPilihanBarang() {
                try {
                    const response = await fetch("{{ route('form.barang-keluar.pilihan-barang') }}");
                    if (!response.ok) throw new Error('Gagal mengambil data barang.');

                    const data = await response.json();
                    const select = document.getElementById('kode_lokasi_kondisi');
                    if (!select) return;

                    if (!data || data.length === 0) {
                        select.innerHTML = '<option value="" disabled selected>-- Belum ada stok barang (Belum ada Barang Masuk) --</option>';
                        return;
                    }

                    select.innerHTML = '<option value="" disabled selected>-- Pilih Barang Tersedia --</option>';

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.dataset.itemId = item.item_id;
                        option.value = `${item.kode}|${item.lokasi_id}|${item.kondisi_id}`;
                        option.text = `${item.kode} - ${item.nama_barang} (Lokasi: ${item.lokasi} · Kondisi: ${item.kondisi}) — Sisa Stok: ${item.stok} ${item.satuan}`;
                        option.dataset.nama = item.nama_barang ?? '';
                        option.dataset.satuan = item.satuan ?? '';
                        option.dataset.stok = item.stok ?? 0;
                        option.dataset.harga = pickHarga(item);
                        select.appendChild(option);
                    });

                    if (data && Array.isArray(data)) {
                        PILIHAN_BARANG_KELUAR = data;
                    }

                    if (window.jQuery && $.fn.select2) {
                        $(select).select2({
                            placeholder: '-- Pilih Barang Tersedia --',
                            allowClear: true,
                            width: '100%',
                            language: {
                                noResults: function() { return 'Barang tidak ditemukan atau stok kosong'; },
                                searching: function() { return 'Mencari...'; }
                            }
                        }).on('select2:select select2:unselect', function() {
                            select.dispatchEvent(new Event('change'));
                        });
                    }

                    const oldItemId = "{{ old('item_id') }}";
                    if (oldItemId) {
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].dataset.itemId == oldItemId) {
                                select.selectedIndex = i;
                                if (window.jQuery && $.fn.select2) {
                                    $(select).val(select.options[i].value).trigger('change');
                                } else {
                                    select.dispatchEvent(new Event('change'));
                                }
                                break;
                            }
                        }
                    }

                } catch (error) {
                    console.error('Error loadPilihanBarang:', error);
                }
            }

            async function fetchDetail(itemId, lokasi, kondisi) {
                try {
                    const response = await fetch(
                        `{{ route('barang-keluar.detail-barang') }}?item_id=${encodeURIComponent(itemId)}&id_lokasi=${encodeURIComponent(lokasi)}&id_kondisi=${encodeURIComponent(kondisi)}`
                    );
                    if (!response.ok) throw new Error('Gagal mengambil detail barang.');

                    const data = await response.json();
                    const hargaItem = pickHarga(data);

                    document.getElementById('nama_barang').value = data.nama_barang ?? '';
                    document.getElementById('satuan').value = data.satuan ?? '';
                    document.getElementById('stok_tersedia').value = num(data.stok);
                    document.getElementById('harga_dasar').value = hargaItem;

                    const hargaJualInput = document.getElementById('harga_jual');
                    if (hargaJualInput && (!hargaJualInput.value || hargaJualInput.value == 0)) {
                        hargaJualInput.value = hargaItem;
                    }

                    updateTotal();
                } catch (error) {
                    console.error('Error fetchDetail:', error);
                }
            }

            function applyAiParsedData(data) {
                const select = document.getElementById('kode_lokasi_kondisi');

                if (data.item_id && select) {
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].dataset.itemId == data.item_id) {
                            select.selectedIndex = i;
                            if (window.jQuery && $.fn.select2) {
                                $(select).val(select.options[i].value).trigger('change');
                            } else {
                                select.dispatchEvent(new Event('change'));
                            }
                            break;
                        }
                    }
                }

                if (data.jumlah_keluar) document.getElementById('jumlah_keluar').value = data.jumlah_keluar;
                if (data.harga_jual) document.getElementById('harga_jual').value = data.harga_jual;
                if (data.penerima) document.getElementById('penerima').value = data.penerima;
                if (data.lokasi_tujuan) document.getElementById('lokasi_tujuan').value = data.lokasi_tujuan;
                if (data.catatan) document.getElementById('catatan').value = data.catatan;

                if (data.jenis_transaksi) {
                    const jenisSelect = document.getElementById('jenis_transaksi');
                    for (let option of jenisSelect.options) {
                        if (option.value.toLowerCase() === String(data.jenis_transaksi).toLowerCase()) {
                            option.selected = true;
                            break;
                        }
                    }
                }

                updateTotal();
            }

            function updateTotal() {
                const jumlahInput = document.getElementById('jumlah_keluar');
                const hargaInput  = document.getElementById('harga_jual');
                const stokInput   = document.getElementById('stok_tersedia');
                const totalHidden = document.getElementById('total_harga_jual');
                const totalDisplay = document.getElementById('total_harga_display');
                const warning = document.getElementById('jumlah_warning');
                const submit = document.getElementById('submitBtn');

                if (!jumlahInput || !hargaInput) return;

                const jumlah = num(jumlahInput.value);
                const harga = num(hargaInput.value);
                const stok = num(stokInput?.value);
                const total = jumlah * harga;

                if (totalHidden) totalHidden.value = total;
                if (totalDisplay) {
                    totalDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                }

                if (jumlah > stok && stok > 0) {
                    if (warning) warning.textContent = `Jumlah melebihi stok tersedia (${stok}).`;
                    if (submit) {
                        submit.disabled = true;
                        submit.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else if (jumlah < 1 && jumlahInput.value !== '') {
                    if (warning) warning.textContent = 'Jumlah minimal 1.';
                    if (submit) {
                        submit.disabled = true;
                        submit.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    if (warning) warning.textContent = '';
                    if (submit) {
                        submit.disabled = false;
                        submit.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }
            }

            window.confirmSimpanKeluar = function() {
                const jumlah = num(document.getElementById('jumlah_keluar').value);
                const stok = num(document.getElementById('stok_tersedia').value);
                if (jumlah > stok) {
                    alert(`Jumlah keluar melebihi stok tersedia. Sisa stok: ${stok}`);
                    return false;
                }
                return confirm('Apakah Anda yakin ingin menyimpan transaksi barang keluar ini?');
            };

            function initTabKeluar() {
                const select = document.getElementById('kode_lokasi_kondisi');
                const jumlah = document.getElementById('jumlah_keluar');
                const harga = document.getElementById('harga_jual');

                loadPilihanBarang();

                if (select) {
                    select.addEventListener('change', async function() {
                        if (!this.value) return;

                        const [kode, lokasi, kondisi] = this.value.split('|');
                        const option = this.options[this.selectedIndex];

                        document.getElementById('item_id').value = option.dataset.itemId || '';
                        document.getElementById('id_lokasi').value = lokasi || '';
                        document.getElementById('id_kondisi').value = kondisi || '';
                        document.getElementById('nama_barang').value = option.dataset.nama ?? '';
                        document.getElementById('satuan').value = option.dataset.satuan ?? '';
                        document.getElementById('stok_tersedia').value = num(option.dataset.stok);

                        const hargaDefault = num(option.dataset.harga);
                        document.getElementById('harga_dasar').value = hargaDefault;

                        const hargaJualInput = document.getElementById('harga_jual');
                        if (hargaJualInput && (!hargaJualInput.value || hargaJualInput.value == 0)) {
                            hargaJualInput.value = hargaDefault;
                        }

                        await fetchDetail(option.dataset.itemId, lokasi, kondisi);
                        updateTotal();
                    });
                }

                if (jumlah) jumlah.addEventListener('input', updateTotal);
                if (harga) harga.addEventListener('input', updateTotal);

                updateTotal();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTabKeluar);
            } else {
                initTabKeluar();
            }
        })();
    </script>

</section>
