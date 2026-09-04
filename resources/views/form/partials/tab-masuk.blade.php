<section id="masuk" class="tab-content" role="tabpanel">

    {{-- Voice Assistant --}}
    <div class="voice-box">
        <div>
            <div class="title">
                <i class="fas fa-robot"></i> Asisten Suara (Barang Masuk)
            </div>
            <div class="desc">
                Tekan tombol mic, lalu sebutkan transaksi barang masuk.<br>
                <em>Contoh:</em> "Masuk Mawar Merah 50 tangkai harga 10 ribu dari Supplier Agromart di Gudang Utama kondisi Segar kadaluarsa 2026-12-31"
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

    {{-- Form Transaksi Barang Masuk --}}
    <form id="form-barang-masuk" action="{{ route('form.barang-masuk.store') }}" method="POST" class="form-card">
        @csrf

        {{-- FORM HEADER --}}
        <div class="form-header">
            <div class="icon-box">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div>
                <h2>Transaksi Barang Masuk</h2>
                <p>Catat penerimaan barang ke dalam inventori.</p>
            </div>
        </div>

        {{-- FORM BODY --}}
        <div class="form-body">

            {{-- SELECT BARANG --}}
            <div class="form-group">
                <label for="select-item-masuk" class="form-label">Pilih Barang Master <span class="required">*</span></label>
                <select name="id_item" id="select-item-masuk" required class="form-control select2-item">
                    <option value="">-- Cari / Pilih Barang Master --</option>
                    @foreach ($items ?? [] as $item)
                        <option value="{{ $item->id }}" data-harga="{{ $item->harga_dasar }}" {{ old('id_item') == $item->id ? 'selected' : '' }}>
                            {{ $item->kode_barang }} — {{ $item->nama_barang }} (Stok: {{ $item->stok }})
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

        {{-- FORM FOOTER --}}
        <div class="form-footer">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Simpan Transaksi Masuk
            </button>
        </div>

    </form>

    {{-- SCRIPT JAVASCRIPT KHUSUS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== SELECT2 INIT =====
            if (window.jQuery && $.fn.select2) {
                $('#select-item-masuk').select2({
                    placeholder: '-- Cari / Pilih Barang Master --',
                    allowClear: true,
                    width: '100%'
                });
            }

            // ===== VOICE ASSISTANT =====
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

            // ===== APPLY AI PARSED DATA =====
            function applyAiParsedData(data) {
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el && val !== null && val !== undefined) el.value = val;
                };

                // Select2 untuk item
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

                // Trigger hybrid dropdown filter
                document.querySelectorAll('.hybrid-input').forEach(input => {
                    input.dispatchEvent(new Event('input'));
                });
            }

            // ===== HYBRID DROPDOWN HANDLER =====
            document.querySelectorAll('.hybrid-select').forEach(wrapper => {
                const input = wrapper.querySelector('.hybrid-input');
                const toggle = wrapper.querySelector('.hybrid-toggle');
                const options = wrapper.querySelector('.hybrid-options');

                if (!input || !options) return;

                const toggleDropdown = () => {
                    options.classList.toggle('show');
                };

                const closeDropdown = () => {
                    options.classList.remove('show');
                };

                const filterOptions = () => {
                    const val = input.value.toLowerCase().trim();
                    let hasVisible = false;
                    options.querySelectorAll('.hybrid-option').forEach(opt => {
                        const text = opt.textContent.toLowerCase();
                        if (text.includes(val)) {
                            opt.style.display = '';
                            hasVisible = true;
                        } else {
                            opt.style.display = 'none';
                        }
                    });
                    if (hasVisible) {
                        options.classList.add('show');
                    } else {
                        options.classList.remove('show');
                    }
                };

                input.addEventListener('focus', () => {
                    options.classList.add('show');
                    filterOptions();
                });

                input.addEventListener('input', filterOptions);

                if (toggle) {
                    toggle.addEventListener('click', (e) => {
                        e.stopPropagation();
                        toggleDropdown();
                        if (options.classList.contains('show')) filterOptions();
                    });
                }

                options.querySelectorAll('.hybrid-option').forEach(opt => {
                    opt.addEventListener('click', function() {
                        input.value = this.dataset.value;
                        closeDropdown();
                        input.dispatchEvent(new Event('input'));
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!wrapper.contains(e.target)) {
                        closeDropdown();
                    }
                });
            });

        });
    </script>

</section>
