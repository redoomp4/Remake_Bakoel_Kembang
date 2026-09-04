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

        .foto-preview {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border-radius: .75rem;
            margin-top: .75rem;
            border: 1px solid #e2e8f0;
        }

        /* ============================================================
           GRID
           ============================================================ */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr 1fr;
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

        .btn-mic {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .75rem 1rem;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: .75rem;
            color: #047857;
            font-size: 1.1rem;
            cursor: pointer;
            transition: .2s;
            flex-shrink: 0;
        }
        .btn-mic:hover {
            background: #d1fae5;
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
        }
    </style>

    <div class="container">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <div class="eyebrow">Master Data</div>
                <h1>Edit Item</h1>
                <p>Perbarui informasi item yang sudah ada.</p>
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
        <form action="{{ route('item.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf
            @method('PUT')

            {{-- FORM HEADER --}}
            <div class="form-header">
                <div class="icon-box">
                    <i class="fas fa-pen"></i>
                </div>
                <div>
                    <h2>Informasi Item</h2>
                    <p>Perbarui detail item yang sudah ada.</p>
                </div>
            </div>

            {{-- FORM BODY --}}
            <div class="form-body">

                {{-- NAMA BARANG --}}
                <div class="form-group">
                    <label for="nama_barang" class="form-label">Nama Barang *</label>
                    <div class="input-with-mic">
                        <input type="text" id="nama_barang" name="nama_barang"
                               value="{{ old('nama_barang', $item->nama_barang) }}" required class="form-control">
                    </div>
                    @error('nama_barang')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- HARGA + STOK MINIMUM --}}
                <div class="grid-2">
                    <div class="form-group">
                        <label for="harga_dasar" class="form-label">Harga Dasar *</label>
                        <input type="number" id="harga_dasar" name="harga_dasar"
                               value="{{ old('harga_dasar', $item->harga_dasar) }}" required class="form-control">
                        @error('harga_dasar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="stok_minimum" class="form-label">Stok Minimum *</label>
                        <input type="number" id="stok_minimum" name="stok_minimum"
                               value="{{ old('stok_minimum', $item->stok_minimum ?? '') }}" required class="form-control">
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
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}"
                                    {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
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
                            @foreach ($satuan as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('id_satuan', $item->id_satuan) == $s->id ? 'selected' : '' }}>
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
                    <textarea id="deskripsi" name="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $item->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- FOTO --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" id="foto" name="foto" class="form-control">
                    @if ($item->foto)
                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="foto-preview">
                    @endif
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
                    <i class="fas fa-save"></i> Update Item
                </button>
            </div>

        </form>

    </div>

    <script>
        function speakField(id) {
            if ('speechSynthesis' in window) {
                speechSynthesis.cancel();
                const label = document.querySelector('label[for="' + id + '"]');
                const text = label ? label.textContent.trim() : id;
                speechSynthesis.speak(new SpeechSynthesisUtterance('Isi ' + text));
            }
        }
    </script>
@endsection
