@extends('layouts.app')

@section('content')
    <style>
        /* ============================================================
           CONTAINER
           ============================================================ */
        main>.container {
            width: 100%;
            max-width: 1280px !important;
            min-width: 0;
            padding: 2rem 1.25rem 3rem;
            margin: 0 auto;
        }

        /* ============================================================
           HEADER
           ============================================================ */
        .page-header {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        .page-header .eyebrow {
            color: #059669;
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .page-header h1 {
            margin: .2rem 0 .25rem;
            color: #0f172a;
            font-size: 1.75rem;
            font-weight: 900;
        }

        .page-header p {
            margin: 0;
            color: #64748b;
            font-size: .9rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem 1.25rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            color: #475569;
            font-size: .85rem;
            font-weight: 800;
            text-decoration: none;
            transition: .2s;
        }
        .btn-back:hover {
            background: #f8fafc;
            transform: translateY(-1px);
        }

        /* ============================================================
           ALERT
           ============================================================ */
        .alert-box {
            padding: .9rem 1rem;
            border-radius: .9rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .alert-danger-box {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
        }

        /* ============================================================
           FORM CARD
           ============================================================ */
        .form-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        .form-header {
            padding: 1.25rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .form-header .icon-box {
            width: 40px;
            height: 40px;
            background: #0B4F35;
            color: #fff;
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-header h2 {
            color: #0B4F35;
            font-weight: 900;
            font-size: 1.1rem;
            margin: 0;
        }

        .form-header p {
            color: #475569;
            font-size: .75rem;
            margin: 0;
        }

        .form-body {
            padding: 1.5rem;
        }

        .form-footer {
            padding: 1.25rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column-reverse;
            gap: .75rem;
        }

        @media (min-width: 640px) {
            .form-footer {
                flex-direction: row;
                justify-content: flex-end;
            }
        }

        /* ============================================================
           FORM ELEMENTS
           ============================================================ */
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            color: #475569;
            font-size: .8rem;
            font-weight: 800;
            margin-bottom: .5rem;
        }

        .form-control {
            width: 100%;
            padding: .75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: .75rem;
            font-size: .9rem;
            font-weight: 600;
            transition: .2s;
            background: #fff;
        }

        .form-control:focus {
            outline: 0;
            border-color: #0B4F35;
            box-shadow: 0 0 0 4px rgba(11, 79, 53, .12);
        }

        .form-control:read-only {
            background: #f8fafc;
            color: #64748b;
        }

        select.form-control {
            appearance: auto;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
            font-weight: 400;
        }

        .form-error {
            color: #dc2626;
            font-size: .75rem;
            font-weight: 700;
            margin-top: .25rem;
        }

        .form-warning {
            color: #dc2626;
            font-size: .75rem;
            font-weight: 700;
            margin-top: .25rem;
        }

        /* ============================================================
           GRID
           ============================================================ */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr 1fr;
            }
            .grid-3 {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        /* ============================================================
           TOTAL BOX
           ============================================================ */
        .total-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 1rem;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .total-box .label {
            color: #059669;
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .total-box .sub-label {
            color: #475569;
            font-size: .7rem;
            margin-top: .25rem;
        }

        .total-box .total-amount {
            color: #0B4F35;
            font-size: 1.5rem;
            font-weight: 900;
        }

        @media (min-width: 768px) {
            .total-box .total-amount {
                font-size: 2rem;
            }
        }

        /* ============================================================
           BUTTONS
           ============================================================ */
        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .75rem 1.5rem;
            background: #0B4F35;
            color: #fff;
            border: 0;
            border-radius: .75rem;
            font-size: .85rem;
            font-weight: 800;
            cursor: pointer;
            transition: .2s;
        }
        .btn-submit:hover:not(:disabled) {
            background: #065f46;
            transform: translateY(-1px);
        }
        .btn-submit:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .75rem 1.25rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            color: #475569;
            font-size: .85rem;
            font-weight: 800;
            text-decoration: none;
            transition: .2s;
        }
        .btn-cancel:hover {
            background: #f8fafc;
        }

        .btn-scan {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .75rem 1rem;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: .75rem;
            color: #047857;
            font-weight: 800;
            font-size: .85rem;
            cursor: pointer;
            transition: .2s;
            white-space: nowrap;
        }
        .btn-scan:hover {
            background: #d1fae5;
        }
        .btn-scan .label {
            display: inline;
        }
        @media (max-width: 480px) {
            .btn-scan .label {
                display: none;
            }
        }

        .btn-mic {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .75rem 1rem;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: .75rem;
            color: #047857;
            font-size: .9rem;
            cursor: pointer;
            transition: .2s;
        }
        .btn-mic:hover {
            background: #d1fae5;
        }

        .select-with-scan {
            display: flex;
            gap: .5rem;
        }
        .select-with-scan .form-control {
            flex: 1;
        }

        .input-with-mic {
            display: flex;
            gap: .5rem;
        }
        .input-with-mic .form-control {
            flex: 1;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 768px) {
            main>.container {
                padding: 1rem .75rem 2rem;
            }
            .page-header {
                padding: 1rem;
                border-radius: 1rem;
                flex-direction: column;
                align-items: stretch;
            }
            .page-header h1 {
                font-size: 1.35rem;
            }
            .btn-back {
                width: 100%;
                justify-content: center;
            }
            .form-header {
                padding: 1rem;
                flex-wrap: wrap;
            }
            .form-body {
                padding: 1rem;
            }
            .form-footer {
                padding: 1rem;
                flex-direction: column;
            }
            .btn-submit, .btn-cancel {
                width: 100%;
                justify-content: center;
            }
            .select-with-scan {
                flex-wrap: wrap;
            }
            .input-with-mic {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            main>.container {
                padding-left: .6rem;
                padding-right: .6rem;
            }
            .page-header h1 {
                font-size: 1.2rem;
            }
            .form-body {
                padding: .75rem;
            }
            .total-box {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    <div class="container">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <div class="eyebrow">Inventori · Arus Keluar</div>
                <h1>Edit Barang Keluar</h1>
                <p>Perbarui informasi transaksi barang keluar.</p>
            </div>
            <a href="{{ route('barang-keluar.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ALERT VALIDATION --}}
        @if ($errors->any())
            <div class="alert-box alert-danger-box">
                <div style="display:flex;align-items:flex-start;gap:.75rem;">
                    <i class="fas fa-circle-exclamation" style="margin-top:.15rem;"></i>
                    <div>
                        <p style="font-weight:800;margin:0 0 .25rem;">Terdapat kesalahan pada form</p>
                        <ul style="margin:0;padding-left:1.25rem;font-weight:500;list-style:disc;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert-box alert-danger-box">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <i class="fas fa-circle-exclamation"></i>
                    <span style="font-weight:700;">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('barang-keluar.update', $barangKeluar->id) }}"
              onsubmit="return confirmUpdate();" class="form-card">
            @csrf
            @method('PUT')

            {{-- FORM HEADER --}}
            <div class="form-header">
                <div class="icon-box">
                    <i class="fas fa-pen"></i>
                </div>
                <div>
                    <h2>Informasi Barang Keluar</h2>
                    <p>Perbarui detail transaksi barang yang keluar.</p>
                </div>
            </div>

            {{-- FORM BODY --}}
            <div class="form-body">

                {{-- BARANG --}}
                <div class="form-group">
                    <label for="kode_lokasi_kondisi" class="form-label">Pilih Barang *</label>
                    <div class="select-with-scan">
                        <select name="kode_lokasi_kondisi" id="kode_lokasi_kondisi" required class="form-control">
                            <option value="">-- Memuat data barang --</option>
                        </select>
                    </div>
                    @error('kode_lokasi_kondisi')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- HIDDEN --}}
                <input type="hidden" name="id_lokasi" id="id_lokasi"
                       value="{{ old('id_lokasi', $barangKeluar->id_lokasi) }}">
                <input type="hidden" name="id_kondisi" id="id_kondisi"
                       value="{{ old('id_kondisi', $barangKeluar->id_kondisi) }}">
                <input type="hidden" name="item_id" id="item_id"
                       value="{{ old('item_id', $barangKeluar->item_id) }}">

                {{-- INFORMASI BARANG --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" id="nama_barang" readonly class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" id="satuan" readonly class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="stok_tersedia" class="form-label">Stok Tersedia</label>
                        <input type="text" id="stok_tersedia" readonly class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="harga_dasar" class="form-label">Harga Rata-Rata</label>
                        <input type="text" id="harga_dasar" readonly class="form-control">
                    </div>
                </div>

                {{-- JUMLAH + HARGA --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="jumlah_keluar" class="form-label">Jumlah Keluar</label>
                        <input type="number" name="jumlah_keluar" id="jumlah_keluar" min="1" required
                               value="{{ old('jumlah_keluar', $barangKeluar->jumlah_keluar) }}"
                               placeholder="Contoh: 5" class="form-control">
                        <p id="jumlah_warning" class="form-warning"></p>
                    </div>
                    <div class="form-group">
                        <label for="harga_jual" class="form-label">Harga Jual / Unit</label>
                        <input type="number" name="harga_jual" id="harga_jual" min="0" step="0.01" required
                               value="{{ old('harga_jual', $barangKeluar->harga_jual) }}"
                               placeholder="0" class="form-control">
                    </div>
                </div>

                {{-- TOTAL --}}
                <div class="form-group">
                    <div class="total-box">
                        <div>
                            <p class="label">Total Nilai Barang</p>
                            <p class="sub-label">Jumlah × Harga Jual</p>
                        </div>
                        <div style="text-align:right;">
                            <p style="color:#059669;font-size:.7rem;font-weight:800;margin:0;">TOTAL</p>
                            <p id="total_harga_display" class="total-amount">Rp 0</p>
                        </div>
                    </div>
                    <input type="hidden" id="total_harga_jual" name="total_harga_jual"
                           value="{{ old('total_harga_jual', $barangKeluar->total_harga_jual) }}">
                </div>

                {{-- PENERIMA + JENIS TRANSAKSI --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="penerima" class="form-label">Penerima</label>
                        <div class="input-with-mic">
                            <input type="text" name="penerima" id="penerima"
                                   value="{{ old('penerima', $barangKeluar->penerima) }}" required
                                   placeholder="Nama penerima" class="form-control">
                        </div>
                        @error('penerima')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="jenis_transaksi" class="form-label">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi" required class="form-control">
                            <option value="" disabled>-- Pilih Jenis Transaksi --</option>
                            @foreach (['Penjualan', 'Donasi', 'Pemakaian Internal', 'Pemindahan Barang', 'Retur ke Supplier', 'Penghapusan', 'Lainnya'] as $jenis)
                                <option value="{{ $jenis }}"
                                    {{ old('jenis_transaksi', $barangKeluar->jenis_transaksi) == $jenis ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_transaksi')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- LOKASI TUJUAN --}}
                <div class="form-group">
                    <label for="lokasi_tujuan" class="form-label">Lokasi Tujuan</label>
                    <input type="text" name="lokasi_tujuan" id="lokasi_tujuan"
                           value="{{ old('lokasi_tujuan', $barangKeluar->lokasi_tujuan) }}" required
                           placeholder="Contoh: Gudang A / Toko Cabang / Rumah Pelanggan" class="form-control">
                    @error('lokasi_tujuan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CATATAN --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" maxlength="1000"
                              placeholder="Contoh: Penerimaan Penjualan, Penghapusan, Retur..." class="form-control">{{ old('catatan', $barangKeluar->catatan) }}</textarea>
                    @error('catatan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="form-footer">
                <a href="{{ route('barang-keluar.index') }}" class="btn-cancel">
                    <i class="fas fa-xmark"></i> Batal
                </a>
                <button type="submit" id="submitBtn" class="btn-submit">
                    <i class="fas fa-save"></i> Update Barang Keluar
                </button>
            </div>

        </form>

    </div>

    {{-- JAVASCRIPT --}}
    <script>
        /*
        |--------------------------------------------------------------------------
        | Helper angka
        |--------------------------------------------------------------------------
        */
        function num(value) {
            const number = parseFloat(
                String(value ?? '').replace(/[^\d.-]/g, '')
            );
            return isNaN(number) ? 0 : number;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil harga dasar
        |--------------------------------------------------------------------------
        */
        function pickHarga(data) {
            return num(
                data?.harga_dasar ??
                data?.harga ??
                data?.harga_satuan ??
                data?.item?.harga_dasar
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Text to Speech
        |--------------------------------------------------------------------------
        */
        function speakField(id) {
            if ('speechSynthesis' in window) {
                speechSynthesis.speak(
                    new SpeechSynthesisUtterance('Masukkan ' + id)
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Load daftar barang
        |--------------------------------------------------------------------------
        */
        async function loadPilihanBarang() {
            try {
                const response = await fetch(
                    "{{ route('barang-keluar.pilihan-barang') }}"
                );
                if (!response.ok) {
                    throw new Error('Gagal mengambil data barang.');
                }
                const data = await response.json();
                const select = document.getElementById('kode_lokasi_kondisi');
                select.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.text = '-- Pilih Barang --';
                defaultOption.disabled = true;
                select.appendChild(defaultOption);

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

                const oldItemId = "{{ old('item_id', $barangKeluar->item_id) }}";
                const oldLokasi = "{{ old('id_lokasi', $barangKeluar->id_lokasi) }}";
                const oldKondisi = "{{ old('id_kondisi', $barangKeluar->id_kondisi) }}";

                let found = false;
                Array.from(select.options).forEach(option => {
                    if (option.dataset.itemId == oldItemId &&
                        option.value.split('|')[1] == oldLokasi &&
                        option.value.split('|')[2] == oldKondisi) {
                        option.selected = true;
                        found = true;
                        setBarangFromOption(option);
                    }
                });

                if (found) {
                    const selected = select.options[select.selectedIndex];
                    await fetchDetail(selected.dataset.itemId, oldLokasi, oldKondisi);
                } else {
                    select.value = '';
                }
                updateTotal();
            } catch (error) {
                console.error(error);
                alert('Gagal mengambil daftar barang. Silakan refresh halaman.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Isi informasi barang dari option
        |--------------------------------------------------------------------------
        */
        function setBarangFromOption(option) {
            if (!option) return;
            document.getElementById('item_id').value = option.dataset.itemId ?? '';
            const parts = option.value.split('|');
            document.getElementById('id_lokasi').value = parts[1] ?? '';
            document.getElementById('id_kondisi').value = parts[2] ?? '';
            document.getElementById('nama_barang').value = option.dataset.nama ?? '';
            document.getElementById('satuan').value = option.dataset.satuan ?? '';
            document.getElementById('stok_tersedia').value = num(option.dataset.stok);
            document.getElementById('harga_dasar').value = num(option.dataset.harga);
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch detail barang
        |--------------------------------------------------------------------------
        */
        async function fetchDetail(itemId, lokasi, kondisi) {
            try {
                const response = await fetch(
                    `{{ route('barang-keluar.detail-barang') }}?item_id=${encodeURIComponent(itemId)}&id_lokasi=${encodeURIComponent(lokasi)}&id_kondisi=${encodeURIComponent(kondisi)}`
                );
                if (!response.ok) {
                    throw new Error('Gagal mengambil detail barang.');
                }
                const data = await response.json();
                document.getElementById('nama_barang').value = data.nama_barang ?? '';
                document.getElementById('satuan').value = data.satuan ?? '';
                document.getElementById('stok_tersedia').value = num(data.stok);
                document.getElementById('harga_dasar').value = pickHarga(data);
            } catch (error) {
                console.error(error);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update total
        |--------------------------------------------------------------------------
        */
        function updateTotal() {
            const jumlah = num(document.getElementById('jumlah_keluar').value);
            const harga = num(document.getElementById('harga_jual').value);
            const stok = num(document.getElementById('stok_tersedia').value);
            const total = jumlah * harga;

            document.getElementById('total_harga_jual').value = total;
            document.getElementById('total_harga_display').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(total);

            const warning = document.getElementById('jumlah_warning');
            const submit = document.getElementById('submitBtn');

            if (jumlah > stok) {
                warning.textContent = `Jumlah melebihi stok tersedia (${stok}).`;
                submit.disabled = true;
                submit.classList.add('opacity-50', 'cursor-not-allowed');
            } else if (jumlah < 1) {
                warning.textContent = 'Jumlah minimal 1.';
                submit.disabled = true;
                submit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                warning.textContent = '';
                submit.disabled = false;
                submit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Confirm update
        |--------------------------------------------------------------------------
        */
        function confirmUpdate() {
            const jumlah = num(document.getElementById('jumlah_keluar').value);
            const stok = num(document.getElementById('stok_tersedia').value);
            if (jumlah > stok) {
                alert(`Jumlah keluar melebihi stok tersedia. Sisa stok: ${stok}`);
                return false;
            }
            return confirm('Apakah Anda yakin ingin memperbarui transaksi barang keluar ini?');
        }

        /*
        |--------------------------------------------------------------------------
        | DOM READY
        |--------------------------------------------------------------------------
        */
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('kode_lokasi_kondisi');
            const jumlah = document.getElementById('jumlah_keluar');
            const harga = document.getElementById('harga_jual');

            loadPilihanBarang();

            select.addEventListener('change', async function() {
                if (!this.value) return;
                const [kode, lokasi, kondisi] = this.value.split('|');
                const option = this.options[this.selectedIndex];
                setBarangFromOption(option);
                await fetchDetail(option.dataset.itemId, lokasi, kondisi);
                updateTotal();
            });

            jumlah.addEventListener('input', updateTotal);
            harga.addEventListener('input', updateTotal);
            updateTotal();
        });
    </script>
@endsection
