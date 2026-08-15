<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Berita Acara Penerimaan Barang</title>

    <style>
        @page {
            margin: 25px 30px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }

        .header {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
        }

        .no-border {
            border-collapse: collapse;
        }

        .no-border td {
            border: none;
            padding: 3px 0;
        }

        .section {
            margin-top: 20px;
        }

        .text-left {
            text-align: left;
        }

        .signature {
            margin-top: 60px;
        }

        .signature td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
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
        </strong>, tanggal <strong> {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d F Y') }} </strong>,
        bertempat di<strong> {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}</strong>, telah dilakukan penerimaan barang dari:
    </p>


    {{-- ===================================================== --}}
    {{-- PIHAK PENGIRIM --}}
    {{-- ===================================================== --}}

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


    {{-- ===================================================== --}}
    {{-- PIHAK PENERIMA --}}
    {{-- ===================================================== --}}

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


    {{-- ===================================================== --}}
    {{-- DETAIL BARANG --}}
    {{-- ===================================================== --}}

    <div class="section">

        <p>
            Adapun barang yang diterima adalah sebagai berikut:
        </p>

        <table>

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

                    {{-- Kode berasal dari ITEM --}}
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

    <div class="section">

        <p>
            <strong>Catatan:</strong>
            {{ $barangMasuk->catatan ?? '-' }}
        </p>

    </div>


    {{-- ===================================================== --}}
    {{-- PENUTUP --}}
    {{-- ===================================================== --}}

    <div class="section">

        <p>
            Demikian berita acara ini dibuat dengan sebenar-benarnya
            dan telah disetujui oleh pihak-pihak terkait.
        </p>

        <p style="margin-bottom: 0;">
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

                <br>
                <br>
                <br>
                <br>

                (
                {{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}
                )
            </td>


            <td>
                Pihak Penerima

                <br>
                <br>
                <br>
                <br>

                (
                {{ $barangMasuk->user->name ?? '-' }}
                )
            </td>

        </tr>

    </table>

</body>

</html>
