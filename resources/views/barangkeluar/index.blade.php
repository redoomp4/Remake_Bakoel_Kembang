@extends('layouts.app')


@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;overflow-x:hidden;">
        <div style="max-width:1250px;margin:auto;width:100%;">
            {{-- HEADER --}} <div
                style="display:flex;justify-content:space-between;align-items:center;      gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div>
                    <p
                        style="color:#8FA882;font-weight:800;font-size:.75rem;      text-transform:uppercase;margin-bottom:.25rem;">
                        Inventori · Arus Keluar </p>
                    <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;"> Daftar Barang Keluar </h4>
                </div> <a href="{{ route('barang-keluar.create') }}"
                    style="background:#0B4F35;color:#fff;padding:.8rem 1.25rem;  border-radius:.85rem;text-decoration:none;font-weight:800;">
                    <i class="fas fa-plus"></i> Tambah Barang Keluar </a>
            </div> {{-- ALERT ERROR --}} @if (session('error'))
                <div
                    style="background:#fef2f2;color:#b91c1c;padding:1rem;  border-radius:1rem;margin-bottom:1rem;font-weight:700;">
                    <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
                </div>
                @endif {{-- ALERT SUCCESS --}} @if (session('success'))
                    <div
                        style="background:#ecfdf5;color:#047857;padding:1rem;  border-radius:1rem;margin-bottom:1rem;font-weight:700;">
                        <i class="fas fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif {{-- FILTER --}} <form action="{{ route('barang-keluar.index') }}"
                    method="GET"
                    style="background:#fff;border:1px solid #E4E4D9;      border-radius:1.25rem;padding:1.25rem;      display:flex;gap:1rem;align-items:end;      flex-wrap:wrap;margin-bottom:1.5rem;">
                    {{-- SEARCH --}} <div style="flex:1;min-width:220px;"> <label for="search"
                            style="display:block;color:#475569;font-weight:800;      font-size:.85rem;margin-bottom:.5rem;">
                            Cari Barang </label> <input type="text" name="search" id="search"
                            value="{{ request('search') }}" placeholder="Kode, nama, penerima, lokasi..."
                            style="width:100%;padding:.8rem;      border:1px solid #E4E4D9;border-radius:.75rem;">
                    </div> {{-- LOKASI --}} <div style="flex:1;min-width:220px;"> <label for="lokasi"
                            style="display:block;color:#475569;font-weight:800;      font-size:.85rem;margin-bottom:.5rem;">
                            Lokasi </label> <select name="lokasi" id="lokasi"
                            style="width:100%;padding:.8rem;      border:1px solid #E4E4D9;border-radius:.75rem;">
                            <option value=""> -- Semua Lokasi -- </option>
                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}"
                                    {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}> {{ $lokasi->nama_lokasi }}
                                </option>
                            @endforeach
                        </select> </div> {{-- FILTER BUTTON --}} <button type="submit"
                        style="background:#0B4F35;color:#fff;border:0;  border-radius:.75rem;padding:.8rem 1.25rem;  font-weight:800;cursor:pointer;">
                        <i class="fas fa-filter"></i> Filter </button> {{-- RESET --}} <a
                        href="{{ route('barang-keluar.index') }}"
                        style="background:#E4E4D9;color:#475569;  border-radius:.75rem;padding:.8rem 1.25rem;  text-decoration:none;font-weight:800;">
                        Reset </a> </form>
                {{-- TABLE --}}
                {{-- TABLE --}}
                <div
                    style="background:#fff;border:1px solid #E4E4D9;
           border-radius:1.5rem;overflow:hidden;
           box-shadow:0 1px 4px rgba(0,0,0,.05);
           overflow-x:auto;">

                    <table style="width:100%;border-collapse:collapse;min-width:1000px;">
                        {{-- TABLE HEADER --}} <thead
                            style="          background:#f0ebe3;          color:#475569;          font-size:.68rem;          text-transform:uppercase;          letter-spacing:.04em;      ">
                            <tr>
                                <th style="padding:.7rem .6rem;text-align:center;white-space:nowrap;"> No </th>
                                <th style="padding:.7rem .6rem;text-align:left;white-space:nowrap;"> Tanggal </th>
                                <th style="padding:.7rem .6rem;text-align:left;white-space:nowrap;"> Kode </th>
                                <th style="padding:.7rem .6rem;text-align:left;"> Nama Barang </th>
                                <th style="padding:.7rem .6rem;text-align:center;white-space:nowrap;"> Jumlah </th>
                                <th style="padding:.7rem .6rem;text-align:left;white-space:nowrap;"> Total Harga </th>
                                <th style="padding:.7rem .6rem;text-align:left;"> Penerima </th>
                                <th style="padding:.7rem .6rem;text-align:left;"> Jenis Transaksi </th>
                                <th style="padding:.7rem .6rem;text-align:left;"> Lokasi Tujuan </th>
                                <th style="padding:.7rem .6rem;text-align:center;white-space:nowrap;"> Aksi </th>
                            </tr>
                        </thead> {{-- TABLE BODY --}} <tbody>
                            @forelse($barangKeluars as $index => $keluar)
                                <tr style="                  border-bottom:1px solid #E4E4D9;                  font-size:.85rem;              "
                                    onmouseover="this.style.background='#fafbf8'" onmouseout="this.style.background='#fff'">
                                    {{-- NO --}} <td
                                        style="                      padding:.7rem .6rem;                      text-align:center;                      color:#64748b;                      font-weight:700;                      white-space:nowrap;                  ">
                                        {{ $barangKeluars->firstItem() + $index }} </td> {{-- TANGGAL --}} <td
                                        style="                      padding:.7rem .6rem;                      white-space:nowrap;                      color:#475569;                  ">
                                        {{ $keluar->tanggal_keluar ? \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y') : '-' }}
                                    </td> {{-- KODE --}} <td
                                        style="                      padding:.7rem .6rem;                      font-weight:800;                      white-space:nowrap;                      color:#0B4F35;                  ">
                                        {{ $keluar->item->kode_barang ?? '-' }} </td> {{-- NAMA BARANG --}} <td
                                        style="                      padding:.7rem .6rem;                      max-width:180px;                      word-break:break-word;                  ">
                                        {{ $keluar->item->nama_barang ?? '-' }} </td> {{-- JUMLAH --}} <td
                                        style="                      padding:.7rem .6rem;                      text-align:center;                      white-space:nowrap;                  ">
                                        {{ number_format($keluar->jumlah_keluar, 0, ',', '.') }} </td>
                                    {{-- TOTAL HARGA --}} <td
                                        style="                      padding:.7rem .6rem;                      font-weight:800;                      white-space:nowrap;                  ">
                                        Rp {{ number_format($keluar->total_harga_jual, 0, ',', '.') }} </td>
                                    {{-- PENERIMA --}} <td
                                        style="                      padding:.7rem .6rem;                      max-width:150px;                      word-break:break-word;                  ">
                                        {{ $keluar->penerima ?? '-' }} </td> {{-- JENIS TRANSAKSI --}} <td
                                        style="                      padding:.7rem .6rem;                      max-width:140px;                  ">
                                        <span
                                            style="                          display:inline-block;                          background:#e0f2fe;                          color:#0369a1;                          padding:.3rem .55rem;                          border-radius:999px;                          font-size:.7rem;                          font-weight:800;                          line-height:1.2;                      ">
                                            {{ $keluar->jenis_transaksi ?? '-' }} </span>
                                    </td> {{-- LOKASI TUJUAN --}}
                                    <td
                                        style="                      padding:.7rem .6rem;                      max-width:150px;                      word-break:break-word;                  ">
                                        {{ $keluar->lokasi_tujuan ?? '-' }} </td> {{-- AKSI --}} <td
                                        style="                      padding:.7rem .6rem;                      white-space:nowrap;                  ">
                                        <div
                                            style="                          display:flex;                          gap:.35rem;                          align-items:center;                          justify-content:center;                      ">
                                            {{-- LIHAT --}} <a href="{{ route('barang-keluar.show', $keluar->id) }}"
                                                title="Lihat Detail"
                                                style="                              background:#60a5fa;                              color:#fff;                              width:34px;                              height:34px;                              min-width:34px;                              border-radius:.55rem;                              text-decoration:none;                              display:inline-flex;                              align-items:center;                              justify-content:center;                          ">
                                                <i class="fas fa-eye"></i> </a> {{-- EDIT --}} <a
                                                href="{{ route('barang-keluar.edit', $keluar->id) }}" title="Edit"
                                                style="                              background:#fbbf24;                              color:#1a1a1a;                              width:34px;                              height:34px;                              min-width:34px;                              border-radius:.55rem;                              text-decoration:none;                              display:inline-flex;                              align-items:center;                              justify-content:center;                          ">
                                                <i class="fas fa-pen"></i> </a> {{-- HAPUS --}} <form
                                                id="delete-form-{{ $keluar->id }}"
                                                action="{{ route('barang-keluar.destroy', $keluar->id) }}" method="POST"
                                                class="inline-flex m-0 p-0"> @csrf @method('DELETE')
                                                <button type="button" title="Hapus Barang Keluar"
                                                    onclick="openDeleteModal({{ $keluar->id }})"
                                                    class="inline-flex items-center justify-center                                  w-[34px] min-w-[34px] h-[34px]                                  rounded-[0.55rem]                                  bg-red-600 text-white                                  hover:bg-red-700                                  transition-all duration-200                                  shadow-sm hover:shadow                                  focus:outline-none focus:ring-2                                  focus:ring-red-500/30">
                                                    <i class="fas fa-trash text-sm"></i> </button>
                                            </form>
                                        </div>
                                    </td>
                            </tr> @empty <tr>
                                    <td colspan="10"
                                        style="                      padding:2rem;                      text-align:center;                      color:#999;                  ">
                                        <i class="fas fa-box-open"
                                            style="                          font-size:1.5rem;                          margin-bottom:.5rem;                      ">
                                        </i>
                                        <div>Tidak ada data Barang Keluar</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div> {{-- PAGINATION --}} <div style="margin-top:1.5rem;text-align:center;">
                    {{ $barangKeluars->appends(request()->query())->links() }} </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div id="deleteModal"
        style="
        display:none;
        position:fixed;
        inset:0;
        z-index:9999;
        background:rgba(15,23,42,.45);
        align-items:center;
        justify-content:center;
        padding:1rem;
    ">
        <div
            style="  width:100%;  max-width:420px;  background:#fff;  border-radius:1.25rem;  padding:1.5rem;  box-shadow:0 20px 50px rgba(0,0,0,.2);
        ">
            <div style="text-align:center;">
                <div
                    style="  width:52px;  height:52px;  margin:0 auto 1rem;  border-radius:50%;  background:#fef2f2;  color:#dc2626;  display:flex;  align-items:center;  justify-content:center;  font-size:1.25rem;      ">
                    <i class="fas fa-trash"></i>
                </div>
                <h3 style="  margin:0;  color:#0B4F35;  font-size:1.15rem;  font-weight:900;      "> Hapus Barang Keluar?
                </h3>
                <p style="  margin:.5rem 0 1.25rem;  color:#64748b;  font-size:.9rem;  line-height:1.5;      "> Data
                    transaksi ini akan dihapus secara permanen. Apakah Anda yakin ingin melanjutkan? </p>
                <div style="  display:flex;  gap:.75rem;  justify-content:center;      "> <button type="button"
                        onclick="closeDeleteModal()"
                        style="      flex:1;      padding:.75rem 1rem;      border:1px solid #E4E4D9;      background:#FAF9F6;      color:#475569;      border-radius:.75rem;      font-weight:800;      cursor:pointer;  ">
                        Batal </button> <button type="button" onclick="submitDeleteForm()"
                        style="      flex:1;      padding:.75rem 1rem;      border:0;      background:#dc2626;      color:#fff;      border-radius:.75rem;      font-weight:800;      cursor:pointer;  ">
                        <i class="fas fa-trash"></i> Hapus </button> </div>
            </div>
        </div>
    </div>

    <script>
        let deleteId = null;

        function openDeleteModal(id) {
            deleteId = id;
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeDeleteModal() {
            deleteId = null;
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function submitDeleteForm() {
            if (!deleteId) {
                return;
            }
            const form = document.getElementById('delete-form-' + deleteId);
            if (form) {
                form.submit();
            }
        }
        // Tutup modal ketika klik area luar
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('deleteModal');
            if (modal && event.target === modal) {
                closeDeleteModal();
            }
        });
        // Tutup dengan tombol ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
@endsection

<style>


    .container-laporan {
        max-width: 1200px;
        margin: auto;
        padding: 30px 20px;
        font-family: 'Segoe UI', sans-serif;
    }


    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }


    .back-button, .btn-secondary {
        padding: 8px 14px;
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease;
        border: none;
        outline: none;
    }
    .back-button:hover, .btn-secondary:hover {
        background-color: #d1d5db;
    }


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
    .filter-form button:hover {
        background-color: #2563eb;
    }


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


    /* Tombol Aksi / Detail */
    .td-action { display: flex; gap: 6px; justify-content: center; align-items: center; flex-wrap: nowrap; }
    .btn-action {
        padding: 6px 10px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
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
            text-align: left;                /* label rata kiri */
            padding: 10px 15px 10px 60%;
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


        /* Label per kolom (urut sesuai <th>) */
        td:nth-of-type(1):before  { content: "No"; }
        td:nth-of-type(2):before  { content: "Kode Barang"; }
        td:nth-of-type(3):before  { content: "Nama Barang"; }
        td:nth-of-type(4):before  { content: "Lokasi"; }
        td:nth-of-type(5):before  { content: "Kondisi"; }
        td:nth-of-type(6):before  { content: "Jumlah Keluar"; }
        td:nth-of-type(7):before  { content: "Harga Jual / Unit"; }
        td:nth-of-type(8):before  { content: "Total Harga"; }
        td:nth-of-type(9):before  { content: "Catatan"; }
        td:nth-of-type(10):before { content: "Penerima"; }
        td:nth-of-type(11):before { content: "User"; }
        td:nth-of-type(12):before { content: "Lokasi Tujuan"; }
        td:nth-of-type(13):before { content: "Tanggal Keluar"; }
        td:nth-of-type(14):before { content: "Detail"; }





        /* Aksi/Detail tetap rapi */
        .td-action { justify-content: flex-start; }
    }
</style>


<div class="container">
    <div class="header">
        <h4>Daftar Barang Keluar</h4>
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary">+ Tambah Barang Keluar</a>
    </div>


    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif



    {{-- Filter/Search --}}
    <form action="{{ route('barang-keluar.index') }}" method="GET" class="filter-form">
        <div class="form-group">
            <label for="search">Cari Barang</label>
            <input
                type="text"
                name="search"
                id="search"
                value="{{ request('search') }}"
                placeholder="Cari Kode Barang, Nama Barang, Penerima, Lokasi"
            >
        </div>


        <div class="form-group">
            <label for="lokasi">Lokasi</label>
            <select name="lokasi" id="lokasi">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                        {{ $lokasi->nama_lokasi }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group" style="flex-direction: row; gap: 10px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('barang-keluar.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- Tabel/Kartu --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Keluar</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Jumlah Keluar</th>
                    <th>Harga Jual / Unit</th>
                    <th>Total Harga</th>
                    <th>Catatan</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangKeluars as $index => $keluar)
                    <tr>
                        <td>{{ $barangKeluars->firstItem() + $index }}</td>
                        <td>{{ $keluar->tanggal_keluar ? \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y H:i:s') : '-' }}</td>
                        <td>{{ $keluar->kode_barang }}</td>
                        <td>{{ $keluar->item->nama_barang ?? '-' }}</td>
                        <td>{{ $keluar->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>{{ $keluar->kondisi->nama_kondisi ?? '-' }}</td>
                        <td><span class="v">{{ $keluar->jumlah_keluar }}</span></td>
                        <td><span class="v">Rp{{ number_format($keluar->harga_jual, 0, ',', '.') }}</span></td>
                        <td><span class="v">Rp{{ number_format($keluar->total_harga_jual, 0, ',', '.') }}</span></td>
                        <td>{{ $keluar->catatan ?? '-' }}</td>


                        <td class="text-start">
                            <a href="{{ route('barang-keluar.detail', $keluar->id) }}" class="btn btn-sm btn-info btn-action">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="14" class="text-center">Tidak ada data barang keluar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>


    {{-- Pagination --}}
    <div style="margin-top:20px; text-align:center;">
        {{ $barangKeluars->appends(request()->query())->links() }}
    </div>

{{--
    @if ($barangKeluars->total() > 0)
        <p class="text-muted">
            {{ $barangKeluars->total() }} hasil ditemukan.
            Halaman {{ $barangKeluars->currentPage() }} dari {{ $barangKeluars->lastPage() }}.
        </p>
    @else
        <p class="text-danger">Tidak ada hasil yang ditemukan.</p>
    @endif --}}
</div>
@endsection
