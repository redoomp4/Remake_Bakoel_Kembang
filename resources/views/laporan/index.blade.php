@extends('layouts.app')

@section('content')
    <style>
        :root {
            --emerald: #0B4F35;
            --emerald-dark: #065f46;
            --emerald-soft: #ecfdf5;
            --slate: #475569;
            --border: #e2e8f0;
            --offwhite: #f7f8f5;
        }

        /* --------------------------------------------------------------------------
                 | PAGE / CONTAINER
                 | -------------------------------------------------------------------------- */

        body {
            background: var(--offwhite);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        }

        main>.container {
            width: 100%;
            max-width: 1280px !important;
            min-width: 0;
            flex: 1 1 0%;
            box-sizing: border-box;
            margin-left: auto;
            margin-right: auto;
            padding: 2rem 1.25rem 3rem;
        }

        /* --------------------------------------------------------------------------
                 | HEADER
                 | -------------------------------------------------------------------------- */

        .header {
            width: 100%;
            box-sizing: border-box;
            background: #fff;
            border: 1px solid var(--border);
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

        .header h4 {
            margin: 0;
            color: var(--emerald);
            font-weight: 900;
            font-size: 1.75rem;
            letter-spacing: -.025em;
        }

        /* --------------------------------------------------------------------------
                 | BACK BUTTON
                 | -------------------------------------------------------------------------- */

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .7rem 1rem;
            background: #eef1eb;
            color: var(--emerald);
            border-radius: .75rem;
            border: 0;
            font-size: .85rem;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
            transition: all .2s ease;
            box-sizing: border-box;
        }

        .back-button:hover {
            background: #e2e8df;
            color: var(--emerald-dark);
            transform: translateY(-1px);
        }

        /* --------------------------------------------------------------------------
                 | FILTER
                 | -------------------------------------------------------------------------- */

        .filter-form {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            background: #fff !important;
            border: 1px solid var(--border) !important;
            border-radius: 1.25rem !important;
            padding: 1.5rem !important;
            margin-bottom: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        .filter-form .form-group {
            display: flex;
            flex-direction: column;
            flex: 1 1 180px;
            min-width: 0;
        }

        .filter-form label {
            color: #64748b;
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .4rem;
        }

        .filter-form input,
        .filter-form select {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            padding: .75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: .75rem;
            background: #fff;
            font-family: inherit;
            font-size: .9rem;
            color: #334155;
        }

        .filter-form input:focus,
        .filter-form select:focus {
            border-color: var(--emerald);
            outline: 0;
            box-shadow: 0 0 0 4px rgba(11, 79, 53, .12);
        }

        /* --------------------------------------------------------------------------
                 | ACTION FILTER
                 | -------------------------------------------------------------------------- */

        .filter-form .action-group {
            display: flex;
            flex-direction: row;
            gap: .75rem;
            flex: 0 0 auto;
            min-width: 0;
        }

        .filter-form button {
            padding: .75rem 1rem;
            background: var(--emerald);
            color: #fff;
            border: 0;
            border-radius: .75rem;
            font-family: inherit;
            font-size: .85rem;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition: all .2s ease;
        }

        .filter-form button:hover {
            background: var(--emerald-dark);
            transform: translateY(-1px);
        }

        /* --------------------------------------------------------------------------
                 | EXPORT BUTTONS
                 | -------------------------------------------------------------------------- */

        .export-buttons {
            width: 100%;
            min-width: 0;
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            margin-bottom: 1.5rem;
        }

        .export-buttons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .7rem 1rem;
            color: #fff;
            background: var(--emerald);
            border-radius: .75rem;
            margin: 0 !important;
            font-size: .85rem;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
            transition: all .2s ease;
            box-sizing: border-box;
        }

        .export-buttons a:hover {
            background: var(--emerald-dark);
            transform: translateY(-1px);
        }

        .export-buttons a:first-child {
            background: #e11d48;
        }

        .export-buttons a:first-child:hover {
            background: #be123c;
        }

        .export-buttons a:nth-child(2),
        .export-buttons a:nth-child(3) {
            background: #059669;
        }

        .export-buttons a:nth-child(2):hover,
        .export-buttons a:nth-child(3):hover {
            background: #047857;
        }

        /* --------------------------------------------------------------------------
                 | TABLE
                 | -------------------------------------------------------------------------- */

        .table-responsive {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            overflow-x: auto;
            overflow-y: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 900px;
        }

        .table-responsive th {
            padding: .9rem .75rem;
            background: #f8fafc !important;
            color: #64748b;
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
            border-bottom: 1px solid var(--border);
            text-align: center;
        }

        .table-responsive th a {
            color: inherit;
            text-decoration: none;
            white-space: nowrap;
        }

        .table-responsive th a:hover {
            text-decoration: underline;
        }

        .table-responsive td {
            padding: .9rem .75rem;
            background: #fff;
            color: #334155;
            font-size: .85rem;
            font-weight: 500;
            white-space: nowrap;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .table-responsive tbody tr:hover td {
            background: #f8fafc !important;
        }

        /* --------------------------------------------------------------------------
                 | PAGINATION
                 | -------------------------------------------------------------------------- */

        .pagination-wrapper {
            width: 100%;
            min-width: 0;
            margin-top: 1.25rem;
            text-align: center;
        }

        .pagination-wrapper .small {
            color: #64748b;
            font-size: .8rem;
        }

        /* --------------------------------------------------------------------------
                 | CHART
                 | -------------------------------------------------------------------------- */

        #stokChart {
            width: 100% !important;
            max-width: 100%;
            margin-top: 2rem !important;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            padding: 1rem;
            box-sizing: border-box;
        }

        /* --------------------------------------------------------------------------
                 | 1024px - DESKTOP
                 | -------------------------------------------------------------------------- */

        @media (min-width: 1024px) {
            main>.container {
                width: 100%;
                max-width: 1280px !important;
                min-width: 0;
                flex: 1 1 0%;
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }

            .filter-form .form-group {
                flex: 1 1 160px;
                min-width: 0;
            }
        }

        /* --------------------------------------------------------------------------
                 | TABLET
                 | -------------------------------------------------------------------------- */

        @media (max-width: 1023px) {
            main>.container {
                width: 100%;
                max-width: 100% !important;
                min-width: 0;
                padding: 1.5rem 1rem 2.5rem;
            }

            .filter-form {
                gap: .85rem;
            }

            .filter-form .form-group {
                flex: 1 1 calc(50% - .85rem);
                min-width: 0;
            }

            .filter-form .action-group {
                width: 100%;
            }
        }

        /* --------------------------------------------------------------------------
                 | MOBILE
                 | -------------------------------------------------------------------------- */

        @media (max-width: 768px) {
            main>.container {
                width: 100%;
                max-width: 100% !important;
                min-width: 0;
                padding: 1rem .75rem 2rem;
            }

            .header {
                padding: 1rem;
                border-radius: 1rem;
                align-items: flex-start;
            }

            .header h4 {
                font-size: 1.35rem;
                line-height: 1.35;
            }

            .header .back-button {
                width: 100%;
                white-space: normal;
                line-height: 1.4;
            }

            .filter-form {
                padding: 1rem !important;
                border-radius: 1rem !important;
                flex-direction: column;
                align-items: stretch;
                gap: .75rem;
            }

            .filter-form .form-group {
                width: 100%;
                flex: none;
                min-width: 0;
            }

            .filter-form .action-group {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: .65rem;
            }

            .filter-form button,
            .filter-form .action-group .back-button {
                width: 100%;
            }

            .export-buttons {
                display: grid;
                grid-template-columns: 1fr;
                gap: .65rem;
            }

            .export-buttons a {
                width: 100%;
            }

            .table-responsive {
                border-radius: 1rem;
            }

            .table-responsive table {
                min-width: 900px;
            }

            .pagination-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .pagination-wrapper .ms-auto {
                min-width: max-content;
            }

            #stokChart {
                min-height: 260px;
                padding: .75rem;
                border-radius: 1rem;
            }
        }

        /* --------------------------------------------------------------------------
                 | MOBILE KECIL
                 | -------------------------------------------------------------------------- */

        @media (max-width: 480px) {
            main>.container {
                padding-left: .6rem;
                padding-right: .6rem;
            }

            .header h4 {
                font-size: 1.2rem;
            }

            .header {
                padding: .9rem;
            }

            .filter-form {
                padding: .85rem !important;
            }

            .filter-form input,
            .filter-form select {
                font-size: .85rem;
                padding: .7rem .85rem;
            }

            .table-responsive table {
                min-width: 900px;
            }

            #stokChart {
                min-height: 240px;
            }
        }
    </style>

    @php $role = auth()->user()->role; @endphp

    <div class="container">
        <div class="header">
            <h4>Laporan Transaksi & Stok Barang</h4>
            @if (in_array($role, ['gudang', 'penjual', 'superadmin']))
                <a href="{{ route('laporan.arus') }}" class="back-button">
                    ← Lihat Laporan Arus Barang (Detail Transaksi)
                </a>
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
                    @foreach ($lokasis as $lokasi)
                        @php
                            $lokasiId = is_array($lokasi) ? $lokasi['id'] ?? $lokasi['nama_lokasi'] : $lokasi;
                            $lokasiName = is_array($lokasi) ? $lokasi['nama_lokasi'] ?? '-' : $lokasi;
                        @endphp
                        <option value="{{ $lokasiId }}" {{ request('lokasi') == $lokasiId ? 'selected' : '' }}>
                            {{ $lokasiName }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- Bawa juga sort_by/sort_dir saat submit --}}
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div class="action-group">
                <button type="submit">Terapkan Filter</button>
                <a href="{{ route('laporan') }}" class="back-button">
                    Reset Filter
                </a>
            </div>
        </form>

        @if (in_array($role, ['gudang', 'penjual', 'superadmin']))
            <div class="export-buttons">
                {{-- request()->query() sudah otomatis membawa start_date & end_date --}}
                <a href="{{ route('laporan.pdf', request()->query()) }}" target="_blank">
                    📄 Cetak PDF
                </a>
                <a href="{{ route('laporan.excel', array_merge(request()->query(), ['format' => 'xlsx'])) }}">
                    📊 Export Excel (.xlsx)
                </a>
                <a href="{{ route('laporan.excel', array_merge(request()->query(), ['format' => 'csv'])) }}"
                    style="background-color: #059669;">
                    📄 Export CSV (.csv)
                </a>
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
                        @foreach ($columns as $key => $label)
                            <th>
                                <a
                                    href="{{ route(
                                        'laporan',
                                        array_merge(request()->all(), [
                                            'sort_by' => $key,
                                            'sort_dir' => $sortBy === $key && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    {{ $label }}
                                    @if ($sortBy === $key)
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
                            <td style="text-align:left">
                                {{ $item['nama_barang'] }}
                            </td>
                            <td>
                                Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}
                            </td>
                            <td>{{ $item['total_masuk'] }}</td>
                            <td>{{ $item['total_keluar'] }}</td>
                            <td>{{ $item['stok_akhir'] }}</td>
                            <td>
                                {{ is_array($item['lokasi']) ? $item['lokasi']['nama_lokasi'] ?? '-' : $item['lokasi'] }}
                            </td>
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
            $first = $isPaginator ? $data->firstItem() ?? ($total ? 1 : 0) : ($total ? 1 : 0);
            $last = $isPaginator ? $data->lastItem() ?? $total : $total;
        @endphp

        <div class="pagination-wrapper">
            <div class="small text-muted">
                Showing {{ $first }} to {{ $last }} of {{ $total }} results
            </div>
            <div class="ms-auto">
                @if ($isPaginator)
                    {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
                @endif
            </div>
        </div>

        @php
            $chartItems = $isPaginator ? $data->items() : $data;
        @endphp

        @if ($total > 0)
            <canvas id="stokChart" height="150"></canvas>
        @endif
    </div>
@endsection

@section('scripts')
    @if (
        ($data instanceof \Illuminate\Pagination\LengthAwarePaginator && $data->total() > 0) ||
            (!($data instanceof \Illuminate\Pagination\LengthAwarePaginator) && count($data) > 0))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const labels = {!! json_encode(collect($chartItems)->pluck('nama_barang')->values()) !!};
            const stokData = {!! json_encode(collect($chartItems)->pluck('stok_akhir')->values()) !!};
            const ctx = document.getElementById('stokChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Stok Akhir',
                        data: stokData,
                        backgroundColor: 'rgba(16, 185, 129, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endif
@endsection
