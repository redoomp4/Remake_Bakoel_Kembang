@extends('layouts.app')

@section('content')
<style>
body{background:#f7f8f5;font-family:'Plus Jakarta Sans','Segoe UI',sans-serif}.container{max-width:1280px!important;padding:2rem 1.25rem 3rem}.container>header{background:#fff;border:1px solid #e2e8f0;border-radius:1.25rem;padding:1.25rem 1.5rem}.container>header h2{color:#0B4F35;font-weight:900}.container>.bg-white{border:1px solid #e2e8f0!important;border-radius:1.25rem!important;box-shadow:0 2px 8px rgba(15,23,42,.05)}form{background:#f8fafc;border:1px solid #e2e8f0;padding:1.25rem;border-radius:1rem}input,select{border:1px solid #cbd5e1!important;border-radius:.75rem!important;padding:.7rem!important}button{border-radius:.75rem!important;font-weight:800}table{border-radius:1rem;overflow:hidden}thead{background:#f8fafc}th{color:#64748b;text-transform:uppercase;font-size:.7rem;letter-spacing:.05em}td{color:#334155;border-color:#f1f5f9!important}
</style>
<header class="d-flex justify-content-between align-items-center py-3 px-4 bg-white border-bottom">
    <h2>Selamat Datang, {{ Auth::user()->name ?? 'User' }}</h2>
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard Gudang</a>
        <a href="{{ route('pemasok.index') }}" class="btn btn-primary btn-sm">Pemasok</a>
        <a href="{{ route('kondisi.index') }}" class="btn btn-primary btn-sm">Kondisi Barang</a>
        <a href="{{ route('lokasi.index') }}" class="btn btn-primary btn-sm">Lokasi</a>
        <a href="{{ route('kategori.index') }}" class="btn btn-primary btn-sm">Kategori</a>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-primary btn-sm">Barang Masuk</a>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-primary btn-sm">Barang Keluar</a>
        <a href="{{ route('laporan.index') }}" class="btn btn-primary btn-sm">Laporan</a>
        <a href="{{ route('logout') }}" class="btn btn-danger btn-sm">Logout</a>
    </div>
</header>

<div class="container mt-4 bg-white p-4 rounded shadow-sm">
    <h3>Laporan Stok Barang</h3>
    <p><a href="{{ route('laporan.arus') }}">Lihat Laporan Arus Barang</a></p>

    <form method="GET" action="{{ route('laporan.stok') }}" class="row g-2 mb-3">
        <div class="col-md-2">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-2">
            <select name="kategori" class="form-select">
                <option value="">-- Semua Kategori --</option>
                @foreach ($kategoriOptions as $id => $kategori)
                    <option value="{{ $id }}" {{ request('kategori') == $id ? 'selected' : '' }}>{{ $kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="lokasi" class="form-select">
                <option value="">-- Semua Lokasi --</option>
                @foreach ($lokasiOptions as $id => $lokasi)
                    <option value="{{ $id }}" {{ request('lokasi') == $id ? 'selected' : '' }}>{{ $lokasi }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="nama_barang" class="form-control" placeholder="Nama Barang" value="{{ request('nama_barang') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="kode_barang" class="form-control" placeholder="Kode Barang" value="{{ request('kode_barang') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-success w-100">Filter</button>
        </div>
    </form>

    <div class="mb-3">
        <a href="{{ route('laporan.stok.pdf') }}" class="me-3">Cetak PDF</a>
        <a href="{{ route('laporan.stok.excel') }}">Export Excel</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Total Masuk</th>
                <th>Total Keluar</th>
                <th>Stok Akhir</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori->kategori ?? '-' }}</td>
                    <td>{{ $item->satuan->nama ?? '-' }}</td>
                    <td>{{ $item->total_masuk }}</td>
                    <td>{{ $item->total_keluar }}</td>
                    <td>{{ $item->stok_akhir }}</td>
                    <td>{{ $item->lokasi->nama_lokasi ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
