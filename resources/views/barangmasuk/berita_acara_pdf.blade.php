<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Berita Acara Penerimaan Barang</title>
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

        /* ================= NO BORDER TABLE ================= */
        .no-border {
            border-collapse: collapse;
            margin-top: 2px;
        }

        .no-border td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }

        /* ================= TEXT ================= */
        .text-left {
            text-align: left;
        }

        /* ================= DETAIL BARANG ================= */
        .detail-table {
            margin-top: 5px;
        }

        .detail-table th,
        .detail-table td {
            padding: 4px;
            font-size: 9pt;
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

        /* ================= CATATAN ================= */
        .catatan {
            margin-top: 8px;
        }

        /* ================= PENUTUP ================= */
        .penutup {
            margin-top: 8px;
        }
    </style>
</head>

<body>
    {{-- ===================================================== --}}
    {{-- HEADER --}}
    {{-- ===================================================== --}}
    <div class="header">
        BERITA ACARA PENERIMAAN BARANG
    </div>

    {{-- ===================================================== --}}
    {{-- NOMOR BERITA ACARA --}}
    {{-- ===================================================== --}}
    <p>
        Nomor :
        BA-PB/{{ $barangMasuk->id }}/{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('m') }}/{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('Y') }}
    </p>

    {{-- ===================================================== --}}
    {{-- PEMBUKA --}}
    {{-- ===================================================== --}}
    <p>
        Pada hari ini
        <strong>
            {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('l') }}
        </strong>,
        tanggal
        <strong>
            {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d F Y') }}
        </strong>,
        bertempat di
        <strong>
            {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}
        </strong>,
        telah dilakukan penerimaan barang dari:
    </p>

    {{-- ===================================================== --}}
    {{-- PIHAK PENGIRIM --}}
    {{-- ===================================================== --}}
    <div class="section">
        <p>
            <strong>Pihak Pengirim:</strong>
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
                    {{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    Alamat
                </td>
                <td>
                    :
                </td>
                <td style="text-align: left;">
                    {{ $barangMasuk->pemasok->alamat ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    No. HP
                </td>
                <td>
                    :
                </td>
                <td style="text-align: left;">
                    {{ $barangMasuk->pemasok->no_hp ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

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
                    {{ $barangMasuk->user->name ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    Jabatan
                </td>
                <td>
                    :
                </td>
                <td style="text-align: left;">
                    {{ $barangMasuk->user->role ?? '-' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ===================================================== --}}
    {{-- DETAIL BARANG --}}
    {{-- ===================================================== --}}
    <div class="section">
        <p>
            Adapun barang yang diterima adalah sebagai berikut:
        </p>
        <table class="detail-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        1
                    </td>
                    <td>
                        {{ $barangMasuk->item->kode_barang ?? '-' }}
                    </td>
                    <td>
                        {{ $barangMasuk->item->nama_barang ?? '-' }}
                    </td>
                    <td>
                        {{ $barangMasuk->jumlah }}
                    </td>
                    <td>
                        {{ $barangMasuk->item->satuan->nama_satuan ?? '-' }}
                    </td>
                    <td>
                        {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}
                    </td>
                    <td>
                        {{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y') }}
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
            {{ $barangMasuk->catatan ?? '-' }}
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
            {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }},
            {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d F Y') }}
        </p>
    </div>

    {{-- ===================================================== --}}
    {{-- TANDA TANGAN --}}
    {{-- ===================================================== --}}
    <table class="signature">
        <tr>
            <td>
                Pihak Pengirim
                <div class="signature-space"></div>
                (
                {{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}
                )
            </td>

            <td>
                Pihak Penerima
                <div class="signature-space"></div>
                (
                {{ $barangMasuk->user->name ?? '-' }}
                )
            </td>
        </tr>
    </table>
</body>

</html>
