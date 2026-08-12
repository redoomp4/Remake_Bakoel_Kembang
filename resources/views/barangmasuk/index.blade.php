@extends('layouts.app')
@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div style="max-width:1250px;margin:auto;">
            <div
                style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div>
                    <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;">Inventori · Arus
                        Masuk</p>
                    <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">Daftar Barang Masuk</h4>
                </div><a href="{{ route('barang-masuk.create') }}"
                    style="background:#0B4F35;color:#fff;padding:.8rem 1.25rem;border-radius:.85rem;text-decoration:none;font-weight:800;">+
                    Tambah Barang Masuk</a>
            </div>
            <form method="GET" action="{{ route('barang-masuk.index') }}"
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.25rem;padding:1.25rem;display:flex;gap:1rem;align-items:end;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div style="flex:1;min-width:220px;"><label for="search"
                        style="display:block;color:#475569;font-weight:800;font-size:.85rem;margin-bottom:.5rem;">Cari
                        Barang</label><input type="text" id="search" name="search" placeholder="Cari nama barang"
                        value="{{ request('search') }}"
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
                    href="{{ route('barang-masuk.index') }}"
                    style="background:#E4E4D9;color:#475569;border-radius:.75rem;padding:.8rem 1.25rem;text-decoration:none;font-weight:800;">Reset</a>
            </form>
            <div
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:1100px;">
                    <thead
                        style="background:#f0ebe3;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">
                        <tr>
                            @foreach (['No', 'Tanggal Masuk', 'Kode Barang', 'Nama Barang', 'Jumlah', 'Harga Beli', 'Total Harga', 'Kadaluarsa', 'Pemasok', 'Lokasi', 'Kondisi', 'Detail'] as $heading)
                                <th style="padding:1rem;text-align:left;">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangMasuks as $bm)
                            <tr style="border-bottom:1px solid #E4E4D9;" onmouseover="this.style.background='#fafbf8'"
                                onmouseout="this.style.background='#fff'">
                                <td style="padding:1rem;">{{ $loop->iteration }}</td>
                                <td style="padding:1rem;">
                                    {{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d-m-Y H:i') }}</td>
                                <td style="padding:1rem;font-weight:800;">{{ $bm->kode_barang }}</td>
                                <td style="padding:1rem;">{{ $bm->item->nama_barang ?? '-' }}</td>
                                <td style="padding:1rem;">{{ $bm->jumlah }}</td>
                                <td style="padding:1rem;">Rp {{ number_format($bm->harga_satuan, 0, ',', '.') }}</td>
                                <td style="padding:1rem;">Rp {{ number_format($bm->total_harga, 0, ',', '.') }}</td>
                                <td style="padding:1rem;">
                                    {{ \Carbon\Carbon::parse($bm->tanggal_kadaluarsa)->format('d-m-Y H:i') }}</td>
                                <td style="padding:1rem;">{{ $bm->pemasok->nama_pemasok ?? '-' }}</td>
                                <td style="padding:1rem;">{{ $bm->lokasi->nama_lokasi ?? '-' }}</td>
                                <td style="padding:1rem;"><span
                                        style="background:#ecfdf5;color:#047857;padding:.4rem .7rem;border-radius:999px;font-size:.75rem;font-weight:800;">{{ $bm->kondisi->nama_kondisi ?? '-' }}</span>
                                </td>
                                <td style="padding:1rem;">
                                    @if ($bm->qr_code)
                                        <a href="{{ route('barang-masuk.qr-card', $bm->id) }}"
                                            style="background:#60a5fa;color:#fff;padding:.5rem .7rem;border-radius:.6rem;text-decoration:none;font-weight:700;">Lihat</a><a
                                            href="{{ route('barang-masuk.qrshow.kode', $bm->kode_barang) }}"
                                            target="_blank"
                                        style="background:#10b981;color:#fff;padding:.5rem .7rem;border-radius:.6rem;text-decoration:none;font-weight:700;">QR</a>@else<span
                                            style="color:#999;">-</span>
                                    @endif
                                </td>
                        </tr>@empty<tr>
                                <td colspan="12" style="padding:2rem;text-align:center;color:#999;">Data belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1.5rem;text-align:center;">{{ $barangMasuks->links() }}</div>
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
