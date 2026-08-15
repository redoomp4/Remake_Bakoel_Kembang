@extends('layouts.app')

@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div style="max-width:900px;margin:auto;">
            <div
                style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                <div>
                    <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;">
                        Master Data</p>
                    <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">Detail Barang</h4>
                </div><a href="{{ route('item.index') }}"
                    style="background:#E4E4D9;color:#475569;padding:.75rem 1rem;border-radius:.75rem;text-decoration:none;font-weight:700;">Kembali</a>
            </div>
            <div
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;padding:1.5rem;display:grid;grid-template-columns:minmax(180px,280px) 1fr;gap:2rem;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <div
                    style="display:flex;align-items:center;justify-content:center;background:#FAF9F6;border-radius:1rem;min-height:220px;">
                    @if ($item->foto)
                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}"
                        style="max-width:100%;max-height:280px;object-fit:cover;border-radius:1rem;">@else<span
                            style="color:#8FA882;font-style:italic;">Tidak ada foto</span>
                    @endif
                </div>
                <div><span
                        style="background:#ecfdf5;color:#047857;padding:.4rem .75rem;border-radius:999px;font-size:.75rem;font-weight:800;">ITEM
                        AKTIF</span>
                    <h5 style="color:#0B4F35;font-size:1.7rem;font-weight:900;margin:1rem 0;">{{ $item->nama_barang }}</h5>
                    <dl
                        style="display:grid;grid-template-columns:1fr 1.4fr;gap:.8rem;border-top:1px solid #E4E4D9;padding-top:1rem;">
                        <dt style="font-weight:800;color:#475569;">Kode Barang</dt>
                        <dd style="margin:0;">{{ $item->kode_barang }}</dd>
                        <dt style="font-weight:800;color:#475569;">Harga Dasar</dt>
                        <dd style="margin:0;">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</dd>
                        <dt style="font-weight:800;color:#475569;">Kategori</dt>
                        <dd style="margin:0;">{{ $item->kategori->kategori }}</dd>
                        <dt style="font-weight:800;color:#475569;">Satuan</dt>
                        <dd style="margin:0;">{{ $item->satuan->nama_satuan ?? '-' }}</dd>
                        <dt style="font-weight:800;color:#475569;">Catatan</dt>
                        <dd style="margin:0;">{{ $item->deskripsi ?? '-' }}</dd>
                    </dl>
                    <p style="color:#8FA882;font-size:.8rem;margin-top:1.5rem;">Ditambahkan pada {{ $item->created_at }}</p>
                    {{-- <a href="https://wa.me/?text={{ urlencode('Detail item: ' . $item->nama_barang . ' - ' . $item->kode_barang) }}"
                        target="_blank"
                        style="display:inline-block;background:#16a34a;color:#fff;padding:.8rem 1rem;border-radius:.75rem;text-decoration:none;font-weight:800;margin-top:.75rem;">Kirim
                        Struk WA</a> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
<style>@media(max-width:640px){div[style*="grid-template-columns:minmax"]{grid-template-columns:1fr!important}dl{grid-template-columns:1fr!important}dd{margin-bottom:.5rem!important}}
</style>
