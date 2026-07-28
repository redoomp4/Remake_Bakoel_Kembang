@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <h2 class="mb-4">Dashboard Gudang</h2>

    <!-- Transaksi Hari Ini -->
    <h4 class="mt-5">Transaksi Hari Ini</h4>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background-color: #79b687; color: #003322;">
                <div class="card-body">
                    <h5 class="card-title">Barang Masuk</h5>
                    <p class="card-text fs-4 fw-bold">{{ $masukHariIni }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background-color: #79b687; color: #003322;">
                <div class="card-body">
                    <h5 class="card-title">Barang Keluar</h5>
                    <p class="card-text fs-4 fw-bold">{{ $keluarHariIni }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Styling Tabel -->
    <style>
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        th {
            padding: 12px;
            background-color: #f3f4f6;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
        }
        td {
            padding: 12px;
            background-color: white;
            text-align: left;
        }
        tr {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }
    </style>

    <!-- Barang Hampir Habis -->
    <h4 class="mt-5">Barang Hampir Habis</h4>
    <table class="table mb-4">
    <tbody>
        @forelse($stokMinimum as $item)
        <tr>
        <td style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
            {{ $item->nama_barang }}
        </td>
        <td class="text-end" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
            <span class="badge rounded-pill" style="background-color: #e31809;">
            Stok: {{ $item->stok_akhir }}
            </span>
        </td>
        </tr>
        @empty
        <tr><td colspan="2">Tidak ada</td></tr>
        @endforelse
    </tbody>
    </table>

    <!-- Barang Kadaluarsa -->
    <h4>Barang Kadaluarsa Dalam 30 Hari</h4>
    <table class="table mb-4">
        <tbody>
            @forelse($kadaluarsa as $item)
            <tr>
                <td style="border-radius: 8px;">
                    {{ $item->nama_barang }} akan kadaluarsa pada tanggal {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->translatedFormat('d F Y') }} 
                    <!-- Kalau tanggalnya mau pakai format biasa 2025-07-30 ganti ke $item->tanggal_kadaluarsa -->
                </td>
            </tr>
            @empty
            <tr><td>Tidak ada</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 5 Barang Stok Terendah -->
    <h4>5 Barang dengan Stok Terendah</h4>
    <table class="table mb-4">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
             @forelse($stokTerendah as $b)
        <tr>
            <td>{{ $b->nama_barang }}</td>
            <td>{{ $b->stok_akhir }}</td>
        </tr>
        @empty
        <tr><td colspan="2">Belum ada data stok.</td></tr>
        @endforelse
        </tbody>
    </table>

    <!-- 5 Barang Idle Stock -->
    <h4>5 Barang Idle Stock (Diatas 30 Hari)</h4>
    <table class="table mb-4">
        <thead>
            <tr>
            <th>Nama</th>
            <th>Lokasi</th>
            <th>Hari Idle</th>
            <th>Terakhir Bergerak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($idleStock as $b)
            <tr>
                <td>{{ $b->nama_barang }}</td>
                <td>{{ $b->nama_lokasi }}</td>
                <td>{{ $b->hari_idle }}</td>
                <td>{{ \Carbon\Carbon::parse($b->last_move)->format('Y-m-d H:i:s') }}</td>
            </tr>
            @empty
            <tr><td colspan="4">Tidak ada barang yang idle &gt; 30 hari.</td></tr>
            @endforelse
        </tbody>
        </table>

    <!-- Top Barang Masuk -->
    <h4>5 Barang Paling Banyak Masuk</h4>
    <table class="table mb-4">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Frekuensi</th>
                <th>Total Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topMasuk as $b)
            <tr>
                <td>{{ $b->nama_barang }}</td>
                <td>{{ $b->frekuensi }}</td>
                <td>{{ $b->total }}</td>
            </tr>
            @empty
            <tr><td colspan="2">Belum ada data stok.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Top Barang Keluar -->
    <h4>5 Barang Paling Banyak Keluar</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Frekuensi</th>
                <th>Total Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topKeluar as $b)
            <tr>
                <td>{{ $b->nama_barang }}</td>
                <td>{{ $b->frekuensi }}</td>
                <td>{{ $b->total }}</td>
            </tr>
            @empty
            <tr><td colspan="2">Belum ada data stok.</td></tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection
