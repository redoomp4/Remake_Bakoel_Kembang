@extends('layouts.app')

@section('content')
<style>
    .container-laporan { max-width:1200px;margin:auto;padding:30px 20px;font-family:'Segoe UI',sans-serif; }
    .header{margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
    .back-button{padding:8px 14px;background:#e5e7eb;color:#111827;border-radius:6px;font-size:14px;text-decoration:none;transition:background .2s;text-align:center;}
    .back-button:hover{background:#d1d5db;}

    .filter-form{background:#f9fafb;border:1px solid #d1d5db;border-radius:10px;padding:15px 20px;margin-bottom:25px;display:flex;flex-wrap:wrap;gap:20px;align-items:flex-end;}
    .filter-form .form-group{display:flex;flex-direction:column;flex:1;min-width:180px;}
    .filter-form label{font-size:14px;font-weight:500;margin-bottom:4px;}
    .filter-form input,.filter-form select{padding:6px 10px;border:1px solid #ccc;border-radius:6px;font-size:14px;}
    .filter-form .action-group{display:flex;flex-direction:row;gap:10px;flex-shrink:0;}
    .filter-form button{padding:8px 16px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-weight:500;cursor:pointer;transition:background .2s;}
    .filter-form button:hover{background:#2563eb;}

    .export-buttons{margin-bottom:20px;}
    .export-buttons a{padding:8px 12px;background:#2563eb;color:#fff;border-radius:6px;margin-right:10px;text-decoration:none;font-size:14px;}
    .export-buttons a:hover{background:#1d4ed8;}

    table{width:100%;border-collapse:separate;border-spacing:0 10px;}
    th,td{padding:12px;background:#fff;text-align:center;}
    th{background:#f3f4f6;font-weight:600;border-bottom:1px solid #ddd;}
    th a{color:inherit;text-decoration:none;}
    th a:hover{text-decoration:underline;}
    tr{box-shadow:0 2px 4px rgba(0,0,0,.05);border-radius:8px;}

    @media(max-width:768px){
        .filter-form{flex-direction:column;gap:12px;align-items:stretch;}
        .filter-form .form-group,.filter-form .action-group{width:100%;}
        .filter-form .action-group{flex-direction:column;}
        .filter-form .action-group .back-button,.filter-form .action-group button{width:100%;}
        .container-laporan{padding:15px 10px;}
    }
</style>

@php $role = auth()->user()->role; @endphp

<div class="container mt-5">
    <div class="header">
        <h4>Laporan Stok Barang</h4>
        @if ($role === 'gudang')
            <a href="{{ route('laporan.arus') }}" class="back-button">← Lihat Laporan Arus Barang</a>
        @endif
    </div>

    {{-- Filter Form --}}
    <form method="GET" class="filter-form">
        {{-- ✨ Tambahan filter tanggal --}}
        <div class="form-group">
            <label>Tanggal Mulai:</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div class="form-group">
            <label>Tanggal Selesai:</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
        </div>
        
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" name="nama_barang" placeholder="Nama Barang..." value="{{ request('nama_barang') }}">
        </div>
        <div class="form-group">
            <label>Kode Barang:</label>
            <input type="text" name="kode_barang" placeholder="Kode Barang..." value="{{ request('kode_barang') }}">
        </div>
        <div class="form-group">
            <label>Lokasi:</label>
            <select name="lokasi">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasis as $lokasi)
                    @php
                        $lokasiId   = is_array($lokasi) ? ($lokasi['id'] ?? $lokasi['nama_lokasi']) : $lokasi;
                        $lokasiName = is_array($lokasi) ? ($lokasi['nama_lokasi'] ?? '-') : $lokasi;
                    @endphp
                    <option value="{{ $lokasiId }}" {{ request('lokasi') == $lokasiId ? 'selected' : '' }}>
                        {{ $lokasiName }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Bawa juga sort_by/sort_dir saat submit agar tidak hilang --}}
        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
        <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">

        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('laporan') }}" class="back-button">Reset Filter</a>
        </div>
    </form>

    @if ($role === 'gudang')
        <div class="export-buttons">
            {{-- request()->query() sudah otomatis membawa start_date & end_date --}}
            <a href="{{ route('laporan.pdf', request()->query()) }}" target="_blank">📄 Cetak PDF</a>
            <a href="{{ route('laporan.excel', request()->query()) }}">📊 Export Excel</a>
        </div>
    @endif

    @php
        $sortBy = request('sort_by');
        $columns = [
            'kode_barang' => 'Kode Barang',
            'nama_barang' => 'Nama Barang',
            'harga_dasar' => 'Harga Dasar',
            'total_masuk' => 'Total Masuk',
            'total_keluar' => 'Total Keluar',
            'stok_akhir' => 'Stok Akhir',
            'lokasi' => 'Lokasi',
            'username' => 'Username',
        ];
    @endphp

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    @foreach($columns as $key => $label)
                        <th>
                            <a href="{{ route('laporan', array_merge(request()->all(), ['sort_by' => $key, 'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'])) }}">
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
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item['kode_barang'] }}</td>
                        <td style="text-align:left">{{ $item['nama_barang'] }}</td>
                        <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                        <td>{{ $item['total_masuk'] }}</td>
                        <td>{{ $item['total_keluar'] }}</td>
                        <td>{{ $item['stok_akhir'] }}</td>
                        <td>{{ is_array($item['lokasi']) ? ($item['lokasi']['nama_lokasi'] ?? '-') : $item['lokasi'] }}</td>
                        <td>{{ $item['username'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination + summary --}}
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

    @php $chartItems = $isPaginator ? $data->items() : $data; @endphp
    @if ($total > 0)
        <canvas id="stokChart" height="150" style="margin-top: 40px;"></canvas>
    @endif
</div>
@endsection

@section('scripts')
@if (($data instanceof \Illuminate\Pagination\LengthAwarePaginator && $data->total() > 0) || (!($data instanceof \Illuminate\Pagination\LengthAwarePaginator) && count($data) > 0))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels   = {!! json_encode(collect($chartItems)->pluck('nama_barang')->values()) !!};
    const stokData = {!! json_encode(collect($chartItems)->pluck('stok_akhir')->values()) !!};

    const ctx = document.getElementById('stokChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Stok Akhir', data: stokData, backgroundColor: 'rgba(54, 162, 235, 0.6)' }] },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
</script>
@endif
@endsection
