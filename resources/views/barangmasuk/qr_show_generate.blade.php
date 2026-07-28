@extends('layouts.public') {{-- atau layouts.app kalau mau sama, tapi pastikan tidak memaksa login --}}

@section('title', 'Detail Barang (QR)')

@section('content')
<style>
  .qr-wrap{max-width:860px;margin:24px auto;padding:20px;border-radius:14px;border:1px solid #e5e7eb;background:#fff;box-shadow:0 10px 20px rgba(0,0,0,.05)}
  .qr-grid{display:grid;grid-template-columns:260px 1fr;gap:24px}
  .qr-photo{border:1px solid #e5e7eb;border-radius:12px;background:#fafafa;display:flex;align-items:center;justify-content:center;overflow:hidden}
  .qr-photo img{width:100%;height:auto;object-fit:contain}
  .qr-info h2{margin:0 0 12px;font-weight:700}
  .qr-table{width:100%;border-collapse:separate;border-spacing:0 8px}
  .qr-table td:first-child{width:140px;font-weight:600;color:#374151}
  .qr-pill{display:inline-block;padding:.25rem .6rem;border:1px solid #e5e7eb;border-radius:999px;background:#f9fafb}
  @media(max-width:768px){.qr-grid{grid-template-columns:1fr}}
</style>

<div class="qr-wrap">
  <div class="qr-grid">
    <div class="qr-photo">
      @php
        $foto = optional($barangMasuk->item)->foto;
      @endphp
      @if($foto && Storage::disk('public')->exists($foto))
        <img src="{{ Storage::url($foto) }}" alt="Foto Barang">
      @else
        <div style="padding:24px;color:#6b7280">Foto tidak tersedia</div>
      @endif
    </div>

    <div class="qr-info">
      <h2>Detail Barang</h2>
      <table class="qr-table">
        <tr><td>Kode Barang</td><td>: <span class="qr-pill">{{ $barangMasuk->kode_barang }}</span></td></tr>
        <tr><td>Nama Barang</td><td>: {{ optional($barangMasuk->item)->nama_barang ?? '-' }}</td></tr>
        <tr><td class="lbl">Kategori</td><td>:{{ data_get($barangMasuk, 'item.kategori.kategori', '-') }}</td></tr>
        <tr><td>Jumlah</td><td>: {{ $barangMasuk->jumlah }}</td></tr>
        <tr><td>Kondisi</td><td>: {{ optional($barangMasuk->kondisi)->nama_kondisi ?? '-' }}</td></tr> 
        {{--<tr><td>Lokasi</td><td>: {{ optional($barangMasuk->lokasi)->nama_lokasi ?? '-' }}</td></tr>
         --}}
      </table>
    </div>
  </div>
</div>
@endsection
