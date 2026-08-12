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
        min-width: 160px;
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
        min-width: 900px;
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
    :root{--emerald:#059669;--emerald-dark:#065f46;--emerald-soft:#ecfdf5;--slate:#1e293b;--border:#e2e8f0} body{background:#f7faf8;font-family:'Plus Jakarta Sans','Segoe UI',sans-serif}.container{max-width:1280px!important;padding:2rem 1.25rem 3rem}.header{background:#fff;border:1px solid var(--border);border-radius:1.25rem;padding:1.25rem 1.5rem;box-shadow:0 2px 8px rgba(15,23,42,.05)}.header h4{color:var(--emerald-dark);font-weight:900;font-size:1.75rem;margin:0}.filter-form{background:#fff!important;border:1px solid var(--border)!important;border-radius:1.25rem!important;padding:1.5rem!important;box-shadow:0 2px 8px rgba(15,23,42,.05)}.filter-form label{font-size:.7rem;font-weight:900;text-transform:uppercase;color:#64748b;letter-spacing:.05em}.filter-form input,.filter-form select{border:1px solid #cbd5e1;border-radius:.75rem;padding:.75rem}.filter-form button{background:var(--emerald)!important;border-radius:.75rem}.back-button{border-radius:.75rem}.export-buttons{display:flex;flex-wrap:wrap;gap:.75rem}.export-buttons a{margin:0;background:var(--emerald)!important;border-radius:.75rem;font-weight:800}.widget-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin:1.5rem 0}.widget{background:#fff;border:1px solid var(--border);border-radius:1.25rem;padding:1.25rem;box-shadow:0 2px 8px rgba(15,23,42,.05)}.widget-kicker{font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:#64748b}.widget-value{font-size:clamp(1.5rem,3vw,2rem);font-weight:900;color:var(--emerald-dark);margin:.5rem 0}.widget-sub{font-size:.85rem;color:#64748b}.widget-icon{display:grid;place-items:center;width:2.75rem;height:2.75rem;border-radius:.9rem;background:var(--emerald-soft);color:var(--emerald-dark);font-size:1.3rem}.table-wrapper{background:#fff;border:1px solid var(--border);border-radius:1.25rem;overflow:auto;box-shadow:0 2px 8px rgba(15,23,42,.05)}table{border-spacing:0!important}th{background:#f8fafc!important;color:#64748b;text-transform:uppercase;font-size:.7rem;letter-spacing:.05em}td{border-bottom:1px solid #f1f5f9!important;color:#334155;padding:1rem}tr:hover td{background:#f8fafc!important}.total-row td{background:#ecfdf5!important;color:#065f46;font-weight:900}@media(max-width:768px){.widget-grid{grid-template-columns:1fr}.container{padding:1rem}.widget-value{font-size:1.5rem}}
</style>




<div class="container">
    <div class="header">
        <h4>Laporan Omzet</h4>
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
            <label>Cari Barang (Kode/Nama):</label>
            <input type="text" name="search" placeholder="Cari kode atau nama barang..." value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label>Lokasi:</label>
            <select name="lokasi">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                        {{ $lokasi->nama_lokasi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Kondisi:</label>
            <select name="kondisi">
                <option value="">-- Semua Kondisi --</option>
                @foreach($kondisis as $kondisi)
                    <option value="{{ $kondisi->id }}" {{ request('kondisi') == $kondisi->id ? 'selected' : '' }}>
                        {{ $kondisi->nama_kondisi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('omzet.index') }}" class="back-button">Reset Filter</a>
        </div>
    </form>




    {{-- Export Buttons --}}
    <div class="export-buttons">
        <a href="{{ route('export.omzet.pdf', request()->query()) }}" target="_blank">📄 Cetak PDF</a>
        <a href="{{ route('export.omzet.excel', request()->query()) }}">📊 Export Excel</a>
    </div>




    @php
        $omzetTotal = $total_omzet ?? $totalOmzet ?? 0;
        $transactionCount = $data instanceof \Illuminate\Pagination\LengthAwarePaginator ? $data->total() : (is_countable($data) ? count($data) : collect($data)->count());
        $averageOmzet = $transactionCount > 0 ? $omzetTotal / $transactionCount : 0;
    @endphp
    <section class="widget-grid" aria-label="Ringkasan omzet">
        <article class="widget"><div class="widget-icon">↗</div><div class="widget-kicker">Total Omzet Pendapatan</div><div class="widget-value">Rp {{ number_format($omzetTotal, 0, ',', '.') }}</div><div class="widget-sub">Total pendapatan terbaca</div></article>
        <article class="widget"><div class="widget-icon">▤</div><div class="widget-kicker">Total Transaksi Penjualan</div><div class="widget-value">{{ $transactionCount }}</div><div class="widget-sub">Volume nota dan pesanan</div></article>
        <article class="widget"><div class="widget-icon">◎</div><div class="widget-kicker">Rata-rata Per Transaksi</div><div class="widget-value">Rp {{ number_format($averageOmzet, 0, ',', '.') }}</div><div class="widget-sub">Berdasarkan filter aktif</div></article>
    </section>

    {{-- Table --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    @php
                        function sortUrlBlade($field) {
                            return route('omzet.index', array_merge(request()->all(), [
                                'sort_by' => $field,
                                'sort_dir' => (request('sort_by') === $field && request('sort_dir') === 'asc') ? 'desc' : 'asc'
                            ]));
                        }
                    @endphp
                    <th>No</th>
                    <th><a href="{{ sortUrlBlade('nama_barang') }}">Nama Barang {!! request('sort_by') === 'nama_barang' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('tanggal') }}">Tanggal {!! request('sort_by') === 'tanggal' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('lokasi') }}">Lokasi {!! request('sort_by') === 'lokasi' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('kondisi') }}">Kondisi {!! request('sort_by') === 'kondisi' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('jumlah_keluar') }}">Jumlah Keluar {!! request('sort_by') === 'jumlah_keluar' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('harga_jual') }}">Harga Jual {!! request('sort_by') === 'harga_jual' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('omzet_item') }}">Omzet {!! request('sort_by') === 'omzet_item' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        {{-- nomor urut mengikuti halaman jika paginator --}}
                        <td>
                            {{ ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                ? (($data->firstItem() ?? 0) + $index)
                                : ($loop->iteration) }}
                        </td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('j/n/Y H:i:s') }}</td>
                        <td>{{ $item['lokasi'] }}</td>
                        <td>{{ $item['kondisi'] }}</td>
                        <td>{{ $item['jumlah_keluar'] }}</td>
                        <td>Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item['omzet_item'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="7">Total Omzet</td>
                    <td>Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>




    {{-- Pagination + summary (selalu tampil) --}}
    @php
        $isPaginator = $data instanceof \Illuminate\Pagination\LengthAwarePaginator;
        $total = $isPaginator ? $data->total() : (is_countable($data) ? count($data) : collect($data)->count());
        $first = $isPaginator ? ($data->firstItem() ?? ($total ? 1 : 0)) : ($total ? 1 : 0);
        $last  = $isPaginator ? ($data->lastItem()  ?? $total) : $total;
    @endphp
    <div style="margin-top:20px; text-align:center;">
        <div class="small text-muted">
            Showing {{ $first }} to {{ $last }} of {{ $total }} results
        </div>
        <div class="ms-auto">
            @if($isPaginator)
                {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
            @endif
        </div>
    </div>
</div>
@endsection




@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("input[type=date]", {
            dateFormat: "d/m/Y",
            locale: "id"
        });
    });
</script>
@endpush
