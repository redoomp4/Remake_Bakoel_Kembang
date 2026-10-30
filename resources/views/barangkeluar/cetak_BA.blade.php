<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Berita Acara Pengeluaran Barang</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18px 28px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ================= HEADER ================= */
        .header {
            text-align: center;
            font-size: 15pt;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        /* ================= PARAGRAPH ================= */
        p {
            margin: 5px 0;
        }

        .section {
            margin-top: 10px;
        }

        /* ================= TABLE ================= */
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 5px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
        }

        /* ================= NO BORDER ================= */
        .no-border {
            border-collapse: collapse;
            margin-top: 2px;
        }

        .no-border td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }

        /* ================= DETAIL ================= */
        .detail-table {
            margin-top: 5px;
        }

        .detail-table th,
        .detail-table td {
            padding: 4px;
            font-size: 8.5pt;
        }

        /* ================= CATATAN ================= */
        .catatan {
            margin-top: 8px;
        }

        /* ================= PENUTUP ================= */
        .penutup {
            margin-top: 8px;
        }

        /* ================= SIGNATURE ================= */
        .signature {
            margin-top: 25px;
        }

        .signature td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }

        .signature-space {
            height: 55px;
        }
    </style>
</head>

<body>
    {{-- ===================================================== --}}
    {{-- HEADER --}}
    {{-- ===================================================== --}}
    <div class="header">
        BERITA ACARA PENGELUARAN BARANG
    </div>

    {{-- ===================================================== --}}
    {{-- NOMOR BERITA ACARA --}}
    {{-- ===================================================== --}}
    <p>
        Nomor :
        BA-KB/{{ $barangKeluar->id }}/{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('m') }}/{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('Y') }}
    </p>

    {{-- ===================================================== --}}
    {{-- PEMBUKA --}}
    {{-- ===================================================== --}}
    <p>
        Pada hari ini
        <strong>
            {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('l') }}
        </strong>,
        tanggal
        <strong>
            {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y') }}
        </strong>,
        bertempat di
        <strong>
            {{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}
        </strong>,
        telah dilakukan pengeluaran barang kepada:
    </p>

    {{-- ===================================================== --}}
    {{-- PIHAK PENERIMA --}}
    {{-- ===================================================== --}}
    <div class="section">
        <p>
            <strong>Pihak Penerima:</strong>
        </p>
        <table class="no-border" style="width: 70%;">
            <tr>
                <td style="width: 30%; text-align: left;">
                    Nama
                </td>
                <td style="width: 5%;">
                    :
                </td>
                <td style="text-align: left;">
                    {{ $barangKeluar->penerima ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    Unit Tujuan
                </td>
                <td>
                    :
                </td>
                <td style="text-align: left;">
                    {{ $barangKeluar->lokasi_tujuan ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================================================== --}}
    {{-- DETAIL BARANG --}}
    {{-- ===================================================== --}}
    <div class="section">
        <p>
            Adapun barang yang diserahkan adalah sebagai berikut:
        </p>
        <table class="detail-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Lokasi Awal</th>
                    <th>Kondisi</th>
                    <th>Jenis Transaksi</th>
                    <th>Lokasi Tujuan</th>
                    <th>Tanggal Keluar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        1
                    </td>
                    <td>
                        {{ $barangKeluar->item->kode_barang ?? '-' }}
                    </td>
                    <td>
                        {{ $barangKeluar->item->nama_barang ?? '-' }}
                    </td>
                    <td>
                        {{ $barangKeluar->jumlah_keluar }}
                    </td>
                    <td>
                        {{ optional($barangKeluar->item->satuan)->nama_satuan ?? '-' }}
                    </td>
                    <td>
                        {{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}
                    </td>
                    <td>
                        {{ $barangKeluar->kondisi->nama_kondisi ?? '-' }}
                    </td>
                    <td>
                        {{ $barangKeluar->jenis_transaksi ?? '-' }}
                    </td>
                    <td>
                        {{ $barangKeluar->lokasi_tujuan ?? '-' }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d-m-Y') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ===================================================== --}}
    {{-- CATATAN --}}
    {{-- ===================================================== --}}
    <div class="catatan">
        <p>
            <strong>Catatan:</strong>
            {{ $barangKeluar->catatan ?? '-' }}
        </p>
    </div>

    {{-- ===================================================== --}}
    {{-- PENUTUP --}}
    {{-- ===================================================== --}}
    <div class="penutup">
        <p>
            Demikian berita acara ini dibuat dengan sebenar-benarnya
            dan telah disetujui oleh pihak-pihak terkait.
        </p>
        <p>
            {{ $barangKeluar->lokasi->nama_lokasi ?? '-' }},
            {{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y') }}
        </p>
    </div>

    {{-- ===================================================== --}}
    {{-- TANDA TANGAN --}}
    {{-- ===================================================== --}}
    <table class="signature">
        <tr>
            <td>
                Petugas Gudang
                <div class="signature-space"></div>
                (
                {{ $barangKeluar->user->username ?? '-' }}
                )
            </td>
            <td>
                Pihak Penerima
                <div class="signature-space"></div>
                (
                {{ $barangKeluar->penerima ?? '-' }}
                )
            </td>
        </tr>
    </table>
</body>

</html>
