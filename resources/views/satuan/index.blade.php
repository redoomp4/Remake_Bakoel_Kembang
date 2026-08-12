@extends('layouts.app')
@section('content')
    <div class="container-fluid px-3 px-md-4 py-4" style="background:#f8fafc;min-height:100vh">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="text-uppercase small fw-bold text-success">Master Data</div>
                <h1 class="h2 fw-bold text-dark mb-1">Kelola Satuan Barang</h1>
                <p class="text-secondary mb-0">Gunakan satuan yang konsisten untuk setiap item inventaris.</p>
            </div><a href="{{ route('satuan.create') }}" class="btn btn-success btn-lg rounded-3 fw-bold"><i
                    class="fas fa-plus-circle me-2"></i>Tambah Satuan Baru</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success rounded-3 border-0">{{ session('success') }}</div>
            @endif @if (session('error'))
                <div class="alert alert-danger rounded-3 border-0">{{ session('error') }}</div>
                @endif @if ($errors->any())
                    <div class="alert alert-danger rounded-3 border-0">{{ $errors->first() }}</div>
                @endif
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                    <div class="bg-light p-3 border-bottom">
                        <form method="GET" action="{{ route('satuan.index') }}" class="row g-2 align-items-end">
                            <div class="col-md-6"><label for="search" class="form-label small fw-bold text-secondary">Cari
                                    satuan</label><input type="text" id="search" name="search"
                                    value="{{ request('search') }}" class="form-control rounded-3"
                                    placeholder="Ketik nama satuan..."></div>
                            <div class="col-auto"><button class="btn btn-primary rounded-3 fw-bold" type="submit"><i
                                        class="fas fa-search me-1"></i>Filter</button></div>
                            <div class="col-auto"><a href="{{ route('satuan.index') }}"
                                    class="btn btn-outline-secondary rounded-3">Reset</a></div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">No</th>
                                    <th>Satuan</th>
                                    <th>Dibuat</th>
                                    <th>Diperbarui</th>
                                    <th class="px-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($satuans as $index => $satuan)
                                    <tr class="border-bottom">
                                        <td class="px-4">{{ $satuans->firstItem() + $index }}</td>
                                        <td><span
                                                class="badge rounded-pill bg-primary-subtle text-primary-emphasis border border-primary-subtle px-3 py-2">{{ $satuan->nama_satuan }}</span>
                                        </td>
                                        <td class="text-secondary">
                                            {{ optional($satuan->created_at)->format('d-m-Y H:i:s') }}</td>
                                        <td class="text-secondary">
                                            {{ optional($satuan->updated_at)->format('d-m-Y H:i:s') }}</td>
                                        <td class="px-4">
                                            <div class="d-flex justify-content-end gap-2"><a
                                                    href="{{ route('satuan.edit', $satuan->id) }}"
                                                    class="btn btn-sm rounded-3 bg-warning-subtle text-warning-emphasis"><i
                                                        class="fas fa-pen"></i></a>
                                                <form action="{{ route('satuan.destroy', $satuan->id) }}" method="POST"
                                                    class="d-inline">@csrf @method('DELETE')<button
                                                        onclick="return confirm('Yakin ingin menghapus?')"
                                                        class="btn btn-sm rounded-3 bg-danger-subtle text-danger"><i
                                                            class="fas fa-trash"></i></button></form>
                                            </div>
                                        </td>
                                </tr>@empty<tr>
                                        <td colspan="5" class="py-5 text-center"><i
                                                class="fas fa-ruler-combined fa-2x text-secondary mb-3"></i>
                                            <h5 class="fw-bold">Belum ada satuan</h5>
                                            <p class="text-secondary">Tambahkan satuan barang pertama.</p><a
                                                href="{{ route('satuan.create') }}"
                                                class="btn btn-success rounded-3">Tambah Data</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">{{ $satuans->links() }}</div>
                </div>
    </div>
@endsection
