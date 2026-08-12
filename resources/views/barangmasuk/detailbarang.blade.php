@extends('layouts.app')
@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div
            style="max-width:900px;margin:auto;background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.05);">
            <div
                style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div>
                    <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;">Inventori · Detail
                    </p>
                    <h4 style="color:#0B4F35;font-size:1.8rem;font-weight:900;margin:0;">Detail Barang Masuk</h4>
                </div><a href="{{ route('barang-masuk.index') }}"
                    style="background:#E4E4D9;color:#475569;padding:.75rem 1rem;border-radius:.75rem;text-decoration:none;font-weight:800;">Kembali</a>
            </div>
            <div style="display:grid;grid-template-columns:240px 1fr;gap:2rem;align-items:start;">
                <div style="background:#FAF9F6;border-radius:1rem;padding:1rem;text-align:center;">
                    @if ($barangMasuk->item && $barangMasuk->item->foto && Storage::disk('public')->exists($barangMasuk->item->foto))
                        <img src="{{ asset('storage/' . $barangMasuk->item->foto) }}" alt="Foto Barang"
                        style="width:100%;border-radius:.75rem;">@else<span style="color:#8FA882;">Tidak tersedia</span>
                    @endif
                </div>
                <div>
                    <h5 style="color:#0B4F35;font-weight:900;font-size:1.35rem;">
                        {{ $barangMasuk->item->nama_barang ?? '-' }}</h5>
                    <table style="width:100%;border-collapse:collapse;">
                        @foreach ([['Kode', $barangMasuk->kode_barang], ['Jumlah', $barangMasuk->jumlah], ['Harga', 'Rp ' . number_format($barangMasuk->harga_satuan, 0, ',', '.')], ['Total', 'Rp ' . number_format($barangMasuk->total_harga, 0, ',', '.')], ['Tanggal', \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y')], ['Kondisi', $barangMasuk->kondisi->nama_kondisi ?? '-'], ['Petugas', $barangMasuk->user->name ?? '-']] as $row)
                            <tr style="border-bottom:1px solid #E4E4D9;">
                                <th style="padding:.7rem;text-align:left;color:#475569;">{{ $row[0] }}</th>
                                <td style="padding:.7rem;">{{ $row[1] }}</td>
                            </tr>
                        @endforeach
                    </table>
                    @php $qrPath = $barangMasuk->qr_code; @endphp<div style="margin-top:1rem;">
                        @if (filled($qrPath) && Storage::disk('public')->exists($qrPath))
                            <img src="{{ Storage::url($qrPath) }}" alt="QR Code"
                            style="width:120px;padding:.5rem;border:1px solid #E4E4D9;border-radius:.75rem;">@else<span
                                style="color:#999;">QR tidak tersedia</span>
                        @endif
                    </div>
                    <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:1.25rem;"><a
                            href="{{ route('barang-masuk.cetak-qr-kecil', $barangMasuk->id) }}" target="_blank"
                            style="background:#0B4F35;color:#fff;padding:.7rem .9rem;border-radius:.7rem;text-decoration:none;font-weight:700;">Cetak
                            QR</a><a href="{{ route('barang-masuk.cetak.pdf', $barangMasuk->id) }}" target="_blank"
                            style="background:#60a5fa;color:#fff;padding:.7rem .9rem;border-radius:.7rem;text-decoration:none;font-weight:700;">Cetak
                            Label</a><a
                            href="https://wa.me/?text={{ urlencode('Detail barang masuk: ' . $barangMasuk->kode_barang) }}"
                            target="_blank"
                            style="background:#16a34a;color:#fff;padding:.7rem .9rem;border-radius:.7rem;text-decoration:none;font-weight:700;">Kirim
                            Struk WA</a></div>
                </div>
            </div>
        </div>
    </div>
@endsection
<style>@media(max-width:640px){div[style*="grid-template-columns:240px"]{grid-template-columns:1fr!important}}</style>
