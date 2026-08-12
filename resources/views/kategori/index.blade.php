@extends('layouts.app')
@section('content')
    <div class="container-fluid px-3 px-md-4 py-4" style="background:#f8fafc;min-height:100vh">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="text-uppercase small fw-bold text-success">Master Data</div>
                <h1 class="h2 fw-bold text-dark mb-1">Kelola Kategori Bunga</h1>
                <p class="text-secondary mb-0">Atur kategori agar pencatatan inventaris lebih rapi.</p>
            </div><a href="{{ route('kategori.create') }}" class="btn btn-success btn-lg rounded-3 fw-bold"><i
                    class="fas fa-plus-circle me-2"></i>Tambah Kategori Baru</a>
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
                        <form method="GET" action="{{ route('kategori.index') }}" class="row g-2 align-items-end">
                            <div class="col-md-6"><label for="search" class="form-label small fw-bold text-secondary">Cari
                                    kategori</label><input type="text" id="search" name="search"
                                    value="{{ request('search') }}" class="form-control rounded-3"
                                    placeholder="Ketik nama kategori..."></div>
                            <div class="col-auto"><button class="btn btn-primary rounded-3 fw-bold" type="submit"><i
                                        class="fas fa-search me-1"></i>Filter</button></div>
                            <div class="col-auto"><a href="{{ route('kategori.index') }}"
                                    class="btn btn-outline-secondary rounded-3">Reset</a></div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">No</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Dibuat</th>
                                    <th>Diperbarui</th>
                                    <th class="px-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kategoris as $index => $kategori)
                                    <tr class="border-bottom">
                                        <td class="px-4">{{ $kategoris->firstItem() + $index }}</td>
                                        <td><span
                                                class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2">{{ $kategori->kategori }}</span>
                                        </td>
                                        <td>{{ $kategori->deskripsi ?? '-' }}</td>
                                        <td class="text-secondary">
                                            {{ optional($kategori->created_at)->format('d-m-Y H:i:s') }}</td>
                                        <td class="text-secondary">
                                            {{ optional($kategori->updated_at)->format('d-m-Y H:i:s') }}</td>
                                        <td class="px-4">
                                            <div class="d-flex justify-content-end gap-2"><a
                                                    href="{{ route('kategori.edit', $kategori->id) }}"
                                                    class="btn btn-sm rounded-3 bg-warning-subtle text-warning-emphasis"><i
                                                        class="fas fa-pen"></i></a>
                                                <form action="{{ route('kategori.destroy', $kategori->id) }}"
                                                    method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">@csrf
                                                    @method('DELETE')<button
                                                        class="btn btn-sm rounded-3 bg-danger-subtle text-danger"><i
                                                            class="fas fa-trash"></i></button></form>
                                            </div>
                                        </td>
                                </tr>@empty<tr>
                                        <td colspan="6" class="py-5 text-center"><i
                                                class="fas fa-folder-open fa-2x text-secondary mb-3"></i>
                                            <h5 class="fw-bold">Belum ada kategori</h5>
                                            <p class="text-secondary">Tambahkan kategori pertama untuk mulai mengelola data.
                                            </p><a href="{{ route('kategori.create') }}"
                                                class="btn btn-success rounded-3">Tambah Data</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">{{ $kategoris->links() }}</div>
                </div>
    </div>
@endsection
