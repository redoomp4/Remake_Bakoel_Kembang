<section id="masuk" class="tab-content" role="tabpanel">

    {{-- SWITCHER MODE INPUT (SATUAN vs MASSAL) --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border-2 border-emerald-200 bg-white p-3 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 font-black">
                <i class="fas fa-receipt text-lg"></i>
            </span>
            <div>
                <h3 class="text-base font-black text-emerald-950">Mode Transaksi Barang Masuk</h3>
                <p class="text-xs text-slate-500">Pilih ingin catat 1 transaksi atau catat banyak barang sekaligus (Nota Restock)</p>
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
        {{-- Voice Assistant --}}
        <div class="voice-box">
            <div>
                <div class="title">
                    <i class="fas fa-robot"></i> Asisten Suara (Barang Masuk)
                </div>
                <div class="desc">
                    Tekan tombol mic, lalu sebutkan transaksi barang masuk.<br>
                    <em>Contoh:</em> "Masuk Mawar Merah 50 tangkai harga 10 ribu dari Supplier Agromart di Gudang Utama kondisi Segar"
                </div>
            </div>
            <button type="button" id="btn-voice-masuk" class="btn-voice">
                <i class="fas fa-microphone" id="voice-icon-masuk"></i>
                <span id="voice-status-masuk">Mulai Bicara</span>
            </button>
            <div id="voice-transcript-masuk" class="voice-transcript">
                <span class="label">Terdengar:</span> <span id="voice-text-masuk" class="italic">...</span>
            </div>
        </div>

        {{-- Form Transaksi Barang Masuk Satuan --}}
        <form id="form-barang-masuk" action="{{ route('form.barang-masuk.store') }}" method="POST" class="form-card">
            @csrf

            <div class="form-header">
                <div class="icon-box">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div>
                    <h2>Transaksi Barang Masuk (Satuan)</h2>
                    <p>Catat penerimaan satu item barang ke dalam inventori.</p>
                </div>
            </div>

            <div class="form-body">
                {{-- SELECT BARANG --}}
                <div class="form-group">
                    <label for="select-item-masuk" class="form-label">Pilih Barang Master <span class="required">*</span></label>
                    <select name="id_item" id="select-item-masuk" required class="form-control select2-item">
                        <option value="">-- Cari / Pilih Barang Master --</option>
                        @foreach ($items ?? [] as $item)
                            <option value="{{ $item->id }}" data-harga="{{ $item->harga_dasar }}" {{ old('id_item') == $item->id ? 'selected' : '' }}>
                                {{ $item->kode_barang }} — {{ $item->nama_barang }} (Stok: {{ $item->total_stok }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">*Barang harus terdaftar di Master Item terlebih dahulu.</div>
                    @error('id_item')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- JUMLAH + HARGA BELI --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="input-jumlah-masuk" class="form-label">Jumlah Masuk <span class="required">*</span></label>
                        <input type="number" name="jumlah" id="input-jumlah-masuk" min="1" required class="form-control" placeholder="Contoh: 50" value="{{ old('jumlah') }}">
                        @error('jumlah')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="input-harga-beli-masuk" class="form-label">Harga Beli Satuan (Rp) <span class="required">*</span></label>
                        <input type="number" name="harga_beli" id="input-harga-beli-masuk" min="0" step="0.01" required class="form-control" placeholder="0.00" value="{{ old('harga_beli') }}">
                        @error('harga_beli')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- TANGGAL MASUK + KADALUARSA --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="input-tgl-masuk" class="form-label">Tanggal Masuk <span class="required">*</span></label>
                        <input type="date" name="tgl_masuk" id="input-tgl-masuk" required class="form-control" value="{{ old('tgl_masuk', date('Y-m-d')) }}">
                        @error('tgl_masuk')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="input-tgl-kadaluarsa" class="form-label">Tanggal Kadaluarsa <span class="form-hint" style="display:inline;font-weight:400;">(Opsional)</span></label>
                        <input type="date" name="tgl_kadaluarsa" id="input-tgl-kadaluarsa" class="form-control" value="{{ old('tgl_kadaluarsa') }}">
                        @error('tgl_kadaluarsa')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PEMASOK + LOKASI + KONDISI --}}
                <div class="grid-3">
                    <div class="form-group">
                        <label for="input-pemasok-masuk" class="form-label">Pemasok <span class="required">*</span></label>
                        <div class="hybrid-select" id="hybrid-pemasok-masuk">
                            <input type="text" name="pemasok_input" id="input-pemasok-masuk" value="{{ old('pemasok_input') }}"
                                   required class="hybrid-input" placeholder="-- Pilih / Ketik Pemasok --" autocomplete="off">
                            <button type="button" class="hybrid-toggle">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="hybrid-options">
                                @foreach ($pemasoks ?? [] as $item)
                                    <li class="hybrid-option" data-value="{{ $item->nama_pemasok }}">{{ $item->nama_pemasok }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @error('pemasok_input')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="input-lokasi-masuk" class="form-label">Lokasi Penyimpanan</label>
                        <div class="hybrid-select" id="hybrid-lokasi-masuk">
                            <input type="text" name="lokasi_input" id="input-lokasi-masuk" value="{{ old('lokasi_input') }}"
                                   class="hybrid-input" placeholder="-- Pilih / Ketik Lokasi --" autocomplete="off">
                            <button type="button" class="hybrid-toggle">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="hybrid-options">
                                @foreach ($lokasis ?? [] as $item)
                                    <li class="hybrid-option" data-value="{{ $item->nama_lokasi }}">{{ $item->nama_lokasi }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @error('lokasi_input')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="input-kondisi-masuk" class="form-label">Kondisi Barang</label>
                        <div class="hybrid-select" id="hybrid-kondisi-masuk">
                            <input type="text" name="kondisi_input" id="input-kondisi-masuk" value="{{ old('kondisi_input') }}"
                                   class="hybrid-input" placeholder="-- Pilih / Ketik Kondisi --" autocomplete="off">
                            <button type="button" class="hybrid-toggle">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="hybrid-options">
                                @foreach ($kondisis ?? [] as $item)
                                    <li class="hybrid-option" data-value="{{ $item->nama_kondisi }}">{{ $item->nama_kondisi }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @error('kondisi_input')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label for="input-catatan-masuk" class="form-label">Catatan Transaksi</label>
                    <textarea name="catatan" id="input-catatan-masuk" rows="3" class="form-control" placeholder="Catatan tambahan transaksi barang masuk">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit text-base py-3 px-6">
                    <i class="fas fa-save text-lg"></i> Simpan Transaksi Masuk
                </button>
            </div>
        </form>
    </div>

    {{-- ======================================================== --}}
    {{-- 2. MODE INPUT MASSAL (NOTA RESTOCK / SPREADSHEET TABLE)   --}}
    {{-- ======================================================== --}}
    <div id="container-masuk-bulk" class="hidden">
        <form id="form-masuk-bulk" action="{{ route('form.barang-masuk.store-bulk') }}" method="POST" class="form-card">
            @csrf

            {{-- HEADER NOTA (DIISI 1x SAJA DI ATAS) --}}
            <div class="border-b-2 border-emerald-200 bg-emerald-50/80 p-5 sm:p-6">
                <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-200 px-3 py-1 text-xs font-black text-emerald-900 mb-1">
                            <i class="fas fa-receipt"></i> Form Nota Restock Masuk
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black text-emerald-950">Informasi Nota / Faktur Pembelian</h2>
                    </div>
                    <div class="text-xs text-slate-500 italic">
                        *Cukup isi informasi nota di bawah ini 1x untuk semua barang di tabel.
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Tanggal Masuk --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Tanggal Masuk <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                    </div>

                    {{-- Pemasok --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Pemasok / Supplier <span class="text-rose-500">*</span></label>
                        <input type="text" name="pemasok_input" list="datalist-pemasok-bulk" required
                            placeholder="-- Pilih/Ketik Pemasok --"
                            value="{{ $pemasoks->first()?->nama_pemasok ?? 'Pemasok Umum' }}"
                            class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                        <datalist id="datalist-pemasok-bulk">
                            @foreach ($pemasoks ?? [] as $pem)
                                <option value="{{ $pem->nama_pemasok }}">
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Lokasi Penyimpanan --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Lokasi Masuk</label>
                        <select name="id_lokasi" class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                            @foreach ($lokasis ?? [] as $lok)
                                <option value="{{ $lok->id }}">{{ $lok->nama_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kondisi Barang --}}
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-600 mb-1">Kondisi</label>
                        <select name="id_kondisi" class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white">
                            @foreach ($kondisis ?? [] as $kon)
                                <option value="{{ $kon->id }}">{{ $kon->nama_kondisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Catatan Nota --}}
                <div class="mt-3">
                    <input type="text" name="catatan" placeholder="Catatan faktur/surat jalan (opsional)"
                        class="w-full rounded-xl border border-slate-300 focus:border-emerald-600 p-2 text-sm text-slate-700 bg-white">
                </div>
            </div>

            {{-- TABEL DAFTAR BARANG NOTA --}}
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <h3 class="text-base sm:text-lg font-black text-emerald-950">
                        <i class="fas fa-list-ul mr-1 text-emerald-600"></i> Rincian Barang yang Diterima
                    </h3>
                    <button type="button" onclick="addMasukRow()"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 text-sm font-black shadow transition">
                        <i class="fas fa-plus-circle text-base"></i> + Tambah Baris Barang
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border-2 border-emerald-100 bg-white">
                    <table class="w-full text-left border-collapse min-w-[850px]" id="table-masuk-bulk">
                        <thead>
                            <tr class="bg-emerald-900 text-white text-sm font-black uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4 min-w-[280px]">Pilih Barang Master <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-36 text-center">Stok Saat Ini</th>
                                <th class="py-3.5 px-4 w-36 text-center">Jumlah Masuk <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-44 text-right">Harga Beli Satuan (Rp) <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-48 text-right">Subtotal (Rp)</th>
                                <th class="py-3.5 px-3 text-center w-14">Hapus</th>
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
                        <i class="fas fa-info-circle text-emerald-600"></i> Pilih barang, lalu masukkan jumlah dan harga beli. Subtotal otomatis terhitung!
                    </p>
                    <button type="button" onclick="addMasukRow()"
                        class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-800 transition">
                        <i class="fas fa-plus"></i> + Tambah Baris Baru
                    </button>
                </div>
            </div>

            {{-- FOOTER TOTAL & TOMBOL SIMPAN --}}
            <div class="form-footer flex flex-col sm:flex-row items-center justify-between gap-6 p-6 bg-slate-50 border-t-2 border-slate-200">
                <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-start">
                    <div class="bg-emerald-100/90 border border-emerald-300 px-5 py-3 rounded-2xl">
                        <p class="text-xs font-black uppercase text-emerald-800">TOTAL BELANJA NOTA</p>
                        <p id="masuk-bulk-grand-total" class="text-2xl sm:text-3xl font-black text-emerald-950">Rp 0</p>
                    </div>
                    <div class="text-sm font-bold text-slate-600">
                        Total: <span id="masuk-bulk-total-qty" class="font-black text-emerald-800 text-lg">0</span> item barang
                    </div>
                </div>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white px-8 py-4 text-lg font-black shadow-lg shadow-emerald-900/20 transition-all transform active:scale-95">
                    <i class="fas fa-save text-xl"></i> Simpan Semua Transaksi Masuk
                </button>
            </div>
        </form>
    </div>

    {{-- SCRIPT JAVASCRIPT KHUSUS BARANG MASUK --}}
    <script>
        // Item Options dari Server
        const ALL_ITEMS_MASUK = @json($itemsJson ?? []);

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

        let masukRowCounter = 0;

        function initMasukRowSelect2(idx) {
            const selectEl = document.querySelector(`#row-masuk-${idx} select[name="rows[${idx}][item_id]"]`);
            if (!selectEl || !window.jQuery || !$.fn.select2) return;

            $(selectEl).select2({
                placeholder: '🔍 Cari / Pilih Barang Master...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('body'),
                language: {
                    noResults: function() { return 'Barang tidak ditemukan'; },
                    searching: function() { return 'Mencari...'; }
                }
            }).on('select2:select select2:unselect', function() {
                onMasukItemChange(idx, this);
            });
        }

        function addMasukRow() {
            const tbody = document.getElementById('tbody-masuk-bulk');
            masukRowCounter++;
            const idx = masukRowCounter;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/40 transition-colors';
            tr.id = `row-masuk-${idx}`;

            let itemOptionsHtml = '<option value=""></option>';
            ALL_ITEMS_MASUK.forEach(it => {
                const stokText = it.stok !== undefined ? ` (Stok: ${it.stok} ${it.satuan || ''})` : '';
                itemOptionsHtml += `<option value="${it.id}"
                    data-harga="${it.harga_dasar}"
                    data-stok="${it.stok}"
                    data-nama="${it.nama}"
                    data-satuan="${it.satuan}">
                    ${it.kode} — ${it.nama}${stokText}
                </option>`;
            });

            tr.innerHTML = `
                <td class="py-3 px-3 text-center font-black text-slate-400 row-number text-base"></td>
                <td class="py-3 px-3" style="min-width:280px">
                    <select name="rows[${idx}][item_id]" required
                        class="w-full select-masuk-item">
                        ${itemOptionsHtml}
                    </select>
                </td>
                <td class="py-3 px-3 text-center">
                    <span id="stok-masuk-badge-${idx}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-slate-100 text-slate-700" data-stok="0">
                        -
                    </span>
                </td>
                <td class="py-3 px-3 text-center">
                    <input type="number" name="rows[${idx}][jumlah]" min="1" value="1" required
                        oninput="calcMasukRow(${idx})"
                        class="w-28 mx-auto text-center rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-lg font-black text-slate-900 bg-white">
                </td>
                <td class="py-3 px-3 text-right">
                    <input type="number" name="rows[${idx}][harga_satuan]" min="0" step="100" value="0" required
                        oninput="calcMasukRow(${idx})"
                        class="w-full text-right rounded-xl border-2 border-slate-300 focus:border-emerald-600 p-2.5 text-lg font-black text-emerald-900 bg-white">
                </td>
                <td class="py-3 px-3 text-right">
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
            // Inisialisasi Select2 pada baris baru
            initMasukRowSelect2(idx);
            reindexMasukRows();
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

        document.addEventListener('DOMContentLoaded', function() {
            // Select2 untuk mode satuan (single)
            if (window.jQuery && $.fn.select2) {
                $('#select-item-masuk').select2({
                    placeholder: '🔍 Cari / Pilih Barang Master...',
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() { return 'Barang tidak ditemukan'; },
                        searching: function() { return 'Mencari...'; }
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

                if (data.id_item && window.jQuery && $.fn.select2) {
                    $('#select-item-masuk').val(data.id_item).trigger('change');
                } else if (data.id_item) {
                    document.getElementById('select-item-masuk').value = data.id_item;
                }

                setVal('input-jumlah-masuk', data.jumlah);
                setVal('input-harga-beli-masuk', data.harga_beli);
                setVal('input-tgl-masuk', data.tgl_masuk || data.tanggal_masuk);
                setVal('input-tgl-kadaluarsa', data.tgl_kadaluarsa || data.tanggal_kadaluarsa);
                setVal('input-pemasok-masuk', data.pemasok_input);
                setVal('input-lokasi-masuk', data.lokasi_input);
                setVal('input-kondisi-masuk', data.kondisi_input);
                setVal('input-catatan-masuk', data.catatan);

                document.querySelectorAll('.hybrid-input').forEach(input => {
                    input.dispatchEvent(new Event('input'));
                });
            }
        });
    </script>

</section>
