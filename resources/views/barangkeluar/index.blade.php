@extends('layouts.app')
@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div style="max-width:1250px;margin:auto;">
            <div
                style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div>
                    <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;">Inventori · Arus
                        Keluar</p>
                    <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">Daftar Barang Keluar</h4>
                </div><a href="{{ route('barang-keluar.create') }}"
                    style="background:#0B4F35;color:#fff;padding:.8rem 1.25rem;border-radius:.85rem;text-decoration:none;font-weight:800;">+
                    Tambah Barang Keluar</a>
            </div>
            @if (session('error'))
                <div style="background:#fef2f2;color:#b91c1c;padding:1rem;border-radius:1rem;margin-bottom:1rem;">
                    {{ session('error') }}</div>
            @elseif(session('success'))
                <div style="background:#ecfdf5;color:#047857;padding:1rem;border-radius:1rem;margin-bottom:1rem;">
                    {{ session('success') }}</div>
            @endif
            <form action="{{ route('barang-keluar.index') }}" method="GET"
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.25rem;padding:1.25rem;display:flex;gap:1rem;align-items:end;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div style="flex:1;min-width:240px;"><label for="search"
                        style="display:block;color:#475569;font-weight:800;font-size:.85rem;margin-bottom:.5rem;">Cari
                        Barang</label><input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Kode, nama, penerima, lokasi"
                        style="width:100%;padding:.8rem;border:1px solid #E4E4D9;border-radius:.75rem;"></div>
                <div style="flex:1;min-width:220px;"><label for="lokasi"
                        style="display:block;color:#475569;font-weight:800;font-size:.85rem;margin-bottom:.5rem;">Lokasi</label><select
                        name="lokasi" id="lokasi"
                        style="width:100%;padding:.8rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div><button type="submit"
                    style="background:#0B4F35;color:#fff;border:0;border-radius:.75rem;padding:.8rem 1.25rem;font-weight:800;">Filter</button><a
                    href="{{ route('barang-keluar.index') }}"
                    style="background:#E4E4D9;color:#475569;border-radius:.75rem;padding:.8rem 1.25rem;text-decoration:none;font-weight:800;">Reset</a>
            </form>
            <div
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;overflow-x:auto;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <table style="width:100%;border-collapse:collapse;min-width:1100px;">
                    <thead
                        style="background:#f0ebe3;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">
                        <tr>
                            @foreach (['No', 'Tanggal', 'Kode', 'Nama Barang', 'Lokasi', 'Kondisi', 'Jumlah', 'Harga Jual', 'Total', 'Catatan', 'Detail'] as $heading)
                                <th style="padding:1rem;text-align:left;">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangKeluars as $index => $keluar)
                            <tr style="border-bottom:1px solid #E4E4D9;">
                                <td style="padding:1rem;">{{ $barangKeluars->firstItem() + $index }}</td>
                                <td style="padding:1rem;">
                                    {{ $keluar->tanggal_keluar ? \Carbon\Carbon::parse($keluar->tanggal_keluar)->format('d-m-Y H:i:s') : '-' }}
                                </td>
                                <td style="padding:1rem;font-weight:800;">{{ $keluar->kode_barang }}</td>
                                <td style="padding:1rem;">{{ $keluar->item->nama_barang ?? '-' }}</td>
                                <td style="padding:1rem;">{{ $keluar->lokasi->nama_lokasi ?? '-' }}</td>
                                <td style="padding:1rem;"><span
                                        style="background:#fef3c7;color:#92400e;padding:.4rem .7rem;border-radius:999px;font-size:.75rem;font-weight:800;">{{ $keluar->kondisi->nama_kondisi ?? '-' }}</span>
                                </td>
                                <td style="padding:1rem;">{{ $keluar->jumlah_keluar }}</td>
                                <td style="padding:1rem;">Rp{{ number_format($keluar->harga_jual, 0, ',', '.') }}</td>
                                <td style="padding:1rem;font-weight:800;">
                                    Rp{{ number_format($keluar->total_harga_jual, 0, ',', '.') }}</td>
                                <td style="padding:1rem;">{{ $keluar->catatan ?? '-' }}</td>
                                <td style="padding:1rem;"><a href="{{ route('barang-keluar.detail', $keluar->id) }}"
                                        style="background:#60a5fa;color:#fff;padding:.5rem .75rem;border-radius:.6rem;text-decoration:none;font-weight:700;">Lihat</a>
                                </td>
                        </tr>@empty<tr>
                                <td colspan="11" style="padding:2rem;text-align:center;color:#999;">Tidak ada data barang
                                    keluar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1.5rem;text-align:center;">{{ $barangKeluars->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
<style>
    input:focus,
    select:focus {
        outline: none;
        border-color: #0B4F35 !important;
        box-shadow: 0 0 0 3px rgba(11, 79, 53, .12)
    }
</style>
