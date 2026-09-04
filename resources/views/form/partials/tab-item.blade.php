<section id="item" class="tab-content active" role="tabpanel">

    {{-- Voice Assistant --}}
    <div class="voice-box">
        <div>
            <div class="title">
                <i class="fas fa-robot"></i> Asisten Suara (Bantu Isian)
            </div>
            <div class="desc">
                Tekan tombol mic, lalu sebutkan detail barang secara bebas.<br>
                <em>Contoh:</em> "Tambah item Cattleya Mantini, kategori Bunga Tangkai, satuan Ikat, stok 30, harga 5 ribu, deskripsi ini bunga impor"
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

    {{-- Form Tambah Item --}}
    <form id="form-tambah-item" action="{{ route('form.item.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf

        {{-- FORM HEADER --}}
        <div class="form-header">
            <div class="icon-box">
                <i class="fas fa-box"></i>
            </div>
            <div>
                <h2>Tambah Item Baru</h2>
                <p>Masukkan detail item yang akan ditambahkan ke inventori.</p>
            </div>
        </div>

        {{-- FORM BODY --}}
        <div class="form-body">

            {{-- NAMA BARANG --}}
            <div class="form-group">
                <label for="input-nama-barang" class="form-label">Nama Barang <span class="required">*</span></label>
                <input type="text" name="nama_barang" id="input-nama-barang" required class="form-control" placeholder="Masukkan nama barang">
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
                    <label for="input-harga-dasar" class="form-label">Harga Dasar (Rp) <span class="required">*</span></label>
                    <input type="number" name="harga_dasar" id="input-harga-dasar" min="0" step="0.01" required class="form-control" placeholder="0.00">
                    @error('harga_dasar')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- FOTO --}}
            <div class="form-group">
                <label for="input-foto" class="form-label">Foto Item</label>
                <input type="file" name="foto" id="input-foto" accept="image/*" class="form-control">
                <div class="form-hint">Upload foto item (opsional, format: JPG, PNG, WebP)</div>
                @error('foto')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- DESKRIPSI --}}
            <div class="form-group" style="margin-bottom:0;">
                <label for="input-deskripsi" class="form-label">Deskripsi Item</label>
                <textarea name="deskripsi" id="input-deskripsi" rows="3" class="form-control" placeholder="Catatan/deskripsi tambahan item">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- FORM FOOTER --}}
        <div class="form-footer">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Simpan Item
            </button>
        </div>

    </form>

    {{-- SCRIPT VOICE ASSISTANT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnVoice = document.getElementById('btn-voice-input');
            const voiceIcon = document.getElementById('voice-icon');
            const voiceStatusText = document.getElementById('voice-status-text');
            const transcriptBox = document.getElementById('voice-transcript-box');
            const transcriptText = document.getElementById('voice-transcript-text');

            if (!btnVoice) return;

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
        });
    </script>

</section>
