@extends('layouts.app')

@section('content')
    <style>
        :root {
            --emerald: #0B4F35;
            --emerald-dark: #065f46;
            --border: #e2e8f0;
            --offwhite: #f8fafc;
        }

        /* --------------------------------------------------------------------------
         | PAGE / CONTAINER
         | -------------------------------------------------------------------------- */

        body {
            background: var(--offwhite);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        }

        /*
         * PENTING:
         * Container langsung di dalam main harus mengikuti ruang yang tersisa
         * setelah sidebar. min-width:0 mencegah halaman terdorong ke kanan.
         */
        main > .container {
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

        .page-header {
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

        .page-header .eyebrow {
            margin-bottom: .3rem;
            color: #059669;
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .page-header h1 {
            margin: 0 0 .25rem;
            color: #0f172a;
            font-size: 1.75rem;
            font-weight: 900;
            line-height: 1.25;
            letter-spacing: -.025em;
        }

        .page-header p {
            margin: 0;
            color: #64748b;
            font-size: .9rem;
        }

        /* --------------------------------------------------------------------------
         | PRIMARY BUTTON
         | -------------------------------------------------------------------------- */

        .btn-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .75rem 1rem;
            background: var(--emerald);
            color: #fff;
            border: 0;
            border-radius: .75rem;
            font-size: .85rem;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
            transition: all .2s ease;
            box-sizing: border-box;
        }

        .btn-add:hover {
            background: var(--emerald-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        /* --------------------------------------------------------------------------
         | ALERT
         | -------------------------------------------------------------------------- */

        .alert-box {
            width: 100%;
            box-sizing: border-box;
            border-radius: .9rem;
            border: 1px solid transparent;
            padding: .9rem 1rem;
            margin-bottom: 1rem;
            font-size: .85rem;
            font-weight: 600;
        }

        .alert-success-box {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }

        .alert-danger-box {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #9f1239;
        }

        /* --------------------------------------------------------------------------
         | MAIN CARD
         | -------------------------------------------------------------------------- */

        .data-card {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        /* --------------------------------------------------------------------------
         | FILTER
         | -------------------------------------------------------------------------- */

        .filter-wrapper {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            background: #f8fafc;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .filter-form {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: .75rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex: 1 1 300px;
            min-width: 0;
        }

        .filter-group label {
            color: #64748b;
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .4rem;
        }

        .filter-group input {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            padding: .75rem 1rem;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: .75rem;
            color: #334155;
            font-family: inherit;
            font-size: .9rem;
        }

        .filter-group input:focus {
            outline: 0;
            border-color: var(--emerald);
            box-shadow: 0 0 0 4px rgba(11, 79, 53, .12);
        }

        .filter-actions {
            display: flex;
            flex: 0 0 auto;
            gap: .65rem;
        }

        .btn-filter,
        .btn-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .75rem 1rem;
            border-radius: .75rem;
            font-size: .85rem;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
            box-sizing: border-box;
            transition: all .2s ease;
        }

        .btn-filter {
            background: var(--emerald);
            color: #fff;
            border: 1px solid var(--emerald);
            cursor: pointer;
        }

        .btn-filter:hover {
            background: var(--emerald-dark);
            border-color: var(--emerald-dark);
            transform: translateY(-1px);
        }

        .btn-reset {
            background: #fff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-reset:hover {
            background: #f1f5f9;
            color: #334155;
            transform: translateY(-1px);
        }

        /* --------------------------------------------------------------------------
         | TABLE
         | -------------------------------------------------------------------------- */

        .table-responsive {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive table {
            width: 100%;
            min-width: 1100px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-responsive th {
            padding: .9rem .75rem;
            background: #f8fafc !important;
            color: #64748b;
            border-bottom: 1px solid var(--border);
            font-size: .7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
        }

        .table-responsive th:first-child {
            padding-left: 1.25rem;
        }

        .table-responsive th:last-child {
            padding-right: 1.25rem;
        }

        .table-responsive td {
            padding: .9rem .75rem;
            background: #fff;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            font-size: .85rem;
            font-weight: 500;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table-responsive td:first-child {
            padding-left: 1.25rem;
        }

        .table-responsive td:last-child {
            padding-right: 1.25rem;
        }

        .table-responsive tbody tr:hover td {
            background: #f8fafc;
        }

        /* --------------------------------------------------------------------------
         | SUPPLIER BADGE
         | -------------------------------------------------------------------------- */

        .supplier-badge {
            display: inline-flex;
            align-items: center;
            padding: .45rem .8rem;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 800;
        }

        /* --------------------------------------------------------------------------
         | ACTION BUTTONS
         | -------------------------------------------------------------------------- */

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: .5rem;
        }

        .action-edit,
        .action-delete {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: .65rem;
            font-size: .8rem;
            text-decoration: none;
            transition: all .2s ease;
        }

        .action-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .action-edit:hover {
            background: #fde68a;
            color: #78350f;
            transform: translateY(-1px);
        }

        .action-delete {
            background: #ffe4e6;
            color: #e11d48;
            cursor: pointer;
        }

        .action-delete:hover {
            background: #fecdd3;
            color: #be123c;
            transform: translateY(-1px);
        }

        /* --------------------------------------------------------------------------
         | EMPTY STATE
         | -------------------------------------------------------------------------- */

        .empty-state {
            padding: 4rem 1.5rem !important;
            text-align: center;
        }

        .empty-state i {
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            margin-bottom: .4rem;
            color: #0f172a;
            font-weight: 900;
        }

        .empty-state p {
            margin-bottom: 1rem;
            color: #64748b;
            font-size: .85rem;
        }

        .btn-empty {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .7rem 1rem;
            background: var(--emerald);
            color: #fff;
            border-radius: .75rem;
            font-size: .85rem;
            font-weight: 800;
            text-decoration: none;
            transition: all .2s ease;
        }

        .btn-empty:hover {
            background: var(--emerald-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        /* --------------------------------------------------------------------------
         | PAGINATION
         | -------------------------------------------------------------------------- */

        .pagination-wrapper {
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            padding: 1rem 1.25rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .pagination-wrapper nav {
            min-width: max-content;
        }

        /* --------------------------------------------------------------------------
         | DESKTOP
         | -------------------------------------------------------------------------- */

        @media (min-width: 1024px) {
            main > .container {
                width: 100%;
                max-width: 1280px !important;
                min-width: 0;
                flex: 1 1 0%;
                padding-left: 1.25rem;
                padding-right: 1.25rem;
            }
        }

        /* --------------------------------------------------------------------------
         | TABLET
         | -------------------------------------------------------------------------- */

        @media (max-width: 1023px) {
            main > .container {
                width: 100%;
                max-width: 100% !important;
                min-width: 0;
                padding: 1.5rem 1rem 2.5rem;
            }

            .filter-group {
                flex: 1 1 260px;
            }
        }

        /* --------------------------------------------------------------------------
         | MOBILE
         | -------------------------------------------------------------------------- */

        @media (max-width: 768px) {
            main > .container {
                width: 100%;
                max-width: 100% !important;
                min-width: 0;
                padding: 1rem .75rem 2rem;
            }

            .page-header {
                padding: 1rem;
                border-radius: 1rem;
                align-items: flex-start;
            }

            .page-header h1 {
                font-size: 1.35rem;
            }

            .page-header p {
                font-size: .8rem;
                line-height: 1.5;
            }

            .btn-add {
                width: 100%;
            }

            .data-card {
                border-radius: 1rem;
            }

            .filter-wrapper {
                padding: 1rem;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                width: 100%;
                flex: none;
            }

            .filter-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn-filter,
            .btn-reset {
                width: 100%;
            }

            .table-responsive {
                border-radius: 0;
            }

            .table-responsive table {
                min-width: 1100px;
            }

            .pagination-wrapper {
                padding: 1rem;
            }
        }

        /* --------------------------------------------------------------------------
         | MOBILE KECIL
         | -------------------------------------------------------------------------- */

        @media (max-width: 480px) {
            main > .container {
                padding-left: .6rem;
                padding-right: .6rem;
            }

            .page-header {
                padding: .9rem;
            }

            .page-header h1 {
                font-size: 1.2rem;
            }

            .filter-wrapper {
                padding: .85rem;
            }

            .filter-group input {
                padding: .7rem .85rem;
                font-size: .85rem;
            }

            .table-responsive table {
                min-width: 1100px;
            }
        }
    </style>

    <div class="container">

        {{-- Header --}}
        <div class="page-header">
            <div>
                <div class="eyebrow">Master Data</div>

                <h1>
                    Kelola Pemasok
                </h1>

                <p>
                    Simpan informasi mitra pemasok bunga dan kebutuhan inventaris.
                </p>
            </div>

            <a href="{{ route('pemasok.create') }}" class="btn-add">
                <i class="fas fa-plus-circle me-2"></i>
                Tambah Pemasok Baru
            </a>
        </div>

        @if (session('success'))
            <div class="alert-box alert-success-box">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-box alert-danger-box">
                {{ session('error') }}
            </div>
        @endif

        <div class="data-card">

            {{-- Filter --}}
            <div class="filter-wrapper">
                <form method="GET"
                    action="{{ route('pemasok.index') }}"
                    class="filter-form">

                    <div class="filter-group">
                        <label for="search">
                            Cari pemasok
                        </label>

                        <input type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nama, email, atau PIC...">
                    </div>

                    <div class="filter-actions">
                        <button class="btn-filter" type="submit">
                            <i class="fas fa-search me-1"></i>
                            Filter
                        </button>

                        <a href="{{ route('pemasok.index') }}" class="btn-reset">
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pemasok</th>
                            <th>Email</th>
                            <th>Jenis</th>
                            <th>Alamat</th>
                            <th>No. Telepon</th>
                            <th>PIC</th>
                            <th>Bergabung</th>
                            <th>Diperbarui</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pemasoks as $pemasok)
                            <tr>
                                <td>
                                    {{ ($pemasoks->currentPage() - 1) * $pemasoks->perPage() + $loop->iteration }}
                                </td>

                                <td>
                                    <span class="supplier-badge">
                                        {{ $pemasok->nama_pemasok }}
                                    </span>
                                </td>

                                <td>
                                    {{ $pemasok->email ?? '-' }}
                                </td>

                                <td>
                                    {{ $pemasok->jenis ?? '-' }}
                                </td>

                                <td style="min-width:220px; white-space:normal;">
                                    {{ $pemasok->alamat ?? '-' }}
                                </td>

                                <td>
                                    {{ $pemasok->no_telepon ?? '-' }}
                                </td>

                                <td>
                                    {{ $pemasok->nama_pic ?? '-' }}
                                </td>

                                <td>
                                    {{ $pemasok->bergabung_sejak
                                        ? \Carbon\Carbon::parse($pemasok->bergabung_sejak)->format('d-m-Y')
                                        : '-' }}
                                </td>

                                <td style="color:#64748b;">
                                    {{ optional($pemasok->updated_at)->format('d-m-Y H:i:s') ?? '-' }}
                                </td>

                                <td>
                                    <div class="action-buttons">

                                        <a href="{{ route('pemasok.edit', $pemasok->id) }}"
                                            class="action-edit"
                                            title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <form action="{{ route('pemasok.destroy', $pemasok->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin hapus?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="action-delete"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="empty-state">
                                    <i class="fas fa-truck fa-2x"></i>

                                    <h5>
                                        Belum ada pemasok
                                    </h5>

                                    <p>
                                        Tambahkan mitra pemasok pertama.
                                    </p>

                                    <a href="{{ route('pemasok.create') }}"
                                        class="btn-empty">
                                        Tambah Data
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="pagination-wrapper">
                {{ $pemasoks->links() }}
            </div>

        </div>
    </div>
@endsection