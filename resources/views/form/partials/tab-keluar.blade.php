<section id="keluar" class="tab-content" role="tabpanel">

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

    {{-- Form Barang Keluar --}}
    <form method="POST" action="{{ route('form.barang-keluar.store') }}" onsubmit="return confirmSimpanKeluar();" class="form-card">
        @csrf

        {{-- FORM HEADER --}}
        <div class="form-header">
            <div class="icon-box">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div>
                <h2>Informasi Barang Keluar</h2>
                <p>Pilih barang dan masukkan detail pengeluaran.</p>
            </div>
        </div>

        {{-- FORM BODY --}}
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
                    <input type="text" name="lokasi_tujuan" id="lokasi_tujuan" value="{{ old('lokasi_tujuan') }}" required class="form-control" placeholder="Contoh: Gudang A / Toko Cabang / Pelanggan">
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

        {{-- FORM FOOTER --}}
        <div class="form-footer">
            <button type="submit" id="submitBtn" class="btn-submit">
                <i class="fas fa-save"></i> Simpan Barang Keluar
            </button>
        </div>

    </form>

    {{-- SCRIPT JAVASCRIPT KHUSUS --}}
    <script>
        (function() {
            let mediaRecorder = null;
            let audioChunks = [];
            let isRecording = false;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // ===== HELPER FUNCTIONS =====
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

            // ===== VOICE ASSISTANT =====
            const btnVoice = document.getElementById('btn-voice-keluar');
            const voiceIcon = document.getElementById('voice-icon-keluar');
            const voiceStatus = document.getElementById('voice-status-keluar');
            const transcriptBox = document.getElementById('voice-transcript-keluar');
            const transcriptText = document.getElementById('voice-text-keluar');

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

            // ===== LOAD BARANG =====
            async function loadPilihanBarang() {
                try {
                    const response = await fetch("{{ route('barang-keluar.pilihan-barang') }}");
                    if (!response.ok) throw new Error('Gagal mengambil data barang.');

                    const data = await response.json();
                    const select = document.getElementById('kode_lokasi_kondisi');
                    if (!select) return;

                    select.innerHTML = '<option value="" disabled selected>-- Pilih Barang --</option>';

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.dataset.itemId = item.item_id;
                        option.value = `${item.kode}|${item.lokasi_id}|${item.kondisi_id}`;
                        option.text = `${item.kode} - ${item.nama_barang} - ${item.lokasi} - ${item.kondisi} (Stok: ${item.stok})`;
                        option.dataset.nama = item.nama_barang ?? '';
                        option.dataset.satuan = item.satuan ?? '';
                        option.dataset.stok = item.stok ?? 0;
                        option.dataset.harga = pickHarga(item);
                        select.appendChild(option);
                    });

                    // Auto-select jika ada old value
                    const oldItemId = "{{ old('item_id') }}";
                    if (oldItemId) {
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].dataset.itemId == oldItemId) {
                                select.selectedIndex = i;
                                select.dispatchEvent(new Event('change'));
                                break;
                            }
                        }
                    }

                } catch (error) {
                    console.error('Error loadPilihanBarang:', error);
                }
            }

            // ===== FETCH DETAIL BARANG =====
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

            // ===== APPLY AI PARSED DATA =====
            function applyAiParsedData(data) {
                const select = document.getElementById('kode_lokasi_kondisi');

                if (data.item_id && select) {
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].dataset.itemId == data.item_id) {
                            select.selectedIndex = i;
                            select.dispatchEvent(new Event('change'));
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

            // ===== UPDATE TOTAL =====
            function updateTotal() {
                const jumlahInput = document.getElementById('jumlah_keluar');
                const hargaInput = document.getElementById('harga_jual');
                const stokInput = document.getElementById('stok_tersedia');
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

                // Validasi
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

            // ===== CONFIRM SIMPAN =====
            window.confirmSimpanKeluar = function() {
                const jumlah = num(document.getElementById('jumlah_keluar').value);
                const stok = num(document.getElementById('stok_tersedia').value);
                if (jumlah > stok) {
                    alert(`Jumlah keluar melebihi stok tersedia. Sisa stok: ${stok}`);
                    return false;
                }
                return confirm('Apakah Anda yakin ingin menyimpan transaksi barang keluar ini?');
            };

            // ===== INIT =====
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

                // Initial update
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
