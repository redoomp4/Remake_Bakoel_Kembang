@extends('layouts.app')

@section('content')
<style>
    .container-laporan { max-width:1200px;margin:auto;padding:30px 20px;font-family:'Segoe UI',sans-serif; }

    /* ==== HEADER ==== */
    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .back-button, .btn-secondary, .create-button {
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease;
        border: none;
        outline: none;
        display: inline-block;
    }
    .back-button { background-color: #e5e7eb; color: #111827; }
    .back-button:hover { background-color: #d1d5db; }
    .create-button { background-color: #1a5de2; color: #fff; }

    /* ==== FILTER ==== */
    form.filter-form {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }
    .filter-form .form-group {
        display: flex;
        flex-direction: column;
        min-width: 200px;
    }
    .filter-form label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .filter-form input,
    .filter-form select {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }
    .filter-form button {
        height: 38px;
        padding: 0 16px;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .filter-form button:hover { background-color: #2563eb; }

    /* ==== TABLE ==== */
    .table-wrapper { width: 100%; overflow-x: auto; }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    th, td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
    }
    th {
        background-color: #f3f4f6;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Tombol Aksi */
    .td-action {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
        flex-wrap: nowrap;
    }
    .btn-action {
        padding: 6px 10px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    /* ==== MOBILE: jadikan kartu ==== */
    @media(max-width: 768px) {
        .header { flex-direction: column; align-items: flex-start; }
        .filter-form { flex-direction: column; }
        .filter-form .form-group { width: 100%; }
        .filter-form button, .btn-secondary { width: 100%; }

        table, thead, tbody, th, td, tr { display: block; width: 100%; }
        thead { display: none; }

        tr {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        td {
            border: none !important;
            text-align: left;
            padding: 8px 12px 8px 55%;
            position: relative;
        }
        td:before {
            position: absolute;
            top: 8px;
            left: 12px;
            width: 38%;
            white-space: nowrap;
            font-weight: bold;
            color: #333;
            content: "";
        }

        /* Label sesuai urutan kolom */
        td:nth-of-type(1):before { content: "Kode Barang"; }
        td:nth-of-type(2):before { content: "Nama Barang"; }
        td:nth-of-type(3):before { content: "Kategori"; }
        td:nth-of-type(4):before { content: "Satuan"; }
        td:nth-of-type(5):before { content: "Stok Minimum"; }
        td:nth-of-type(6):before { content: "Foto"; }
        td:nth-of-type(7):before { content: "Aksi"; }

        .td-action { justify-content: flex-start; }
    }
</style>

<div class="container">
    <div class="header">
        <h4>Daftar Item</h4>
        <a href="{{ route('item.create') }}" class="create-button">+ Tambah Item</a>
    </div>

    {{-- Form Filter --}}
    <form method="GET" action="{{ route('item.index') }}" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Barang</label>
            <input type="text" id="search" name="search" placeholder="Cari nama barang" value="{{ request('search') }}">
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">

        <div style="max-width:1250px;margin:auto;">

            {{-- HEADER --}}
            <div
                style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div>
                    <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;">
                        Inventori · Master Barang
                    </p>
                    <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">
                        Daftar Item
                    </h4>
                </div>
                <a href="{{ route('item.create') }}"
                    style="background:#0B4F35;color:#fff;padding:.8rem 1.25rem;
                           border-radius:.85rem;text-decoration:none;font-weight:800;">
                    + Tambah Item Baru
                </a>

            </div>


            {{-- FILTER --}}
            <form method="GET" action="{{ route('item.index') }}"
                style="background:#fff;border:1px solid #E4E4D9;
                       border-radius:1.25rem;padding:1.25rem;
                       display:flex;gap:1rem;align-items:end;
                       flex-wrap:wrap;margin-bottom:1.5rem;">
                {{-- SEARCH --}}
                <div style="flex:1;min-width:220px;">
                    <label for="search"
                        style="display:block;color:#475569;font-weight:800;
                               font-size:.85rem;margin-bottom:.5rem;">
                        Cari Barang
                    </label>
                    <input type="text" id="search" name="search" placeholder="Cari nama atau kode barang..."
                        value="{{ request('search') }}"
                        style="width:100%;padding:.8rem;
                               border:1px solid #E4E4D9;
                               border-radius:.75rem;">
                </div>

                {{-- KATEGORI --}}
                <div style="flex:1;min-width:220px;">
                    <label for="kategori"
                        style="display:block;color:#475569;font-weight:800;
                               font-size:.85rem;margin-bottom:.5rem;">
                        Kategori
                    </label>
                    <select name="kategori" id="kategori"
                        style="width:100%;padding:.8rem;
                               border:1px solid #E4E4D9;
                               border-radius:.75rem;">
                        <option value="">
                            -- Semua Kategori --
                        </option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BUTTON --}}
                <button type="submit"
                    style="background:#0B4F35;color:#fff;border:0;
                           border-radius:.75rem;padding:.8rem 1.25rem;
                           font-weight:800;cursor:pointer;">
                    Filter
                </button>
                <a href="{{ route('item.index') }}"
                    style="background:#E4E4D9;color:#475569;
                           border-radius:.75rem;padding:.8rem 1.25rem;
                           text-decoration:none;font-weight:800;">
                    Reset
                </a>

            </form>


            {{-- TABLE --}}
            <div
                style="background:#fff;border:1px solid #E4E4D9;
                       border-radius:1.5rem;overflow:hidden;
                       box-shadow:0 1px 4px rgba(0,0,0,.05);
                       overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:1000px;">
                    <thead
                        style="background:#f0ebe3;color:#475569;
                               font-size:.72rem;text-transform:uppercase;
                               letter-spacing:.06em;">
                        <tr>
                            @foreach (['No', 'Foto', 'Kode Barang', 'Nama Barang', 'Kategori', 'Satuan', 'Stok Minimum', 'Aksi'] as $heading)
                                <th style="padding:1rem;text-align:left;">
                                    {{ $heading }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($items as $item)
                            <tr style="border-bottom:1px solid #E4E4D9;" onmouseover="this.style.background='#fafbf8'"
                                onmouseout="this.style.background='#fff'">
                                {{-- NO --}}
                                <td style="padding:1rem;">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- FOTO --}}
                                <td style="padding:1rem;">
                                    @if ($item->foto)
                                        <img src="{{ asset('storage/' . $item->foto) }}"
                                            alt="Foto {{ $item->nama_barang }}"
                                            style="width:48px;height:48px;
                                                   border-radius:.75rem;
                                                   object-fit:cover;
                                                   border:1px solid #E4E4D9;">
                                    @else
                                        <span style="color:#999;font-size:.8rem;">
                                            Tidak ada
                                        </span>
                                    @endif
                                </td>

                                {{-- KODE --}}
                                <td style="padding:1rem;font-weight:800;">
                                    {{ $item->kode_barang }}
                                </td>

                                {{-- NAMA --}}
                                <td style="padding:1rem;">
                                    {{ $item->nama_barang }}
                                </td>

                                {{-- KATEGORI --}}
                                <td style="padding:1rem;">
                                    <span
                                        style="background:#f1f5f9;color:#475569;
                                               padding:.4rem .7rem;
                                               border-radius:999px;
                                               font-size:.75rem;
                                               font-weight:800;">
                                        {{ $item->kategori->kategori ?? '-' }}
                                    </span>
                                </td>

                                {{-- SATUAN --}}
                                <td style="padding:1rem;">
                                    {{ $item->satuan->nama_satuan ?? '-' }}
                                </td>

                                {{-- STOK MINIMUM --}}
                                <td style="padding:1rem;">
                                    {{ $item->stok_minimum }}
                                </td>

                                {{-- AKSI --}}
                                <td style="padding:1rem;">
                                    <div
                                        style="display:flex;gap:.4rem;
                                               flex-wrap:wrap;">
                                        {{-- DETAIL --}}
                                        <a href="{{ route('item.show', $item->id) }}" title="Lihat Detail"
                                            style="background:#60a5fa;color:#fff;
                                                   padding:.5rem .7rem;
                                                   border-radius:.6rem;
                                                   text-decoration:none;
                                                   font-weight:700;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- EDIT --}}
                                        <a href="{{ route('item.edit', $item->id) }}" title="Edit"
                                            style="background:#fbbf24;color:#1a1a1a;
                                                   padding:.5rem .7rem;
                                                   border-radius:.6rem;
                                                   text-decoration:none;
                                                   font-weight:700;">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        {{-- Cetak QR --}}
                                        @if ($item->qr_code)
                                            <a href="{{ route('item.cetak.pdf', $item->id) }}" target="_blank"
                                                title="Cetak QR Code"
                                                style="background:#10b981;color:#fff;
                                                padding:.5rem .7rem;
                                                border-radius:.6rem;
                                                text-decoration:none;
                                                font-weight:700;">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                        @endif
                                        {{-- HAPUS --}}
                                        <form action="{{ route('item.destroy', $item->id) }}" method="POST"
                                            class="delete-form" data-item="{{ $item->nama_barang }}"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" title="Hapus" onclick="openDeleteModal(this)"
                                                style="background:#ef4444;color:#fff;
                                                       border:0;
                                                       padding:.5rem .7rem;
                                                       border-radius:.6rem;
                                                       font-weight:700;
                                                       cursor:pointer;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                {{-- <td colspan="8"
                                    style="padding:2rem;
                                           text-align:center;
                                           color:#999;">
                                    Data item belum tersedia.
                                </td> --}}
                                <td colspan="8"
                                    style="                      padding:2rem;                      text-align:center;                      color:#999;                  ">
                                    <i class="fas fa-box-open"
                                        style="                          font-size:1.5rem;                          margin-bottom:.5rem;                      ">
                                    </i>
                                    <div> Data item belum tersedia
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>


            {{-- PAGINATION --}}
            <div style="margin-top:1.5rem;text-align:center;">
                {{ $items->links() }}

            </div>

        </div>

    </div>


    {{-- DELETE CONFIRMATION MODAL --}}
    <div id="deleteModal"
        class="fixed inset-0 bg-brand-emerald/40 backdrop-blur-sm
           hidden items-center justify-center z-50 p-4"
        role="dialog" aria-modal="true" aria-labelledby="delete-title">

        <div id="deleteModalContent"
            class="bg-white text-gray-900
               border-2 border-brand-emerald
               p-7 md:p-10 rounded-3xl shadow-2xl
               max-w-xl w-full text-center space-y-5
               transform scale-95 transition-transform duration-300">

            {{-- ICON --}}
            <div
                class="w-16 h-16 bg-emerald-50 rounded-2xl
                    flex items-center justify-center
                    text-rose-600 mx-auto">
                <i class="fas fa-triangle-exclamation text-4xl"></i>

            </div>

            {{-- TITLE --}}
            <div>
                <h3 id="delete-title" class="text-xl md:text-2xl font-black text-brand-emerald">
                    Hapus Item?
                </h3>
                <p class="text-sm text-brand-slate font-semibold mt-2">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            {{-- ITEM --}}
            <div class="bg-[#FAF9F6]
                    border border-[#E4E4D9]
                    rounded-2xl p-4">
                <p class="text-sm text-brand-slate font-semibold">
                    Apakah Anda yakin ingin menghapus item:
                </p>
                <p id="deleteItemName" class="text-base font-black text-brand-emerald mt-1">
                </p>

            </div>

            {{-- BUTTON --}}
            <div class="flex justify-center gap-3 pt-1">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-7 py-3
                       bg-[#E4E4D9]
                       hover:bg-gray-300
                       text-brand-slate
                       font-black
                       rounded-xl
                       transition">
                    Batal
                </button>
                <button type="button" onclick="submitDelete()"
                    class="px-7 py-3
                       bg-rose-600
                       hover:bg-rose-700
                       text-white
                       font-black
                       rounded-xl
                       transition">
                    <i class="fas fa-trash-can mr-1"></i>
                    Ya, Hapus
                </button>

            </div>

        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori">
                <option value="">-- Semua Kategori --</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="flex-direction: row; gap: 10px;">
            <button type="submit">Filter</button>
            <a href="{{ route('item.index') }}" class="back-button">Reset</a>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Stok Minimum</th>
                    <th >Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}"
                                     alt="Foto {{ $item->nama_barang }}"
                                     class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                            @else
                                <span class="text-muted fst-italic">(Tidak ada foto)</span>
                            @endif
                        </td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->kategori->kategori }}</td>
                        <td>{{ $item->satuan->nama_satuan ?? '-' }}</td>
                        <td>{{ $item->stok_minimum }}</td>

                        <td class="text-start">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('item.show', $item->kode_barang) }}"
                                class="btn-action" style="background-color: #60a5fa; color: #fff;">Lihat</a>
                                <a href="{{ route('item.edit', $item->kode_barang) }}"
                                class="btn-action" style="background-color: #fbbf24; color: #111;">Edit</a>
                                <a href="{{ route('barang-masuk.qrshow.kode', $item->kode_barang) }}"
                                class="btn-action" style="background-color: #10b981; color: #fff;" target="_blank" title="Lihat QR Code">🌸 QR</a>
                                {{--<form action="{{ route('item.destroy', $item->kode_barang) }}"
                                    method="POST" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action" style="background-color: #ef4444; color: #fff;">Hapus</button>
                                </form>--}}
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:20px; text-align:center;">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>


@endsection
