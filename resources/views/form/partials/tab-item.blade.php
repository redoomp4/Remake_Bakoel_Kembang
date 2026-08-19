<section id="tab-content-item" class="oneshot-tab-content" role="tabpanel">

    {{-- Voice Assistant Section --}}
    <div class="mb-6 rounded-3xl border border-brand-accent bg-emerald-50/60 p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-black text-brand-emerald flex items-center gap-2">
                    <i class="fas fa-robot text-lg text-emerald-600"></i> Asisten Suara (Bantu Isian)
                </h3>
                <p class="mt-1 text-xs text-brand-slate leading-relaxed">
                    Tekan tombol mic, lalu sebutkan detail barang secara bebas.<br>
                    <span class="font-semibold text-emerald-800">Contoh:</span> <em>"Tambah item Cattleya Mantini,
                        kategori Bunga Tangkai, satuan Ikat, stok 30, harga 5 ribu, deskripsi ini bunga impor"</em>
                </p>
            </div>
            <button type="button" id="btn-voice-input"
                class="inline-flex shrink-0 items-center justify-center gap-3 rounded-2xl bg-brand-emerald px-6 py-4 text-sm font-black text-white shadow-md transition hover:bg-emerald-800 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                <i class="fas fa-microphone text-lg" id="voice-icon"></i>
                <span id="voice-status-text">Mulai Bicara</span>
            </button>
        </div>
        <div id="voice-transcript-box"
            class="mt-3 hidden rounded-xl bg-white p-3 border border-emerald-200 text-xs text-slate-600">
            <span class="font-bold text-emerald-700">Terdengar:</span> <span id="voice-transcript-text"
                class="italic">...</span>
        </div>
    </div>

    {{-- Form Tambah Item --}}
    <form id="form-tambah-item" action="{{ route('form.item.store') }}" method="POST" enctype="multipart/form-data"
        class="rounded-3xl border border-brand-accent bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <h2 class="mb-6 text-xl font-black text-brand-emerald">Tambah Item Baru</h2>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="field-label">Nama Barang</label>
                <input name="nama_barang" id="input-nama-barang" required class="field-input"
                    placeholder="Masukkan nama barang">
            </div>
            <div>
                <label class="field-label">Kategori (Pilih atau Ketik Baru)</label>
                <div class="relative hybrid-select" id="hybrid-kategori">
                    <input type="text" name="kategori_input" id="kategori_input" value="{{ old('kategori_input') }}"
                        required class="field-input pr-10 hybrid-input" placeholder="-- Pilih / Ketik Kategori --"
                        autocomplete="off">
                    <button type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-brand-slate hybrid-toggle">
                        <i class="fas fa-chevron-down text-xs pointer-events-none"></i>
                    </button>
                    <ul
                        class="hybrid-options hidden absolute z-50 left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-xl border border-brand-accent bg-white py-1 shadow-lg text-brand-slate">
                        @foreach ($kategories ?? [] as $item)
                            <li class="hybrid-option cursor-pointer px-4 py-2 text-sm hover:bg-emerald-50 hover:text-emerald-700 font-medium"
                                data-value="{{ $item->kategori }}">{{ $item->kategori }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div>
                <label class="field-label">Satuan (Pilih atau Ketik Baru)</label>
                <div class="relative hybrid-select" id="hybrid-satuan">
                    <input type="text" name="satuan_input" id="satuan_input" value="{{ old('satuan_input') }}"
                        required class="field-input pr-10 hybrid-input" placeholder="-- Pilih / Ketik Satuan --"
                        autocomplete="off">
                    <button type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-brand-slate hybrid-toggle">
                        <i class="fas fa-chevron-down text-xs pointer-events-none"></i>
                    </button>
                    <ul
                        class="hybrid-options hidden absolute z-50 left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-xl border border-brand-accent bg-white py-1 shadow-lg text-brand-slate">
                        @foreach ($satuans ?? [] as $item)
                            <li class="hybrid-option cursor-pointer px-4 py-2 text-sm hover:bg-emerald-50 hover:text-emerald-700 font-medium"
                                data-value="{{ $item->nama_satuan }}">{{ $item->nama_satuan }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div>
                <label class="field-label">Stok Minimum</label>
                <input name="stok_minimum" id="input-stok-minimum" type="number" min="0" required
                    class="field-input" value="0">
            </div>
            <div>
                <label class="field-label">Harga Dasar (Rp)</label>
                <input name="harga_dasar" id="input-harga-dasar" type="number" min="0" step="0.01" required
                    class="field-input" placeholder="0.00">
            </div>
            <div>
                <label class="field-label">Foto Item</label>
                <input name="foto" type="file" accept="image/*" class="field-input">
            </div>
            <div class="md:col-span-2">
                <label class="field-label">Deskripsi Item</label>
                <textarea name="deskripsi" id="input-deskripsi" rows="3" class="field-input"
                    placeholder="Catatan/deskripsi tambahan item"></textarea>
            </div>
        </div>
        <button type="submit"
            class="mt-6 rounded-xl bg-brand-emerald px-5 py-3 text-sm font-black text-white transition hover:bg-brand-slate">
            <i class="fas fa-save mr-2"></i>Simpan Item
        </button>
    </form>

    {{-- Script Voice Assistant --}}
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
                if (status === 'recording') {
                    btnVoice.disabled = false;
                    btnVoice.classList.remove('bg-brand-emerald', 'bg-amber-600');
                    btnVoice.classList.add('bg-rose-600', 'animate-pulse');
                    if (voiceIcon) voiceIcon.className = 'fas fa-stop text-lg';
                    if (voiceStatusText) voiceStatusText.textContent = 'Merekam... (Klik Stop)';
                    if (transcriptBox) transcriptBox.classList.remove('hidden');
                    if (transcriptText) transcriptText.textContent = 'Sedang merekam suara Anda...';
                } else if (status === 'processing') {
                    btnVoice.disabled = true;
                    btnVoice.classList.remove('bg-rose-600', 'animate-pulse', 'bg-brand-emerald');
                    btnVoice.classList.add('bg-amber-600');
                    if (voiceIcon) voiceIcon.className = 'fas fa-spinner fa-spin text-lg';
                    if (voiceStatusText) voiceStatusText.textContent = message || 'AI Mendengarkan...';
                    if (transcriptText) transcriptText.textContent = 'Mengirim rekaman suara ke AI...';
                } else { // idle
                    btnVoice.disabled = false;
                    btnVoice.classList.remove('bg-rose-600', 'animate-pulse', 'bg-amber-600');
                    btnVoice.classList.add('bg-brand-emerald');
                    if (voiceIcon) voiceIcon.className = 'fas fa-microphone text-lg';
                    if (voiceStatusText) voiceStatusText.textContent = message || 'Mulai Bicara';
                }
            }

            async function startRecording() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];

                    mediaRecorder.ondataavailable = event => {
                        if (event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onstop = async () => {
                        // Matikan stream mikrofon setelah selesai
                        stream.getTracks().forEach(track => track.stop());

                        const audioBlob = new Blob(audioChunks, {
                            type: 'audio/webm'
                        });
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

                    // Isi Form
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

                    updateUI('idle', 'Selesai! Form Terisi');
                    if (transcriptText) transcriptText.textContent = 'Form berhasil diisi oleh AI!';

                    if (typeof window.toast === 'function') {
                        window.toast('Form berhasil terisi dari suara!', 'success');
                    }

                } catch (err) {
                    console.error('Audio processing error:', err);
                    updateUI('idle', 'Gagal Memproses');
                    if (transcriptText) transcriptText.textContent = `Error: ${err.message}`;
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
