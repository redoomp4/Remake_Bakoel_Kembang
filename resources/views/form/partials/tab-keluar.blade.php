<section id="tab-content-keluar" class="oneshot-tab-content hidden" role="tabpanel">
    <form method="POST" action="{{ route('form.barang-keluar.store') }}" onsubmit="return confirmSimpanKeluar();"
        class="rounded-3xl border border-[#E4E4D9] bg-white p-5 shadow-sm sm:p-7 space-y-6">
        @csrf

        {{-- HEADER TAB & AI VOICE BUTTON --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#E4E4D9] pb-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0B4F35] text-white">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <h2 class="font-black text-[#0B4F35]">Informasi Barang Keluar</h2>
                    <p class="text-xs text-[#475569]">Pilih barang dan masukkan detail pengeluaran.</p>
                </div>
            </div>

            {{-- TOMBOL PEREKAM AI --}}
            <div class="flex items-center gap-2">
                <button type="button" id="btnVoiceAi"
                    class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-xs font-black text-[#0B4F35] shadow-sm transition-all hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-[#0B4F35]/20">
                    <i id="micIcon" class="fas fa-microphone text-emerald-600"></i>
                    <span id="voiceBtnText">Isi via Suara (AI)</span>
                </button>
            </div>
        </div>

        {{-- STATUS INDIKATOR AI --}}
        <div id="voiceStatusBox" class="hidden rounded-xl border border-emerald-200 bg-emerald-50/50 p-3 text-xs">
            <div class="flex items-center gap-2 font-bold text-[#0B4F35]">
                <span id="voicePulse" class="relative flex h-3 w-3">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                </span>
                <span id="voiceStatusText">Merekam suara... Ucapkan transaksi Anda.</span>
            </div>
            <p id="transcriptPreview" class="mt-1 text-[11px] italic text-gray-500 hidden"></p>
        </div>

        {{-- SELECT BARANG --}}
        <div>
            <label for="kode_lokasi_kondisi" class="mb-2 block text-sm font-extrabold text-[#475569]">Pilih Barang</label>
            <select name="kode_lokasi_kondisi" id="kode_lokasi_kondisi" required
                class="w-full rounded-xl border-2 border-[#E4E4D9] bg-white px-4 py-3 text-sm font-semibold focus:border-[#0B4F35] focus:outline-none focus:ring-4 focus:ring-[#0B4F35]/10">
                <option value="" disabled selected>-- Pilih Barang --</option>
            </select>
            @error('kode_lokasi_kondisi')
                <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- HIDDEN INPUTS --}}
        <input type="hidden" name="id_lokasi" id="id_lokasi">
        <input type="hidden" name="id_kondisi" id="id_kondisi">
        <input type="hidden" name="item_id" id="item_id">

        {{-- INFORMASI BARANG (READONLY) --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="nama_barang" class="mb-2 block text-sm font-extrabold text-[#475569]">Nama Barang</label>
                <input type="text" id="nama_barang" readonly
                    class="w-full rounded-xl border-2 border-[#E4E4D9] bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600">
            </div>
            <div>
                <label for="satuan" class="mb-2 block text-sm font-extrabold text-[#475569]">Satuan</label>
                <input type="text" id="satuan" readonly
                    class="w-full rounded-xl border-2 border-[#E4E4D9] bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600">
            </div>
            <div>
                <label for="stok_tersedia" class="mb-2 block text-sm font-extrabold text-[#475569]">Stok Tersedia</label>
                <input type="text" id="stok_tersedia" readonly
                    class="w-full rounded-xl border-2 border-[#E4E4D9] bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600">
            </div>
            <div>
                <label for="harga_dasar" class="mb-2 block text-sm font-extrabold text-[#475569]">Harga Rata-Rata</label>
                <input type="text" id="harga_dasar" readonly
                    class="w-full rounded-xl border-2 border-[#E4E4D9] bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600">
            </div>
        </div>

        {{-- JUMLAH + HARGA JUAL --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="jumlah_keluar" class="mb-2 block text-sm font-extrabold text-[#475569]">Jumlah Keluar</label>
                <input type="number" name="jumlah_keluar" id="jumlah_keluar" min="1" required placeholder="Contoh: 5"
                    class="w-full rounded-xl border-2 border-[#E4E4D9] px-4 py-3 text-sm font-semibold focus:border-[#0B4F35] focus:outline-none focus:ring-4 focus:ring-[#0B4F35]/10">
                <p id="jumlah_warning" class="mt-1 text-xs font-bold text-red-500"></p>
            </div>
            <div>
                <label for="harga_jual" class="mb-2 block text-sm font-extrabold text-[#475569]">Harga Jual / Unit</label>
                <input type="number" name="harga_jual" id="harga_jual" min="0" step="0.01" required placeholder="0"
                    class="w-full rounded-xl border-2 border-[#E4E4D9] px-4 py-3 text-sm font-semibold focus:border-[#0B4F35] focus:outline-none focus:ring-4 focus:ring-[#0B4F35]/10">
            </div>
        </div>

        {{-- RINGKASAN TOTAL NILAI --}}
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Total Nilai Barang</p>
                    <p class="mt-1 text-xs text-[#475569]">Jumlah × Harga Jual</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-emerald-600">TOTAL</p>
                    <p id="total_harga_display" class="text-xl font-black text-[#0B4F35] md:text-2xl">Rp 0</p>
                </div>
            </div>
            <input type="hidden" id="total_harga_jual" name="total_harga_jual" value="0">
        </div>

        {{-- PENERIMA, JENIS TRANSAKSI & LOKASI TUJUAN --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label for="penerima" class="mb-2 block text-sm font-extrabold text-[#475569]">Penerima</label>
                <input type="text" name="penerima" id="penerima" value="{{ old('penerima') }}" required placeholder="Nama penerima"
                    class="w-full rounded-xl border-2 border-[#E4E4D9] px-4 py-3 text-sm font-semibold focus:border-[#0B4F35] focus:outline-none focus:ring-4 focus:ring-[#0B4F35]/10">
                @error('penerima')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="jenis_transaksi" class="mb-2 block text-sm font-extrabold text-[#475569]">Jenis Transaksi</label>
                <select name="jenis_transaksi" id="jenis_transaksi" required
                    class="w-full rounded-xl border-2 border-[#E4E4D9] bg-white px-4 py-3 text-sm font-semibold focus:border-[#0B4F35] focus:outline-none focus:ring-4 focus:ring-[#0B4F35]/10">
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
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="lokasi_tujuan" class="mb-2 block text-sm font-extrabold text-[#475569]">Lokasi Tujuan</label>
                <input type="text" name="lokasi_tujuan" id="lokasi_tujuan" value="{{ old('lokasi_tujuan') }}" required
                    placeholder="Contoh: Gudang A / Toko Cabang / Pelanggan"
                    class="w-full rounded-xl border-2 border-[#E4E4D9] px-4 py-3 text-sm font-semibold focus:border-[#0B4F35] focus:outline-none focus:ring-4 focus:ring-[#0B4F35]/10">
                @error('lokasi_tujuan')
                    <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- CATATAN --}}
        <div>
            <label for="catatan" class="mb-2 block text-sm font-extrabold text-[#475569]">Catatan</label>
            <textarea name="catatan" id="catatan" rows="3" maxlength="255"
                placeholder="Contoh: Penerimaan Penjualan, Penghapusan, Retur..."
                class="w-full rounded-xl border-2 border-[#E4E4D9] px-4 py-3 text-sm focus:border-[#0B4F35] focus:outline-none focus:ring-4 focus:ring-[#0B4F35]/10"></textarea>
            @error('catatan')
                <p class="mt-1 text-xs font-bold text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION BUTTON --}}
        <div class="flex justify-end pt-2">
            <button type="submit" id="submitBtn"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B4F35] px-6 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#083d29]">
                <i class="fas fa-save"></i>
                Simpan Barang Keluar
            </button>
        </div>
    </form>

    {{-- SCRIPT JAVASCRIPT KHUSUS --}}
    <script>
        (function() {
            let mediaRecorder = null;
            let audioChunks = [];
            let isRecording = false;

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
                } catch (error) {
                    console.error('Error fetchDetail:', error);
                }
            }

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

            // REKAM SUARA & HIT ROUTE PARSE VOICE AI
            async function toggleVoiceRecording() {
                const btn = document.getElementById('btnVoiceAi');
                const text = document.getElementById('voiceBtnText');
                const icon = document.getElementById('micIcon');
                const statusBox = document.getElementById('voiceStatusBox');
                const statusText = document.getElementById('voiceStatusText');
                const transcriptPreview = document.getElementById('transcriptPreview');

                if (!isRecording) {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        audioChunks = [];
                        mediaRecorder = new MediaRecorder(stream);

                        mediaRecorder.ondataavailable = event => {
                            if (event.data.size > 0) audioChunks.push(event.data);
                        };

                        mediaRecorder.onstop = async () => {
                            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                            await sendAudioToAi(audioBlob);
                        };

                        mediaRecorder.start();
                        isRecording = true;

                        btn.classList.replace('bg-emerald-50', 'bg-red-50');
                        btn.classList.replace('border-emerald-300', 'border-red-300');
                        btn.classList.replace('text-[#0B4F35]', 'text-red-600');
                        icon.className = 'fas fa-stop-circle text-red-600 animate-pulse';
                        text.textContent = 'Berhenti Rekam';

                        statusBox.classList.remove('hidden');
                        statusText.textContent = 'Merekam... Ucapkan detail transaksi Anda.';
                        transcriptPreview.classList.add('hidden');
                    } catch (err) {
                        alert('Akses mikrofon ditolak atau tidak didukung oleh browser Anda.');
                    }
                } else {
                    mediaRecorder.stop();
                    mediaRecorder.stream.getTracks().forEach(track => track.stop());
                    isRecording = false;

                    btn.classList.replace('bg-red-50', 'bg-emerald-50');
                    btn.classList.replace('border-red-300', 'border-emerald-300');
                    btn.classList.replace('text-red-600', 'text-[#0B4F35]');
                    icon.className = 'fas fa-spinner fa-spin text-emerald-600';
                    text.textContent = 'Memproses...';

                    statusText.textContent = 'AI sedang menguraikan transaksi Anda...';
                }
            }

            async function sendAudioToAi(audioBlob) {
                const formData = new FormData();
                formData.append('audio', audioBlob, 'voice_input.webm');

                try {
                    const response = await fetch("{{ route('form.barang-keluar.parse-voice') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const result = await response.json();
                    const statusBox = document.getElementById('voiceStatusBox');
                    const text = document.getElementById('voiceBtnText');
                    const icon = document.getElementById('micIcon');
                    const transcriptPreview = document.getElementById('transcriptPreview');

                    icon.className = 'fas fa-microphone text-emerald-600';
                    text.textContent = 'Isi via Suara (AI)';

                    if (result.success) {
                        applyAiParsedData(result.data);

                        if (result.transcript_raw) {
                            transcriptPreview.textContent = `Hasil Suara: "${result.transcript_raw}"`;
                            transcriptPreview.classList.remove('hidden');
                        }

                        document.getElementById('voiceStatusText').textContent = 'Formulir berhasil diisi oleh AI!';
                        setTimeout(() => statusBox.classList.add('hidden'), 5000);
                    } else {
                        alert(result.message || 'Gagal mengekstrak data dari suara.');
                        statusBox.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('AI Processing Error:', error);
                    alert('Terjadi kesalahan koneksi saat memproses perintah suara.');
                    document.getElementById('voiceStatusBox').classList.add('hidden');
                    document.getElementById('micIcon').className = 'fas fa-microphone text-emerald-600';
                    document.getElementById('voiceBtnText').textContent = 'Isi via Suara (AI)';
                }
            }

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
                const btnVoice = document.getElementById('btnVoiceAi');

                loadPilihanBarang();

                if (btnVoice) btnVoice.addEventListener('click', toggleVoiceRecording);

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
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTabKeluar);
            } else {
                initTabKeluar();
            }
        })();
    </script>
</section>