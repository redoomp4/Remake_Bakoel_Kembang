@extends('layouts.app')

@section('content')
    <style>
        :root {
            --emerald-dark: #0B4F35;
            --sage: #8FA882;
            --slate: #475569;
            --offwhite: #FAF9F6;
            --accent: #E4E4D9;
        }

        body {
            background-color: var(--offwhite);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
        }

        .header-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border-bottom: 1px solid var(--accent);
        }

        .header-section h4 {
            font-size: 1.875rem;
            font-weight: 900;
            color: var(--emerald-dark);
            margin: 0;
        }

        .btn-create {
            background-color: var(--emerald-dark);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(11, 79, 53, 0.1);
        }

        .btn-create:hover {
            background-color: #083a28;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(11, 79, 53, 0.2);
        }

        .container-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.25rem;
        }

        /* FILTER CARD */
        .filter-card {
            background: white;
            border: 1px solid var(--accent);
            border-radius: 1.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--slate);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--accent);
            border-radius: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--emerald-dark);
            ring: 2px solid var(--emerald-dark);
            box-shadow: 0 0 0 3px rgba(11, 79, 53, 0.1);
        }

        .btn-filter {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-filter:hover {
            background-color: #2563eb;
        }

        .btn-reset {
            background-color: var(--accent);
            color: var(--slate);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-reset:hover {
            background-color: #d4d4c1;
        }

        /* TABLE CARD */
        .table-card {
            background: white;
            border: 1px solid var(--accent);
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f0ebe3;
            border-bottom: 2px solid var(--accent);
        }

        thead th {
            padding: 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 900;
            color: var(--slate);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--accent);
            font-size: 0.95rem;
            color: #1a1a1a;
        }

        tbody tr:hover {
            background-color: #fafbf8;
            transition: background-color 0.2s ease;
        }

        .img-thumb {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }

        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge-default {
            background-color: #ddd6c9;
            color: #6b6b63;
        }

        /* ACTION BUTTONS */
        .action-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 0.75rem;
            border: none;
            border-radius: 0.625rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-view {
            background-color: #60a5fa;
            color: white;
        }

        .btn-view:hover {
            background-color: #3b82f6;
            transform: translateY(-1px);
        }

        .btn-edit {
            background-color: #fbbf24;
            color: #1a1a1a;
        }

        .btn-edit:hover {
            background-color: #f59e0b;
            transform: translateY(-1px);
        }

        .btn-qr {
            background-color: #10b981;
            color: white;
        }

        .btn-qr:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }

        /* PAGINATION */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        /* MOBILE */
        @media(max-width: 768px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.5rem 1rem;
            }

            .filter-row {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .btn-filter,
            .btn-reset {
                width: 100%;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
                width: 100%;
            }

            thead {
                display: none;
            }

            tbody tr {
                margin-bottom: 1rem;
                border: 1px solid var(--accent);
                border-radius: 0.75rem;
                padding: 1rem;
                background-color: white;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            }

            tbody td {
                border: none !important;
                text-align: left;
                padding: 0.5rem 0 0.5rem 50%;
                position: relative;
                padding-left: 50%;
            }

            tbody td:before {
                position: absolute;
                left: 0;
                top: 0.5rem;
                width: 45%;
                font-weight: 700;
                color: var(--slate);
                text-transform: uppercase;
                font-size: 0.7rem;
                letter-spacing: 0.025em;
            }

            td:nth-of-type(1):before {
                content: "No";
            }

            td:nth-of-type(2):before {
                content: "Foto";
            }

            td:nth-of-type(3):before {
                content: "Kode";
            }

            td:nth-of-type(4):before {
                content: "Nama";
            }

            td:nth-of-type(5):before {
                content: "Kategori";
            }

            td:nth-of-type(6):before {
                content: "Satuan";
            }

            td:nth-of-type(7):before {
                content: "Stok Min";
            }

            td:nth-of-type(8):before {
                content: "Aksi";
            }
        }
    </style>

    <div class="header-section">
        <h4>Daftar Item</h4>
        <a href="{{ route('item.create') }}" class="btn-create">+ Tambah Item Baru</a>
    </div>

    <div class="container-main">
        {{-- Filter Card --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('item.index') }}" class="filter-row">
                <div class="filter-group">
                    <label for="search">Cari Barang</label>
                    <input type="text" id="search" name="search" placeholder="Nama atau kode barang..."
                        value="{{ request('search') }}">
                </div>

                <div class="filter-group">
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group" style="display: flex; gap: 0.5rem; min-width: auto;">
                    <button type="submit" class="btn-filter">Filter</button>
                    <a href="{{ route('item.index') }}" class="btn-reset">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-responsive">
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($item->foto)
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto {{ $item->nama_barang }}"
                                            class="img-thumb">
                                    @else
                                        <span style="font-size: 0.8rem; color: #999;">Tidak ada</span>
                                    @endif
                                </td>
                                <td><strong>{{ $item->kode_barang }}</strong></td>
                                <td>{{ $item->nama_barang }}</td>
                                <td><span class="badge badge-default">{{ $item->kategori->kategori ?? '-' }}</span></td>
                                <td>{{ $item->satuan->nama_satuan ?? '-' }}</td>
                                <td>{{ $item->stok_minimum }}</td>

                                <td>
                                    <div class="action-group">
                                        <a href="{{ route('item.show', $item->kode_barang) }}" class="btn-action btn-view"
                                            title="Lihat Detail">👁️</a>
                                        <a href="{{ route('item.edit', $item->kode_barang) }}" class="btn-action btn-edit"
                                            title="Edit">✏️</a>
                                        <a href="{{ route('barang-masuk.qrshow.kode', $item->kode_barang) }}"
                                            class="btn-action btn-qr" target="_blank" title="QR Code">🌸</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem; color: #999;">Data belum
                                    tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="pagination">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
