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

        .form-label .optional {
            color: #94a3b8;
            font-weight: 500;
            font-size: .7rem;
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
        .btn-submit:hover {
            background: #065f46;
            transform: translateY(-1px);
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
                <div class="eyebrow">Inventori · Barang Masuk</div>
                <h1>Tambah Barang Masuk</h1>
                <p>Catat penerimaan barang ke dalam inventori.</p>
            </div>
            <a href="{{ route('barang-masuk.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- ALERT ERROR --}}
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
        <form method="POST" action="{{ route('barang-masuk.store') }}" onsubmit="return confirmSimpan();" class="form-card">
            @csrf

            {{-- FORM HEADER --}}
            <div class="form-header">
                <div class="icon-box">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h2>Informasi Barang</h2>
                    <p>Masukkan detail barang yang diterima.</p>
                </div>
            </div>

            {{-- FORM BODY --}}
            <div class="form-body">

                {{-- ITEM --}}
                <div class="form-group">
                    <label for="item_id" class="form-label">Pilih Item</label>
                    <select name="item_id" id="item_id" required class="form-control">
                        <option value="" disabled {{ old('item_id') ? '' : 'selected' }}>-- Pilih Item --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->kode_barang }} — {{ $item->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- JUMLAH + HARGA --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" id="jumlah" name="jumlah" min="1"
                               value="{{ old('jumlah') }}" required placeholder="Contoh: 10" class="form-control">
                        @error('jumlah')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="harga_satuan" class="form-label">Harga Beli / Satuan</label>
                        <input type="number" id="harga_satuan" name="harga_satuan" min="0" step="0.01"
                               value="{{ old('harga_satuan') }}" required placeholder="0" class="form-control">
                        @error('harga_satuan')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- TOTAL --}}
                <div class="form-group">
                    <div class="total-box">
                        <div>
                            <p class="label">Total Nilai Barang</p>
                            <p class="sub-label">Jumlah × Harga Beli</p>
                        </div>
                        <div style="text-align:right;">
                            <p style="color:#059669;font-size:.7rem;font-weight:800;margin:0;">TOTAL</p>
                            <p id="total_harga_display" class="total-amount">Rp 0</p>
                        </div>
                    </div>
                    <input type="hidden" id="total_harga" name="total_harga" value="{{ old('total_harga', 0) }}">
                </div>

                {{-- TANGGAL --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="datetime-local" id="tanggal_masuk" name="tanggal_masuk"
                               value="{{ old('tanggal_masuk', now()->format('Y-m-d\TH:i')) }}" required class="form-control">
                        @error('tanggal_masuk')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_kadaluarsa" class="form-label">
                            Tanggal Kadaluarsa <span class="optional">(Opsional)</span>
                        </label>
                        <input type="datetime-local" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa"
                               value="{{ old('tanggal_kadaluarsa') }}" class="form-control">
                        @error('tanggal_kadaluarsa')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- PEMASOK / LOKASI / KONDISI --}}
                <div class="grid-3">
                    <div class="form-group">
                        <label for="id_pemasok" class="form-label">Pemasok</label>
                        <select name="id_pemasok" id="id_pemasok" required class="form-control">
                            <option value="" disabled {{ old('id_pemasok') ? '' : 'selected' }}>Pilih Pemasok</option>
                            @foreach ($pemasoks as $pemasok)
                                <option value="{{ $pemasok->id }}" {{ old('id_pemasok') == $pemasok->id ? 'selected' : '' }}>
                                    {{ $pemasok->nama_pemasok }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_lokasi" class="form-label">Lokasi</label>
                        <select name="id_lokasi" id="id_lokasi" required class="form-control">
                            <option value="" disabled {{ old('id_lokasi') ? '' : 'selected' }}>Pilih Lokasi</option>
                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}" {{ old('id_lokasi') == $lokasi->id ? 'selected' : '' }}>
                                    {{ $lokasi->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_kondisi" class="form-label">Kondisi</label>
                        <select name="id_kondisi" id="id_kondisi" required class="form-control">
                            <option value="" disabled {{ old('id_kondisi') ? '' : 'selected' }}>Pilih Kondisi</option>
                            @foreach ($kondisis as $kondisi)
                                <option value="{{ $kondisi->id }}" {{ old('id_kondisi') == $kondisi->id ? 'selected' : '' }}>
                                    {{ $kondisi->nama_kondisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" maxlength="1000"
                              placeholder="Tambahkan catatan jika diperlukan..." class="form-control">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- FORM FOOTER --}}
            <div class="form-footer">
                <a href="{{ route('barang-masuk.index') }}" class="btn-cancel">
                    <i class="fas fa-xmark"></i> Batal
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Simpan Barang Masuk
                </button>
            </div>

        </form>

    </div>

    <script>
        function updateTotalHarga() {
            const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
            const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
            const total = jumlah * harga;
            document.getElementById('total_harga').value = total;
            document.getElementById('total_harga_display').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const jumlah = document.getElementById('jumlah');
            const harga = document.getElementById('harga_satuan');
            jumlah.addEventListener('input', updateTotalHarga);
            harga.addEventListener('input', updateTotalHarga);
            updateTotalHarga();
        });

        function confirmSimpan() {
            return confirm('Apakah Anda yakin ingin menyimpan data barang masuk ini?');
        }
    </script>
@endsection