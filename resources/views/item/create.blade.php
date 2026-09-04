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

        select.form-control {
            appearance: auto;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
            font-weight: 400;
        }

        input[type="file"].form-control {
            padding: .65rem;
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
        }
    </style>

    <div class="container">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <div class="eyebrow">Master Data</div>
                <h1>Tambah Item</h1>
                <p>Tambahkan item baru ke dalam inventori.</p>
            </div>
            <a href="{{ route('item.index') }}" class="btn-back">
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

        {{-- FORM --}}
        <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf

            {{-- FORM HEADER --}}
            <div class="form-header">
                <div class="icon-box">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <h2>Informasi Item</h2>
                    <p>Masukkan detail item yang akan ditambahkan.</p>
                </div>
            </div>

            {{-- FORM BODY --}}
            <div class="form-body">

                {{-- NAMA BARANG --}}
                <div class="form-group">
                    <label for="nama_barang" class="form-label">Nama Barang *</label>
                    <input type="text" id="nama_barang" name="nama_barang"
                           value="{{ old('nama_barang') }}" required class="form-control">
                    @error('nama_barang')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- HARGA + STOK MINIMUM --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="harga_dasar" class="form-label">Harga Dasar *</label>
                        <input type="number" id="harga_dasar" name="harga_dasar"
                               value="{{ old('harga_dasar') }}" required class="form-control">
                        @error('harga_dasar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="stok_minimum" class="form-label">Stok Minimum *</label>
                        <input type="number" id="stok_minimum" name="stok_minimum"
                               value="{{ old('stok_minimum') }}" required class="form-control">
                        @error('stok_minimum')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- KATEGORI + SATUAN --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="id_kategori" class="form-label">Kategori *</label>
                        <select id="id_kategori" name="id_kategori" required class="form-control">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}" {{ old('id_kategori') == $k->id ? 'selected' : '' }}>
                                    {{ $k->kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="id_satuan" class="form-label">Satuan *</label>
                        <select id="id_satuan" name="id_satuan" required class="form-control">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach ($satuan as $s)
                                <option value="{{ $s->id }}" {{ old('id_satuan') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_satuan')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- DESKRIPSI --}}
                <div class="form-group">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" class="form-control">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- FOTO --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label for="foto" class="form-label">Foto Barang</label>
                    <input type="file" id="foto" name="foto" class="form-control">
                    @error('foto')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="form-footer">
                <a href="{{ route('item.index') }}" class="btn-cancel">
                    <i class="fas fa-xmark"></i> Batal
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Simpan Item
                </button>
            </div>

        </form>

    </div>
@endsection
