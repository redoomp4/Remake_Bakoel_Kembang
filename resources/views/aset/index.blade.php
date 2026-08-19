@extends('layouts.app')




@section('content')
<style>
    .container-laporan { max-width:1200px;margin:auto;padding:30px 20px;font-family:'Segoe UI',sans-serif; }

    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }




    /* Filter Form */
    .filter-form {
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
        flex: 1;
        min-width: 180px;
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




    /* Tombol */
    .filter-form .action-group {
        display: flex;
        flex-direction: row;
        gap: 10px;
        flex-shrink: 0;
    }
    .filter-form button {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .filter-form button:hover { background-color: #2563eb; }
    .back-button {
        padding: 8px 14px;
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease;
        text-align: center;
    }
    .back-button:hover { background-color: #d1d5db; }




    /* Export */
    .export-buttons { margin-bottom: 20px; }
    .export-buttons a {
        padding: 8px 12px;
        background-color: #2563eb;
        color: #fff;
        border-radius: 6px;
        margin-right: 10px;
        text-decoration: none;
        font-size: 14px;
    }
    .export-buttons a:hover { background-color: #1d4ed8; }




    /* Table */
    .table-wrapper { overflow-x: auto; }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        min-width: 800px;
    }
    th, td {
        padding: 12px;
        background-color: white;
        text-align: center;
    }
    th {
        background-color: #f3f4f6;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
    }
    th a { color: inherit; text-decoration: none; }
    th a:hover { text-decoration: underline; }
    tr { box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); border-radius: 8px; }
    .total-row { background-color: yellow; font-weight: bold; }




    /* Responsif */
    @media(max-width: 768px) {
        .filter-form { flex-direction: column; gap: 12px; align-items: stretch; }
        .filter-form .form-group,
        .filter-form .action-group { width: 100%; }
        .filter-form .action-group { flex-direction: column; }
        .filter-form .action-group .back-button,
        .filter-form .action-group button { width: 100%; }
        .container-laporan { padding: 15px 10px; }
    }
    :root{--emerald:#059669;--emerald-dark:#065f46;--emerald-soft:#ecfdf5;--slate:#1e293b;--border:#e2e8f0} body{background:#f7faf8;font-family:'Plus Jakarta Sans','Segoe UI',sans-serif}.container{max-width:1280px!important;padding:2rem 1.25rem 3rem}.header{background:#fff;border:1px solid var(--border);border-radius:1.25rem;padding:1.25rem 1.5rem;box-shadow:0 2px 8px rgba(15,23,42,.05)}.header h4{color:var(--emerald-dark);font-weight:900;font-size:1.75rem;margin:0}.filter-form{background:#fff!important;border:1px solid var(--border)!important;border-radius:1.25rem!important;padding:1.5rem!important;box-shadow:0 2px 8px rgba(15,23,42,.05)}.filter-form label{font-size:.7rem;font-weight:900;text-transform:uppercase;color:#64748b;letter-spacing:.05em}.filter-form input,.filter-form select{border:1px solid #cbd5e1;border-radius:.75rem;padding:.75rem}.filter-form button{background:var(--emerald)!important;border-radius:.75rem}.back-button{border-radius:.75rem}.export-buttons{display:flex;flex-wrap:wrap;gap:.75rem}.export-buttons a{margin:0;background:var(--emerald)!important;border-radius:.75rem;font-weight:800}.widget-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin:1.5rem 0}.widget{background:#fff;border:1px solid var(--border);border-radius:1.25rem;padding:1.25rem;box-shadow:0 2px 8px rgba(15,23,42,.05)}.widget-kicker{font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:#64748b}.widget-value{font-size:clamp(1.5rem,3vw,2rem);font-weight:900;color:var(--emerald-dark);margin:.5rem 0}.widget-sub{font-size:.85rem;color:#64748b}.widget-icon{display:grid;place-items:center;width:2.75rem;height:2.75rem;border-radius:.9rem;background:var(--emerald-soft);color:var(--emerald-dark);font-size:1.3rem}.meter{height:.75rem;background:#d1fae5;border-radius:999px;overflow:hidden;display:flex;margin-top:1rem}.meter-good{width:85%;background:#10b981}.meter-bad{width:15%;background:#f43f5e}.meter-labels{display:flex;justify-content:space-between;margin-top:.5rem;font-size:.7rem;font-weight:800}.table-wrapper{background:#fff;border:1px solid var(--border);border-radius:1.25rem;overflow:auto;box-shadow:0 2px 8px rgba(15,23,42,.05)}table{border-spacing:0!important}th{background:#f8fafc!important;color:#64748b;text-transform:uppercase;font-size:.7rem;letter-spacing:.05em}td{border-bottom:1px solid #f1f5f9!important;color:#334155;padding:1rem}tr:hover td{background:#f8fafc!important}.total-row td{background:#ecfdf5!important;color:#065f46;font-weight:900}@media(max-width:768px){.widget-grid{grid-template-columns:1fr}.container{padding:1rem}.widget-value{font-size:1.5rem}}
</style>




<div class="container">
    <div class="header">
        <h4>Laporan Total Aset</h4>
    </div>




    {{-- Filter --}}
    <form method="GET" class="filter-form">
        <div class="form-group">
            <label>Tanggal Mulai:</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div class="form-group">
            <label>Tanggal Selesai:</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div class="form-group">
            <label>Lokasi:</label>
            <select name="lokasi">
                <option value="">-- Semua Lokasi --</option>
                @foreach($listLokasi as $lokasi)
                    <option value="{{ $lokasi }}" {{ request('lokasi') == $lokasi ? 'selected' : '' }}>
                        {{ $lokasi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Kondisi:</label>
            <select name="kondisi">
                <option value="">-- Semua Kondisi --</option>
                @foreach($listKondisi as $kondisi)
                    <option value="{{ $kondisi }}" {{ request('kondisi') == $kondisi ? 'selected' : '' }}>
                        {{ $kondisi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" name="nama_barang" placeholder="Nama Barang..." value="{{ request('nama_barang') }}">
        </div>
        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('aset.index') }}" class="back-button">Reset Filter</a>
        </div>
    </form>




    {{-- Tombol Ekspor --}}
    @php
        $query = request()->except(['page']);
        if (request('start_date')) $query['tanggal_mulai'] = request('start_date');
        if (request('end_date')) $query['tanggal_selesai'] = request('end_date');
    @endphp


    <div class="export-buttons">
        <a href="{{ route('export.aset.pdf', $query) }}" target="_blank">📄 Cetak PDF</a>
        <a href="{{ route('export.aset.excel', $query) }}">📊 Export Excel</a>
    </div>






    <section class="widget-grid" aria-label="Ringkasan aset">
        <article class="widget"><div class="widget-icon">▣</div><div class="widget-kicker">Total Nilai Aset</div><div class="widget-value">Rp {{ number_format($totalAset ?? 0, 0, ',', '.') }}</div><div class="widget-sub">Total akumulasi aset kebun dan toko</div></article>
        <article class="widget"><div class="widget-icon">▤</div><div class="widget-kicker">Total Unit & Variasi Aset</div><div class="widget-value">{{ $grouped->total() ?? $grouped->count() }}</div><div class="widget-sub">Barang terdaftar dalam inventaris</div></article>
        <article class="widget"><div class="widget-icon">✓</div><div class="widget-kicker">Status Kesehatan Aset</div><div class="widget-value">Layak Dipakai</div><div class="widget-sub">Ringkasan kondisi aset terdata</div><div class="meter"><div class="meter-good"></div><div class="meter-bad"></div></div><div class="meter-labels"><span style="color:#047857">Layak / Bagus</span><span style="color:#e11d48">Perlu Perbaikan</span></div></article>
    </section>

    {{-- Tabel --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    @php
                        $sortBy = request('sort_by');
                        $columns = [
                            'nama_barang' => 'Nama Barang',
                            'lokasi'      => 'Lokasi',
                            'kondisi'     => 'Kondisi',
                            'stok_akhir'  => 'Stok Akhir',
                            'harga_beli'  => 'Harga Beli',
                            'jumlah_aset' => 'Jumlah Aset',
                        ];
                    @endphp
                    <th>No</th>
                    @foreach($columns as $key => $label)
                        <th>
                            <a href="{{ route('aset.index', array_merge(request()->all(), ['sort_by' => $key, 'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'])) }}">
                                {{ $label }}
                                @if($sortBy === $key)
                                    {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                                @else
                                    ▲▼
                                @endif
                            </a>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $index => $row)
                    <tr>
                        {{-- nomor urut mengikuti halaman --}}
                        <td>{{ ($grouped->firstItem() ?? 0) + $index }}</td>
                        <td>{{ $row['nama_barang'] }}</td>
                        <td>{{ $row['lokasi'] }}</td>
                        <td>{{ $row['kondisi'] }}</td>
                        <td>{{ $row['stok_akhir'] }}</td>
                        <td>Rp {{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row['jumlah_aset'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" class="text-center">Total Aset</td>
                    <td>Rp {{ number_format($totalAset, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>




    {{-- Pagination + summary (selalu tampil meski 1 halaman) --}}
    @if ($grouped instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:20px; text-align:center;">
            <div class="small text-muted">
                Showing {{ $grouped->firstItem() ?? ($grouped->total() ? 1 : 0) }}
                to {{ $grouped->lastItem() ?? $grouped->total() }}
                of {{ $grouped->total() }} results
            </div>
            <div class="ms-auto">
                {!! $grouped->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    @endif
</div>

@endsection




@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("input[name='start_date']", {
                dateFormat: "d/m/Y",
            });
            flatpickr("input[name='end_date']", {
                dateFormat: "d/m/Y",
            });
        });
    </script>
@endsection
