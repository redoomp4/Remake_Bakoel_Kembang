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

        /*
                |--------------------------------------------------------------------------
                | PAGE / CONTAINER
                |--------------------------------------------------------------------------
                | Penting:
                | .container adalah child langsung dari <main> yang pada >=1024px
                | menjadi flex-row bersama sidebar.
                |
                | min-width:0 + flex:1 mencegah halaman terdorong/menjorok ke kanan.
                */
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

        /*
                |--------------------------------------------------------------------------
                | HEADER
                |--------------------------------------------------------------------------
                */
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

        /*
                |--------------------------------------------------------------------------
                | BACK BUTTON
                |--------------------------------------------------------------------------
                */
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
        }

        .back-button:hover {
            background: #e2e8df;
            color: var(--emerald-dark);
            transform: translateY(-1px);
        }

        /*
                |--------------------------------------------------------------------------
                | FILTER
                |--------------------------------------------------------------------------
                */
        .filter-form {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            padding: 1.5rem;
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

        /*
                |--------------------------------------------------------------------------
                | ACTION FILTER
                |--------------------------------------------------------------------------
                */
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

        /*
                |--------------------------------------------------------------------------
                | EXPORT BUTTONS
                |--------------------------------------------------------------------------
                */
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

        /*
                |--------------------------------------------------------------------------
                | TABLE
                |--------------------------------------------------------------------------
                | table boleh lebih lebar dari layar, tetapi YANG BOLEH overflow
                | hanya .table-responsive.
                */
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
        }

        .table-responsive tbody tr:hover td {
            background: #f8fafc;
        }

        /*
                |--------------------------------------------------------------------------
                | PAGINATION
                |--------------------------------------------------------------------------
                */
        .pagination-wrapper {
            width: 100%;
            min-width: 0;
            margin-top: 1.25rem;
            text-align: center;
        }

        /*
                |--------------------------------------------------------------------------
                | 1024px - DESKTOP
                |--------------------------------------------------------------------------
                | Ini bagian paling penting untuk kasus kamu.
                */
        @media (min-width: 1024px) {
            main>.container {
                /*
                         * Jangan pakai lebar fixed.
                         * Biarkan mengambil sisa ruang setelah sidebar.
                         */
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

        /*
                |--------------------------------------------------------------------------
                | TABLET
                |--------------------------------------------------------------------------
                */
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

        /*
                |--------------------------------------------------------------------------
                | MOBILE
                |--------------------------------------------------------------------------
                */
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
            }

            .back-button {
                width: 100%;
            }

            .filter-form {
                padding: 1rem;
                border-radius: 1rem;
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
        }

        /*
                |--------------------------------------------------------------------------
                | MOBILE KECIL
                |--------------------------------------------------------------------------
                */
        @media (max-width: 480px) {
            main>.container {
                padding-left: .6rem;
                padding-right: .6rem;
            }

            .header h4 {
                font-size: 1.2rem;
            }

            .filter-form {
                padding: .85rem;
            }

            .table-responsive table {
                min-width: 900px;
            }
        }
    </style>
    <div class="container">
        <div class="header">
            <h4>Laporan Arus Barang</h4>
            <a href="{{ route('laporan') }}" class="back-button">
                ← Kembali ke Laporan Stok
            </a>
        </div>
        {{-- Filter Form --}}
        <form class="filter-form" method="GET" action="{{ route('laporan.arus') }}">
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
                <label>Lokasi:</label>
                <select name="lokasi" class="form-control">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach ($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Pencarian (Nama/Kode/Pihak):</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari barang atau pihak...">
            </div>
            {{-- pertahankan sort saat submit --}}
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div class="action-group">
                <button type="submit">Terapkan Filter</button>
                <a href="{{ route('laporan.arus') }}" class="back-button">
                    Reset Filter
                </a>
            </div>
        </form>
        <div class="export-buttons">
            {{-- query() membawa lokasi/search/start_date/end_date --}}
            <a href="{{ route('laporan.arus.pdf', request()->query()) }}" target="_blank">
                📄 Cetak PDF
            </a>
            <a href="{{ route('laporan.arus.excel', array_merge(request()->query(), ['format' => 'xlsx'])) }}">
                📊 Export Excel (.xlsx)
            </a>
            <a href="{{ route('laporan.arus.excel', array_merge(request()->query(), ['format' => 'csv'])) }}">
                📄 Export CSV (.csv)
            </a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    @php
                        $sortBy = request('sort_by');
                        $columns = [
                            'kode_barang' => 'Kode Barang',
                            'nama_barang' => 'Nama Barang',
                            'harga_dasar' => 'Harga Dasar',
                            'tanggal' => 'Tanggal Transaksi',
                            'jumlah_masuk' => 'Jumlah Masuk',
                            'jumlah_keluar' => 'Jumlah Keluar',
                            'total_barang' => 'Total Barang',
                            'lokasi' => 'Lokasi',
                            'pihak' => 'Pihak',
                        ];
                    @endphp
                    <tr>
                        @foreach ($columns as $key => $label)
                            <th>
                                <a
                                    href="{{ route(
                                        'laporan.arus',
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
                    @foreach ($paginated as $row)
                        @php
                            $kode = $row['kode_barang'] ?? '-';
                            $pihak = $row['pihak'] ?? null;
                            if (is_string($pihak)) {
                                $json = json_decode($pihak, true);
                                $pihak = is_array($json) ? $json : ['nama' => $pihak];
                            }
                            $tgl = !empty($row['tanggal']) ? \Carbon\Carbon::parse($row['tanggal']) : null;
                        @endphp
                        <tr>
                            <td>{{ $kode }}</td>
                            <td>
                                {{ $row['nama_barang'] ?? '-' }}
                            </td>
                            <td>
                                Rp {{ number_format((float) ($row['harga_dasar'] ?? 0)) }}
                            </td>
                            <td>
                                {{ $tgl ? $tgl->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td>
                                {{ $row['jumlah_masuk'] ?? 0 }}
                            </td>
                            <td>
                                {{ $row['jumlah_keluar'] ?? 0 }}
                            </td>
                            <td>
                                {{ $row['total_barang'] ?? 0 }}
                            </td>
                            <td>
                                {{ is_array($row['lokasi'] ?? null) ? $row['lokasi']['nama_lokasi'] ?? '-' : $row['lokasi'] ?? '-' }}
                            </td>
                            <td>
                                {{ $pihak['nama_pemasok'] ?? ($pihak['nama_penerima'] ?? ($pihak['nama'] ?? '-')) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- Pagination + summary --}}
        @php
            $isPaginator = $paginated instanceof \Illuminate\Pagination\LengthAwarePaginator;
            $total = $isPaginator
                ? $paginated->total()
                : (is_countable($paginated)
                    ? count($paginated)
                    : collect($paginated)->count());
            $first = $isPaginator ? $paginated->firstItem() ?? ($total ? 1 : 0) : ($total ? 1 : 0);
            $last = $isPaginator ? $paginated->lastItem() ?? $total : $total;
        @endphp
        <div class="pagination-wrapper">
            <div class="small text-muted">
                Showing {{ $first }}
                to {{ $last }}
                of {{ $total }} results
            </div>
            <div class="ms-auto">
                @if ($isPaginator)
                    {!! $paginated->appends(request()->query())->links('pagination::bootstrap-5') !!}
                @endif
            </div>
        </div>
    </div>
@endsection
