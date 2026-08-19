<section id="tab-content-masuk" class="oneshot-tab-content hidden" role="tabpanel">

    {{-- Voice Assistant Section --}}
    <div class="mb-6 rounded-3xl border border-brand-accent bg-emerald-50/60 p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="flex items-center gap-2 text-base font-black text-brand-emerald">
                    <i class="fas fa-robot text-lg text-emerald-600"></i> Asisten Suara (Barang Masuk)
                </h3>
                <p class="mt-1 text-xs leading-relaxed text-brand-slate">
                    Tekan tombol mic, lalu sebutkan transaksi barang masuk.<br>
                    <span class="font-semibold text-emerald-800">Contoh:</span> <em>"Masuk Mawar Merah 50 tangkai harga 10 ribu dari Supplier Agromart di Gudang Utama kondisi Segar kadaluarsa 2026-12-31"</em>
                </p>
            </div>
            <button type="button" id="btn-voice-input-masuk"
                class="inline-flex shrink-0 items-center justify-center gap-3 rounded-2xl bg-brand-emerald px-6 py-4 text-sm font-black text-white shadow-md transition hover:bg-emerald-800 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60">
                <i class="fas fa-microphone text-lg" id="voice-icon-masuk"></i>
                <span id="voice-status-text-masuk">Mulai Bicara</span>
            </button>
        </div>
        <div id="voice-transcript-box-masuk"
            class="mt-3 hidden rounded-xl border border-emerald-200 bg-white p-3 text-xs text-slate-600">
            <span class="font-bold text-emerald-700">Terdengar:</span> <span id="voice-transcript-text-masuk"
                class="italic">...</span>
        </div>
    </div>

    {{-- Form Transaksi Barang Masuk --}}
    <form id="form-barang-masuk" action="{{ route('form.barang-masuk.store') }}" method="POST"
        class="rounded-3xl border border-brand-accent bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <h2 class="mb-6 text-xl font-black text-brand-emerald">Transaksi Barang Masuk</h2>

        <div class="grid gap-5 md:grid-cols-2">

            {{-- Select2 Pilih Barang --}}
            <div class="md:col-span-2">
                <label class="field-label">Pilih Barang Master <span class="text-rose-500">*</span></label>
                <select name="id_item" id="select-item-masuk" required class="select2-item field-input w-full">
                    <option value="">-- Cari / Pilih Barang Master --</option>
                    @foreach ($items ?? [] as $item)
                        <option value="{{ $item->id }}" data-harga="{{ $item->harga_dasar }}" {{ old('id_item') == $item->id ? 'selected' : '' }}>
                            {{ $item->kode_barang }} — {{ $item->nama_barang }} (Stok: {{ $item->stok }})
                        </option>
                    @endforeach
                </select>
                <span class="mt-1 block text-[11px] text-gray-500">*Barang harus terdaftar di Master Item terlebih dahulu.</span>
                @error('id_item')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah Masuk --}}
            <div>
                <label class="field-label">Jumlah Masuk <span class="text-rose-500">*</span></label>
                <input name="jumlah" id="input-jumlah-masuk" type="number" min="1" required class="field-input"
                    placeholder="Contoh: 50" value="{{ old('jumlah') }}">
                @error('jumlah')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Harga Beli Satuan --}}
            <div>
                <label class="field-label">Harga Beli Satuan (Rp) <span class="text-rose-500">*</span></label>
                <input name="harga_beli" id="input-harga-beli-masuk" type="number" min="0" step="0.01" required
                    class="field-input" placeholder="0.00" value="{{ old('harga_beli') }}">
                @error('harga_beli')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Masuk --}}
            <div>
                <label class="field-label">Tanggal Masuk <span class="text-rose-500">*</span></label>
                <input name="tgl_masuk" id="input-tgl-masuk" type="date" required value="{{ old('tgl_masuk', date('Y-m-d')) }}"
                    class="field-input">
                @error('tgl_masuk')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Kadaluarsa --}}
            <div>
                <label class="field-label">Tanggal Kadaluarsa <span class="text-xs font-medium text-gray-400">(Opsional)</span></label>
                <input name="tgl_kadaluarsa" id="input-tgl-kadaluarsa" type="date" value="{{ old('tgl_kadaluarsa') }}"
                    class="field-input">
                @error('tgl_kadaluarsa')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pemasok (Hybrid Select) --}}
            <div>
                <label class="field-label">Pemasok (Pilih atau Ketik Baru) <span class="text-rose-500">*</span></label>
                <div class="relative hybrid-select" id="hybrid-pemasok-masuk">
                    <input type="text" name="pemasok_input" id="input-pemasok-masuk" value="{{ old('pemasok_input') }}"
                        required class="field-input hybrid-input pr-10" placeholder="-- Pilih / Ketik Pemasok --"
                        autocomplete="off">
                    <button type="button"
                        class="hybrid-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-brand-slate">
                        <i class="fas fa-chevron-down pointer-events-none text-xs"></i>
                    </button>
                    <ul
                        class="hybrid-options absolute left-0 right-0 z-50 mt-1 hidden max-h-48 overflow-y-auto rounded-xl border border-brand-accent bg-white py-1 text-brand-slate shadow-lg">
                        @foreach ($pemasoks ?? [] as $item)
                            <li class="hybrid-option cursor-pointer px-4 py-2 text-sm font-medium hover:bg-emerald-50 hover:text-emerald-700"
                                data-value="{{ $item->nama_pemasok }}">{{ $item->nama_pemasok }}</li>
                        @endforeach
                    </ul>
                </div>
                @error('pemasok_input')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Lokasi Penyimpanan (Hybrid Select) --}}
            <div>
                <label class="field-label">Lokasi Penyimpanan (Pilih atau Ketik Baru)</label>
                <div class="relative hybrid-select" id="hybrid-lokasi-masuk">
                    <input type="text" name="lokasi_input" id="input-lokasi-masuk" value="{{ old('lokasi_input') }}"
                        class="field-input hybrid-input pr-10" placeholder="-- Pilih / Ketik Lokasi --"
                        autocomplete="off">
                    <button type="button"
                        class="hybrid-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-brand-slate">
                        <i class="fas fa-chevron-down pointer-events-none text-xs"></i>
                    </button>
                    <ul
                        class="hybrid-options absolute left-0 right-0 z-50 mt-1 hidden max-h-48 overflow-y-auto rounded-xl border border-brand-accent bg-white py-1 text-brand-slate shadow-lg">
                        @foreach ($lokasis ?? [] as $item)
                            <li class="hybrid-option cursor-pointer px-4 py-2 text-sm font-medium hover:bg-emerald-50 hover:text-emerald-700"
                                data-value="{{ $item->nama_lokasi }}">{{ $item->nama_lokasi }}</li>
                        @endforeach
                    </ul>
                </div>
                @error('lokasi_input')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kondisi Barang (Hybrid Select) --}}
            <div class="md:col-span-2">
                <label class="field-label">Kondisi Barang (Pilih atau Ketik Baru)</label>
                <div class="relative hybrid-select" id="hybrid-kondisi-masuk">
                    <input type="text" name="kondisi_input" id="input-kondisi-masuk" value="{{ old('kondisi_input') }}"
                        class="field-input hybrid-input pr-10" placeholder="-- Pilih / Ketik Kondisi --"
                        autocomplete="off">
                    <button type="button"
                        class="hybrid-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-brand-slate">
                        <i class="fas fa-chevron-down pointer-events-none text-xs"></i>
                    </button>
                    <ul
                        class="hybrid-options absolute left-0 right-0 z-50 mt-1 hidden max-h-48 overflow-y-auto rounded-xl border border-brand-accent bg-white py-1 text-brand-slate shadow-lg">
                        @foreach ($kondisis ?? [] as $item)
                            <li class="hybrid-option cursor-pointer px-4 py-2 text-sm font-medium hover:bg-emerald-50 hover:text-emerald-700"
                                data-value="{{ $item->nama_kondisi }}">{{ $item->nama_kondisi }}</li>
                        @endforeach
                    </ul>
                </div>
                @error('kondisi_input')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div class="md:col-span-2">
                <label class="field-label">Catatan Transaksi</label>
                <textarea name="catatan" id="input-catatan-masuk" rows="3" class="field-input"
                    placeholder="Catatan tambahan transaksi barang masuk">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit"
            class="mt-6 rounded-xl bg-brand-emerald px-5 py-3 text-sm font-black text-white transition hover:bg-brand-slate">
            <i class="fas fa-save mr-2"></i>Simpan Transaksi Masuk
        </button>
    </form>

    {{-- Script Voice Assistant & Hybrid Select Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 jika jQuery & Select2 tersedia
            if (window.jQuery && $.fn.select2) {
                $('#select-item-masuk').select2({
                    placeholder: '-- Cari / Pilih Barang Master --',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Handler untuk Hybrid Select dengan Live Filter Saat Mengetik
            document.querySelectorAll('.hybrid-select').forEach(wrapper => {
                const input = wrapper.querySelector('.hybrid-input');
                const toggleBtn = wrapper.querySelector('.hybrid-toggle');
                const optionsList = wrapper.querySelector('.hybrid-options');
                const options = wrapper.querySelectorAll('.hybrid-option');

                const toggleDropdown = () => optionsList.classList.toggle('hidden');

                if (toggleBtn) toggleBtn.addEventListener('click', toggleDropdown);

                if (input) {
                    // Tampilkan dropdown & reset visibilitas semua opsi saat fokus
                    input.addEventListener('focus', () => {
                        optionsList.classList.remove('hidden');
                        options.forEach(opt => opt.style.display = '');
                    });

                    // Live Filter berdasarkan karakter yang diketik
                    input.addEventListener('input', function() {
                        const filter = this.value.toLowerCase().trim();
                        optionsList.classList.remove('hidden');

                        options.forEach(opt => {
                            const text = opt.textContent.toLowerCase();
                            opt.style.display = text.includes(filter) ? '' : 'none';
                        });
                    });
                }

                // Pilih opsi saat diklik
                options.forEach(option => {
                    option.addEventListener('click', function() {
                        input.value = this.dataset.value;
                        optionsList.classList.add('hidden');
                    });
                });
            });

            // Tutup semua Hybrid Dropdown ketika klik di luar elemen
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.hybrid-select')) {
                    document.querySelectorAll('.hybrid-options').forEach(el => el.classList.add('hidden'));
                }
            });

            // Voice Assistant Handler
            const btnVoiceMasuk = document.getElementById('btn-voice-input-masuk');
            const voiceIconMasuk = document.getElementById('voice-icon-masuk');
            const voiceStatusTextMasuk = document.getElementById('voice-status-text-masuk');
            const transcriptBoxMasuk = document.getElementById('voice-transcript-box-masuk');
            const transcriptTextMasuk = document.getElementById('voice-transcript-text-masuk');

            if (!btnVoiceMasuk) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            let mediaRecorderMasuk = null;
            let audioChunksMasuk = [];
            let isRecordingMasuk = false;

            function updateUIMasuk(status, message = '') {
                if (status === 'recording') {
                    btnVoiceMasuk.disabled = false;
                    btnVoiceMasuk.classList.remove('bg-brand-emerald', 'bg-amber-600');
                    btnVoiceMasuk.classList.add('bg-rose-600', 'animate-pulse');
                    if (voiceIconMasuk) voiceIconMasuk.className = 'fas fa-stop text-lg';
                    if (voiceStatusTextMasuk) voiceStatusTextMasuk.textContent = 'Merekam... (Klik Stop)';
                    if (transcriptBoxMasuk) transcriptBoxMasuk.classList.remove('hidden');
                    if (transcriptTextMasuk) transcriptTextMasuk.textContent = 'Sedang merekam suara Anda...';
                } else if (status === 'processing') {
                    btnVoiceMasuk.disabled = true;
                    btnVoiceMasuk.classList.remove('bg-rose-600', 'animate-pulse', 'bg-brand-emerald');
                    btnVoiceMasuk.classList.add('bg-amber-600');
                    if (voiceIconMasuk) voiceIconMasuk.className = 'fas fa-spinner fa-spin text-lg';
                    if (voiceStatusTextMasuk) voiceStatusTextMasuk.textContent = message || 'AI Mendengarkan...';
                    if (transcriptTextMasuk) transcriptTextMasuk.textContent = 'Mengirim rekaman suara ke AI...';
                } else {
                    btnVoiceMasuk.disabled = false;
                    btnVoiceMasuk.classList.remove('bg-rose-600', 'animate-pulse', 'bg-amber-600');
                    btnVoiceMasuk.classList.add('bg-brand-emerald');
                    if (voiceIconMasuk) voiceIconMasuk.className = 'fas fa-microphone text-lg';
                    if (voiceStatusTextMasuk) voiceStatusTextMasuk.textContent = message || 'Mulai Bicara';
                }
            }

            async function startRecordingMasuk() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorderMasuk = new MediaRecorder(stream);
                    audioChunksMasuk = [];

                    mediaRecorderMasuk.ondataavailable = event => {
                        if (event.data.size > 0) audioChunksMasuk.push(event.data);
                    };

                    mediaRecorderMasuk.onstop = async () => {
                        stream.getTracks().forEach(track => track.stop());
                        const audioBlob = new Blob(audioChunksMasuk, { type: 'audio/webm' });
                        await sendAudioToAIMasuk(audioBlob);
                    };

                    mediaRecorderMasuk.start();
                    isRecordingMasuk = true;
                    updateUIMasuk('recording');
                } catch (err) {
                    console.error('Microphone error:', err);
                    updateUIMasuk('idle', 'Akses Mic Ditolak');
                    alert('Gagal mengakses mikrofon. Pastikan izin mikrofon telah diberikan.');
                }
            }

            function stopRecordingMasuk() {
                if (mediaRecorderMasuk && isRecordingMasuk) {
                    mediaRecorderMasuk.stop();
                    isRecordingMasuk = false;
                    updateUIMasuk('processing', 'Menganalisis Suara...');
                }
            }

            async function sendAudioToAIMasuk(blob) {
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

                    if (result.transcript_raw && transcriptTextMasuk) {
                        transcriptTextMasuk.textContent = `"${result.transcript_raw}"`;
                    }

                    const d = result.data;

                    if (d.item_found && d.id_item) {
                        if (window.jQuery && $.fn.select2) {
                            $('#select-item-masuk').val(d.id_item).trigger('change');
                        } else {
                            document.getElementById('select-item-masuk').value = d.id_item;
                        }
                    } else {
                        if (window.jQuery && $.fn.select2) {
                            $('#select-item-masuk').val('').trigger('change');
                        }
                        const searchedName = d.nama_barang || 'tersebut';

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Barang Tidak Ditemukan!',
                                html: `Barang <b>"${searchedName}"</b> tidak ditemukan pada database Master Item.<br><br>Silakan ulangi ucapan Anda atau tambahkan barang tersebut terlebih dahulu di menu <b>Master Item</b>.`,
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#059669'
                            });
                        } else {
                            alert(`Barang "${searchedName}" tidak ditemukan di database Master Item.`);
                        }
                    }

                    const setVal = (id, val) => {
                        const el = document.getElementById(id);
                        if (el && val !== null && val !== undefined) el.value = val;
                    };

                    setVal('input-jumlah-masuk', d.jumlah);
                    setVal('input-harga-beli-masuk', d.harga_beli);
                    setVal('input-tgl-masuk', d.tgl_masuk || d.tanggal_masuk);
                    setVal('input-tgl-kadaluarsa', d.tgl_kadaluarsa || d.tanggal_kadaluarsa);
                    setVal('input-pemasok-masuk', d.pemasok_input);
                    setVal('input-lokasi-masuk', d.lokasi_input || 'Gudang Utama');
                    setVal('input-kondisi-masuk', d.kondisi_input || 'Segar');
                    setVal('input-catatan-masuk', d.catatan);

                    updateUIMasuk('idle', d.item_found ? 'Selesai! Form Terisi' : 'Barang Tak Ditemukan');

                } catch (err) {
                    console.error('Audio processing error:', err);
                    updateUIMasuk('idle', 'Gagal Memproses');
                    if (transcriptTextMasuk) transcriptTextMasuk.textContent = `Error: ${err.message}`;
                }
            }

            btnVoiceMasuk.addEventListener('click', function() {
                if (!isRecordingMasuk) {
                    startRecordingMasuk();
                } else {
                    stopRecordingMasuk();
                }
            });
        });
    </script>
</section>
