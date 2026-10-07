<section id="item" class="tab-content active" role="tabpanel">

    {{-- SWITCHER MODE INPUT (SATUAN vs MASSAL) --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border-2 border-emerald-200 bg-white p-3 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 font-black">
                <i class="fas fa-layer-group text-lg"></i>
            </span>
            <div>
                <h3 class="text-base font-black text-emerald-950">Mode Pengisian Master Item</h3>
                <p class="text-xs text-slate-500">Pilih ingin isi 1 per 1 atau langsung banyak baris (Tabel)</p>
            </div>
        </div>
        <div class="flex rounded-xl bg-slate-100 p-1">
            <button type="button" id="btn-mode-item-single" onclick="switchItemMode('single')"
                class="mode-switch-btn flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-black transition-all bg-white text-emerald-800 shadow-sm">
                <i class="fas fa-keyboard"></i> Satu per Satu / Suara
            </button>
            <button type="button" id="btn-mode-item-bulk" onclick="switchItemMode('bulk')"
                class="mode-switch-btn flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-black transition-all text-slate-600 hover:text-emerald-800">
                <i class="fas fa-table"></i> Input Massal (Tabel)
            </button>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 1. MODE SATU PER SATU / SUARA --}}
    {{-- ======================================================== --}}
    <div id="container-item-single">
        {{-- Voice Assistant --}}
        <div class="voice-box">
            <div>
                <div class="title">
                    <i class="fas fa-robot"></i> Asisten Suara (Bantu Isian)
                </div>
                <div class="desc">
                    Tekan tombol mic, lalu sebutkan detail barang secara bebas.<br>
                    <em>Contoh:</em> "Tambah item Cattleya Mantini, kategori Bunga Tangkai, satuan Ikat, stok minimum 10, harga 5 ribu"
                </div>
            </div>
            <button type="button" id="btn-voice-input" class="btn-voice">
                <i class="fas fa-microphone" id="voice-icon"></i>
                <span id="voice-status-text">Mulai Bicara</span>
            </button>
            <div id="voice-transcript-box" class="voice-transcript">
                <span class="label">Terdengar:</span> <span id="voice-transcript-text" class="italic">...</span>
            </div>
        </div>

        {{-- Form Tambah Item Satuan --}}
        <form id="form-tambah-item" action="{{ route('form.item.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf

            <div class="form-header">
                <div class="icon-box">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <h2>Tambah Item Baru (Satuan)</h2>
                    <p>Masukkan detail satu item yang akan didaftarkan ke katalog.</p>
                </div>
            </div>

            <div class="form-body">
                {{-- NAMA BARANG --}}
                <div class="form-group">
                    <label for="input-nama-barang" class="form-label">Nama Barang / Bunga <span class="required">*</span></label>
                    <input type="text" name="nama_barang" id="input-nama-barang" required class="form-control text-base" placeholder="Contoh: Mawar Merah Holland Super">
                    @error('nama_barang')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KATEGORI + SATUAN --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="kategori_input" class="form-label">Kategori <span class="required">*</span></label>
                        <div class="hybrid-select" id="hybrid-kategori">
                            <input type="text" name="kategori_input" id="kategori_input" value="{{ old('kategori_input') }}"
                                   required class="hybrid-input" placeholder="-- Pilih / Ketik Kategori --" autocomplete="off">
                            <button type="button" class="hybrid-toggle">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="hybrid-options">
                                @foreach ($kategories ?? [] as $item)
                                    <li class="hybrid-option" data-value="{{ $item->kategori }}">{{ $item->kategori }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @error('kategori_input')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="satuan_input" class="form-label">Satuan <span class="required">*</span></label>
                        <div class="hybrid-select" id="hybrid-satuan">
                            <input type="text" name="satuan_input" id="satuan_input" value="{{ old('satuan_input') }}"
                                   required class="hybrid-input" placeholder="-- Pilih / Ketik Satuan --" autocomplete="off">
                            <button type="button" class="hybrid-toggle">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="hybrid-options">
                                @foreach ($satuans ?? [] as $item)
                                    <li class="hybrid-option" data-value="{{ $item->nama_satuan }}">{{ $item->nama_satuan }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @error('satuan_input')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- STOK MINIMUM + HARGA DASAR --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="input-stok-minimum" class="form-label">Stok Minimum <span class="required">*</span></label>
                        <input type="number" name="stok_minimum" id="input-stok-minimum" min="0" required class="form-control" value="{{ old('stok_minimum', 0) }}">
                        @error('stok_minimum')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="input-harga-dasar" class="form-label">Estimasi Harga Dasar (Rp) <span class="required">*</span></label>
                        <input type="number" name="harga_dasar" id="input-harga-dasar" min="0" step="0.01" required class="form-control" placeholder="0" value="{{ old('harga_dasar', 0) }}">
                        @error('harga_dasar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- FOTO --}}
                <div class="form-group">
                    <label for="input-foto" class="form-label">Foto Item (Opsional)</label>
                    <input type="file" name="foto" id="input-foto" accept="image/*" class="form-control">
                    <div class="form-hint">Format: JPG, PNG, WebP (Maks 2MB)</div>
                    @error('foto')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DESKRIPSI --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label for="input-deskripsi" class="form-label">Deskripsi / Catatan Tambahan</label>
                    <textarea name="deskripsi" id="input-deskripsi" rows="2" class="form-control" placeholder="Catatan opsional">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit text-base py-3 px-6">
                    <i class="fas fa-save text-lg"></i> Simpan Item
                </button>
            </div>
        </form>
    </div>

    {{-- ======================================================== --}}
    {{-- 2. MODE INPUT MASSAL (TABEL SPREADSHEET STYLE)           --}}
    {{-- ======================================================== --}}
    <div id="container-item-bulk" class="hidden">
        <form id="form-item-bulk" action="{{ route('form.item.store-bulk') }}" method="POST" class="form-card">
            @csrf

            {{-- HEADER PENJELASAN RAMAH IBU-IBU --}}
            <div class="border-b border-emerald-100 bg-emerald-50/70 p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-200/80 px-3 py-1 text-xs font-black text-emerald-900 mb-2">
                            <i class="fas fa-sparkles"></i> Mode Cepat Tabel
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black text-emerald-950">Input Massal Katalog Bunga Baru</h2>
                        <p class="mt-1 text-sm text-slate-600">Ketik nama bunga dan satuan baris demi baris seperti di buku tulis. Cepat dan praktis!</p>
                    </div>
                    <button type="button" onclick="addItemRow()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 text-base font-black shadow transition-all transform active:scale-95">
                        <i class="fas fa-plus-circle text-lg"></i> Tambah Baris Bunga
                    </button>
                </div>
            </div>

            {{-- TABEL SPREADSHEET --}}
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto rounded-2xl border-2 border-emerald-100 bg-white">
                    <table class="w-full text-left border-collapse min-w-[750px]" id="table-item-bulk">
                        <thead>
                            <tr class="bg-emerald-900 text-white text-sm font-black uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4 min-w-[220px]">Nama Barang / Bunga <span class="text-rose-300">*</span></th>
                                <th class="py-3.5 px-4 w-48">Kategori</th>
                                <th class="py-3.5 px-4 w-40">Satuan</th>
                                <th class="py-3.5 px-4 w-32 text-center">Stok Min</th>
                                <th class="py-3.5 px-3 text-center w-14">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-item-bulk" class="divide-y divide-slate-200 text-slate-800">
                            {{-- Baris awal diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>

                {{-- PETUNJUK & QUICK BUTTON --}}
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 italic">
                        <i class="fas fa-info-circle text-emerald-600"></i> Baris yang nama barangnya kosong akan otomatis diabaikan.
                    </p>
                    <button type="button" onclick="addItemRow()"
                        class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-emerald-600 bg-emerald-50/50 hover:bg-emerald-100 px-4 py-2.5 text-sm font-black text-emerald-800 transition">
                        <i class="fas fa-plus"></i> + Tambah Baris Baru
                    </button>
                </div>
            </div>

            {{-- FOOTER SIMPAN --}}
            <div class="form-footer flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-slate-50 border-t border-slate-200">
                <div class="text-sm font-bold text-slate-600">
                    Total Baris Siap Disimpan: <span id="item-bulk-count" class="font-black text-emerald-800 text-lg">0</span> item
                </div>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white px-8 py-4 text-lg font-black shadow-lg shadow-emerald-900/20 transition-all transform active:scale-95">
                    <i class="fas fa-save text-xl"></i> Simpan Semua Bunga Baru
                </button>
            </div>
        </form>
    </div>

    {{-- SCRIPT JAVASCRIPT KHUSUS MASTER ITEM --}}
    <script>
        // Data Options dari Server
        const ITEM_KATEGORIES = @json($kategoriesJson ?? []);
        const ITEM_SATUANS    = @json($satuansJson ?? []);

        // Toggle Switch Mode
        function switchItemMode(mode) {
            const btnSingle = document.getElementById('btn-mode-item-single');
            const btnBulk   = document.getElementById('btn-mode-item-bulk');
            const contSingle = document.getElementById('container-item-single');
            const contBulk   = document.getElementById('container-item-bulk');

            if (mode === 'bulk') {
                btnBulk.classList.add('bg-white', 'text-emerald-800', 'shadow-sm');
                btnBulk.classList.remove('text-slate-600');
                btnSingle.classList.remove('bg-white', 'text-emerald-800', 'shadow-sm');
                btnSingle.classList.add('text-slate-600');

                contSingle.classList.add('hidden');
                contBulk.classList.remove('hidden');

                // Jika tabel masih kosong, isi 3 baris pertama
                if (document.getElementById('tbody-item-bulk').children.length === 0) {
                    for (let i = 0; i < 3; i++) addItemRow();
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

        let itemRowCounter = 0;

        function addItemRow() {
            const tbody = document.getElementById('tbody-item-bulk');
            itemRowCounter++;
            const idx = itemRowCounter;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/40 transition-colors';
            tr.id = `row-item-${idx}`;

            // Kategori Datalist Options
            const kategoriDatalistId = `datalist-kat-${idx}`;
            const satuanDatalistId   = `datalist-sat-${idx}`;

            let katOptions = ITEM_KATEGORIES.map(k => `<option value="${k}">`).join('');
            let satOptions = ITEM_SATUANS.map(s => `<option value="${s}">`).join('');

            tr.innerHTML = `
                <td class="py-3 px-3 text-center font-black text-slate-400 row-number text-base"></td>
                <td class="py-3 px-3">
                    <input type="text" name="items[${idx}][nama_barang]" required
                        placeholder="Ketik Nama Bunga / Barang"
                        class="w-full rounded-xl border-2 border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 p-2.5 text-base font-bold text-slate-900 bg-white shadow-sm"
                        oninput="updateItemBulkCount()">
                </td>
                <td class="py-3 px-3">
                    <input type="text" name="items[${idx}][kategori_input]" list="${kategoriDatalistId}"
                        placeholder="Pilih/Ketik" value="Bunga & Tanaman"
                        class="w-full rounded-xl border border-slate-300 focus:border-emerald-600 p-2 text-sm font-bold text-slate-800 bg-slate-50">
                    <datalist id="${kategoriDatalistId}">
                        ${katOptions}
                    </datalist>
                </td>
                <td class="py-3 px-3">
                    <input type="text" name="items[${idx}][satuan_input]" list="${satuanDatalistId}"
                        placeholder="Pcs/Tangkai/Pot" value="Pcs"
                        class="w-full rounded-xl border border-slate-300 focus:border-emerald-600 p-2 text-sm font-bold text-slate-800 bg-slate-50">
                    <datalist id="${satuanDatalistId}">
                        ${satOptions}
                    </datalist>
                </td>
                <td class="py-3 px-3 text-center">
                    <input type="number" name="items[${idx}][stok_minimum]" min="0" value="0"
                        class="w-20 mx-auto text-center rounded-xl border border-slate-300 focus:border-emerald-600 p-2 text-base font-bold text-slate-800">
                </td>
                <td class="py-3 px-2 text-center">
                    <button type="button" onclick="removeItemRow('${idx}')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-rose-500 hover:bg-rose-100 hover:text-rose-700 transition"
                        title="Hapus Baris Ini">
                        <i class="fas fa-trash-alt text-base"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
            reindexItemRows();
            updateItemBulkCount();
        }

        function removeItemRow(idx) {
            const row = document.getElementById(`row-item-${idx}`);
            if (row) {
                row.remove();
                reindexItemRows();
                updateItemBulkCount();
            }
        }

        function reindexItemRows() {
            const rows = document.querySelectorAll('#tbody-item-bulk tr');
            rows.forEach((r, i) => {
                const numCell = r.querySelector('.row-number');
                if (numCell) numCell.textContent = i + 1;
            });
        }

        function updateItemBulkCount() {
            const inputs = document.querySelectorAll('#tbody-item-bulk input[name*="[nama_barang]"]');
            let filled = 0;
            inputs.forEach(inp => {
                if (inp.value.trim() !== '') filled++;
            });
            const badge = document.getElementById('item-bulk-count');
            if (badge) badge.textContent = filled;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Voice Assistant Handler Satuan
            const btnVoice = document.getElementById('btn-voice-input');
            const voiceIcon = document.getElementById('voice-icon');
            const voiceStatusText = document.getElementById('voice-status-text');
            const transcriptBox = document.getElementById('voice-transcript-box');
            const transcriptText = document.getElementById('voice-transcript-text');

            if (btnVoice) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                let mediaRecorder = null;
                let audioChunks = [];
                let isRecording = false;

                function updateUI(status, message = '') {
                    btnVoice.disabled = false;
                    btnVoice.classList.remove('recording', 'processing');

                    if (status === 'recording') {
                        btnVoice.classList.add('recording');
                        if (voiceIcon) voiceIcon.className = 'fas fa-stop';
                        if (voiceStatusText) voiceStatusText.textContent = 'Rekam... (Stop)';
                        if (transcriptBox) {
                            transcriptBox.classList.add('show');
                            transcriptBox.style.display = 'block';
                        }
                        if (transcriptText) transcriptText.textContent = 'Sedang merekam suara Anda...';
                    } else if (status === 'processing') {
                        btnVoice.disabled = true;
                        btnVoice.classList.add('processing');
                        if (voiceIcon) voiceIcon.className = 'fas fa-spinner fa-spin';
                        if (voiceStatusText) voiceStatusText.textContent = message || 'AI Mendengarkan...';
                        if (transcriptText) transcriptText.textContent = 'Mengirim rekaman suara ke AI...';
                    } else {
                        btnVoice.classList.remove('recording', 'processing');
                        if (voiceIcon) voiceIcon.className = 'fas fa-microphone';
                        if (voiceStatusText) voiceStatusText.textContent = message || 'Mulai Bicara';
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
                        updateUI('recording');

                    } catch (err) {
                        console.error('Microphone error:', err);
                        updateUI('idle', 'Akses Mic Ditolak');
                        alert('Gagal mengakses mikrofon. Pastikan izin mikrofon telah diberikan.');
                    }
                }

                function stopRecording() {
                    if (mediaRecorder && isRecording) {
                        mediaRecorder.stop();
                        isRecording = false;
                        updateUI('processing', 'Menganalisis Suara...');
                    }
                }

                async function sendAudioToAI(blob) {
                    const formData = new FormData();
                    formData.append('audio', blob, 'recording.webm');

                    try {
                        const response = await fetch('{{ route('form.parse-voice') }}', {
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

                        const d = result.data;
                        const setVal = (id, val) => {
                            const el = document.getElementById(id);
                            if (el && val !== null && val !== undefined) el.value = val;
                        };

                        setVal('input-nama-barang', d.nama_barang);
                        setVal('kategori_input', d.kategori_input);
                        setVal('satuan_input', d.satuan_input);
                        setVal('input-stok-minimum', d.stok_minimum);
                        setVal('input-harga-dasar', d.harga_dasar);
                        setVal('input-deskripsi', d.deskripsi);

                        if (transcriptText) transcriptText.textContent = 'Form berhasil diisi oleh AI!';
                        updateUI('idle', 'Selesai! Form Terisi');

                        if (typeof window.toast === 'function') {
                            window.toast('Form berhasil terisi dari suara!', 'success');
                        }

                    } catch (err) {
                        console.error('Audio processing error:', err);
                        updateUI('idle', 'Gagal Memproses');
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
        });
    </script>

</section>
