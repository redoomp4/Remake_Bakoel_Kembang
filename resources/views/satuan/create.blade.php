@extends('layouts.app')


@section('content')
<style>body{background:#FAF9F6;font-family:'Plus Jakarta Sans','Segoe UI',sans-serif}.container{max-width:760px;padding:2rem 1.25rem 3rem}.container h4{color:#0B4F35;font-weight:900;font-size:2rem}.container form{background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;padding:2rem;box-shadow:0 1px 3px rgba(0,0,0,.05)}.form-control{border-color:#E4E4D9;border-radius:.75rem;padding:.75rem 1rem}.btn-primary{background:#0B4F35;border:0;border-radius:.75rem;padding:.75rem 1.25rem;font-weight:700}</style>
<div class="container">
    <h4>Tambah Satuan</h4>


    {{-- Flash error --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    {{-- Validasi error
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}


    <form method="POST" action="{{ route('satuan.store') }}">
        @csrf
        <div class="mb-3">
            <label for="nama_satuan" class="form-label">Nama Satuan <span class="text-danger">*</span></label>
            <input type="text" name="nama_satuan" class="form-control @error('nama_satuan') is-invalid @enderror"
                   value="{{ old('nama_satuan') }}" required>
            @error('nama_satuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('satuan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
