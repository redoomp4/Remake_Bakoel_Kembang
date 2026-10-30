@extends('layouts.app')

@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div
            style="
            max-width:900px;
            margin:auto;
            background:#fff;
            border:1px solid #E4E4D9;
            border-radius:1.5rem;
            padding:1.5rem;
            box-shadow:0 1px 4px rgba(0,0,0,.05);
        ">
            {{-- HEADER --}}
            <div
                style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:1rem;
                flex-wrap:wrap;
                margin-bottom:1.5rem;
            ">
                <div>
                    <p
                        style="
                        color:#8FA882;
                        font-weight:800;
                        font-size:.75rem;
                        text-transform:uppercase;
                        margin:0 0 .25rem;
                    ">
                        Inventori · Detail
                    </p>
                    <h4
                        style="
                        color:#0B4F35;
                        font-size:1.8rem;
                        font-weight:900;
                        margin:0;
                    ">
                        Detail Barang Masuk
                    </h4>
                </div>
                <a href="{{ route('barang-masuk.index') }}"
                    style="
                    background:#E4E4D9;
                    color:#475569;
                    padding:.75rem 1rem;
                    border-radius:.75rem;
                    text-decoration:none;
                    font-weight:800;
                ">
                    Kembali
                </a>
            </div>
            {{-- DETAIL --}}
            <div
                style="
                display:grid;
                grid-template-columns:240px 1fr;
                gap:2rem;
                align-items:start;
            ">
                {{-- FOTO ITEM --}}
                <div
                    style="
                    background:#FAF9F6;
                    border-radius:1rem;
                    padding:1rem;
                    text-align:center;
                ">
                    @if ($barangMasuk->item && $barangMasuk->item->foto && Storage::disk('public')->exists($barangMasuk->item->foto))
                        <img src="{{ Storage::url($barangMasuk->item->foto) }}"
                            alt="Foto {{ $barangMasuk->item->nama_barang }}"
                            style="
                            width:100%;
                            aspect-ratio:1/1;
                            object-fit:cover;
                            border-radius:.75rem;
                        ">
                    @else
                        <div
                            style="
                            min-height:220px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#8FA882;
                            font-weight:700;
                        ">
                            Foto tidak tersedia
                        </div>
                    @endif
                </div>
                {{-- INFORMASI TRANSAKSI --}}
                <div>
                    {{-- NAMA ITEM --}}
                    <h5
                        style="
                        color:#0B4F35;
                        font-weight:900;
                        font-size:1.35rem;
                        margin:0 0 .25rem;
                    ">
                        {{ $barangMasuk->item->nama_barang ?? '-' }}
                    </h5>
                    <p
                        style="
                        color:#8FA882;
                        font-size:.8rem;
                        font-weight:800;
                        margin:0 0 1rem;
                    ">
                        {{ $barangMasuk->item->kode_barang ?? '-' }}
                    </p>

                    {{-- INFORMASI --}}
                    <table
                        style="
                        width:100%;
                        border-collapse:collapse;
                    ">

                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                                width:40%;
                            ">
                                Jumlah
                            </th>

                            <td style="padding:.7rem;">
                                {{ number_format($barangMasuk->jumlah) }}
                                {{ $barangMasuk->item->satuan->nama_satuan ?? 'unit' }}
                            </td>
                        </tr>


                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Harga Satuan
                            </th>

                            <td style="padding:.7rem;">
                                Rp {{ number_format($barangMasuk->harga_satuan, 0, ',', '.') }}
                            </td>
                        </tr>


                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Total Harga
                            </th>

                            <td
                                style="
                                padding:.7rem;
                                font-weight:800;
                                color:#0B4F35;
                            ">
                                Rp {{ number_format($barangMasuk->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>


                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Tanggal Masuk
                            </th>

                            <td style="padding:.7rem;">
                                {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y H:i') }}
                            </td>
                        </tr>


                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Kadaluarsa
                            </th>

                            <td style="padding:.7rem;">

                                @if ($barangMasuk->tanggal_kadaluarsa)
                                    {{ \Carbon\Carbon::parse($barangMasuk->tanggal_kadaluarsa)->format('d-m-Y') }}
                                @else
                                    <span style="color:#999;">
                                        Tidak ada
                                    </span>
                                @endif

                            </td>
                        </tr>


                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Pemasok
                            </th>

                            <td style="padding:.7rem;">
                                {{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}
                            </td>
                        </tr>


                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Lokasi
                            </th>

                            <td style="padding:.7rem;">
                                {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}
                            </td>
                        </tr>


                        <tr style="border-bottom:1px solid #E4E4D9;">
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Kondisi
                            </th>

                            <td style="padding:.7rem;">

                                <span
                                    style="
                                    background:#ecfdf5;
                                    color:#047857;
                                    padding:.4rem .7rem;
                                    border-radius:999px;
                                    font-size:.75rem;
                                    font-weight:800;
                                ">
                                    {{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}
                                </span>

                            </td>
                        </tr>
                        <tr>
                            <th
                                style="
                                padding:.7rem;
                                text-align:left;
                                color:#475569;
                            ">
                                Petugas
                            </th>

                            <td style="padding:.7rem;">
                                {{ $barangMasuk->user->name ?? '-' }}
                            </td>
                        </tr>
                    </table>
                    {{-- CATATAN --}}
                    @if ($barangMasuk->catatan)
                        <div
                            style="
                            margin-top:1.25rem;
                            background:#FAF9F6;
                            border:1px solid #E4E4D9;
                            border-radius:.85rem;
                            padding:1rem;
                        ">

                            <p
                                style="
                                color:#475569;
                                font-size:.75rem;
                                font-weight:900;
                                text-transform:uppercase;
                                margin:0 0 .4rem;
                            ">
                                Catatan
                            </p>

                            <p
                                style="
                                color:#475569;
                                margin:0;
                                line-height:1.6;
                                font-size:.9rem;
                            ">
                                {{ $barangMasuk->catatan }}
                            </p>
                        </div>
                    @endif
                    {{-- ACTION --}}
                    <div class="flex flex-col sm:flex-row gap-2">
                        {{-- BERITA ACARA --}}
                        <a href="{{ route('barang-masuk.cetak-detail', $barangMasuk->id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2
                            px-5 py-3 rounded-xl
                            bg-white border-2 border-blue-500
                            text-sm font-extrabold text-blue-600
                            shadow-md
                            hover:bg-blue-50 hover:shadow-lg
                            transition">
                            <i class="fas fa-file-pdf"></i>
                            Cetak PDF
                        </a>

                        <a href="{{ route('barang-masuk.cetak-berita-acara', $barangMasuk->id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2
                            px-5 py-3 rounded-xl
                            bg-white border-2 border-[#0B4F35]
                            text-sm font-extrabold text-[#0B4F35]
                            shadow-md
                            hover:bg-[#F0F7F3]
                            hover:border-[#083d29]
                            hover:text-[#083d29]
                            hover:shadow-lg
                            transition">
                            <i class="fas fa-file-signature"></i>
                            Cetak Berita Acara
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
