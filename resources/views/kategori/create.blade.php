@extends('layouts.app')

@section('content')
<style>
 body { background:#FAF9F6; font-family:'Plus Jakarta Sans','Segoe UI',sans-serif; } .container { max-width:760px; padding:2rem 1.25rem 3rem; } .container form { background:#fff; border:1px solid #E4E4D9; border-radius:1.5rem; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,.05); } .container h4 { color:#0B4F35; font-weight:900; font-size:2rem; } .form-control { border-color:#E4E4D9; border-radius:.75rem; padding:.75rem 1rem; } .btn-primary { background:#0B4F35; border:0; border-radius:.75rem; padding:.75rem 1.25rem; font-weight:700; } .btn-secondary { border:0; border-radius:.75rem; padding:.75rem 1.25rem; }
</style>
<div class="container">
    <h4 class="mb-4">Tambah Kategori</h4>

    {{-- Flash error dari session --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Error Validasi
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

    <form method="POST" action="{{ route('kategori.store') }}">
        @csrf

        <div class="mb-3">
            <label for="kategori" class="form-label">
                Nama Kategori <span class="text-danger">*</span>
            </label>
            <input type="text" name="kategori"
                   class="form-control @error('kategori') is-invalid @enderror"
                   value="{{ old('kategori') }}"
                   placeholder="Masukkan nama kategori..." required>
            @error('kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi"
                      class="form-control @error('deskripsi') is-invalid @enderror"
                      rows="3"
                      placeholder="Deskripsi kategori (opsional)">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan
            </button>
            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
