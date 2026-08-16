<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Barang Keluar</title>
    <style>
        @page {
            margin: 35px 40px 45px 40px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            color: #334155;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        /* =====================================================
           HEADER
        ===================================================== */
        .header {
            width: 100%;
            border-bottom: 3px solid #0B4F35;
            padding-bottom: 14px;
            margin-bottom: 22px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-left {
            width: 65%;
            vertical-align: middle;
        }
        .header-right {
            width: 35%;
            text-align: right;
            vertical-align: middle;
        }
        .brand {
            color: #0B4F35;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .document-title {
            color: #475569;
            font-size: 12px;
            font-weight: bold;
        }
        .document-number {
            color: #64748B;
            font-size: 9px;
            margin-top: 4px;
        }
        .status {
            display: inline-block;
            background: #E0F2FE;
            color: #0369A1;
            border-radius: 12px;
            padding: 5px 10px;
            font-size: 8.5px;
            font-weight: bold;
        }
        /* =====================================================
           SECTION
        ===================================================== */
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            color: #0B4F35;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #E4E4D9;
        }
        /* =====================================================
           INFORMATION TABLE
        ===================================================== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #E4E4D9;
        }
        .info-table tr {
            border-bottom: 1px solid #E4E4D9;
        }
        .info-table tr:last-child {
            border-bottom: none;
        }
        .info-label {
            width: 28%;
            background: #FAF9F6;
            color: #64748B;
            font-weight: bold;
            padding: 8px 10px;
            vertical-align: top;
        }
        .info-value {
            width: 72%;
            color: #334155;
            font-weight: normal;
            padding: 8px 10px;
            vertical-align: top;
        }
        /* =====================================================
           TRANSACTION SUMMARY
        ===================================================== */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 7px 0;
            margin-left: -7px;
            margin-right: -7px;
        }
        .summary-box {
            width: 33.33%;
            background: #F0FDF4;
            border: 1px solid #D1FAE5;
            padding: 10px;
            vertical-align: top;
        }
        .summary-label {
            color: #059669;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .summary-value {
            color: #0B4F35;
            font-size: 13px;
            font-weight: bold;
            margin-top: 4px;
        }
        /* =====================================================
           TRANSACTION BADGE
        ===================================================== */
        .transaction-badge {
            display: inline-block;
            background: #E0F2FE;
            color: #0369A1;
            padding: 4px 9px;
            border-radius: 10px;
            font-size: 8.5px;
            font-weight: bold;
        }
        /* =====================================================
           NOTES
        ===================================================== */
        .note-box {
            background: #FAF9F6;
            border: 1px solid #E4E4D9;
            padding: 10px 12px;
            color: #475569;
            min-height: 45px;
        }
        /* =====================================================
           FOOTER
        ===================================================== */
        .footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            border-top: 1px solid #E4E4D9;
            padding-top: 7px;
            color: #94A3B8;
            font-size: 8px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left {
            text-align: left;
        }
        .footer-right {
            text-align: right;
        }
        /* =====================================================
           SIGNATURE
        ===================================================== */
        .signature {
            margin-top: 30px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            color: #475569;
        }
        .signature-space {
            height: 55px;
        }
        .signature-name {
            font-weight: bold;
            color: #334155;
        }
    </style>
</head>

<body>
    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <div class="brand">
                        INVENTORI
                    </div>
                    <div class="document-title">
                        DETAIL TRANSAKSI BARANG KELUAR
                    </div>
                    <div class="document-number">
                        No. Transaksi: BK-{{ str_pad($barangKeluar->id, 5, '0', STR_PAD_LEFT) }}
                    </div>
                </td>
                <td class="header-right">
                    <span class="status">
                        BARANG KELUAR
                    </span>
                </td>
            </tr>
        </table>
    </div>
    {{-- =====================================================
         RINGKASAN
    ====================================================== --}}
    <div class="section">
        <div class="section-title">
            Ringkasan Transaksi
        </div>
        <table class="summary-table">
            <tr>
                <td class="summary-box">
                    <div class="summary-label">
                        Jumlah Keluar
                    </div>
                    <div class="summary-value">
                        {{ number_format($barangKeluar->jumlah_keluar, 0, ',', '.') }}
                        {{ optional($barangKeluar->item->satuan)->nama_satuan ?? '' }}
                    </div>
                </td>
                <td class="summary-box">
                    <div class="summary-label">
                        Harga Jual / Unit
                    </div>
                    <div class="summary-value">
                        Rp {{ number_format($barangKeluar->harga_jual, 0, ',', '.') }}
                    </div>
                </td>
                <td class="summary-box">
                    <div class="summary-label">
                        Total Nilai
                    </div>
                    <div class="summary-value">
                        Rp {{ number_format($barangKeluar->total_harga_jual, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    {{-- =====================================================
         INFORMASI BARANG
    ====================================================== --}}
    <div class="section">
        <div class="section-title">
            Informasi Barang
        </div>
        <table class="info-table">
            <tr>
                <td class="info-label">
                    Kode Barang
                </td>
                <td class="info-value">
                    {{ $barangKeluar->item->kode_barang ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Nama Barang
                </td>
                <td class="info-value">
                    {{ $barangKeluar->item->nama_barang ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Satuan
                </td>
                <td class="info-value">
                    {{ optional($barangKeluar->item->satuan)->nama_satuan ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Harga Rata-Rata
                </td>
                <td class="info-value">
                    Rp {{ number_format($hargaRata, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Kondisi
                </td>
                <td class="info-value">
                    {{ $barangKeluar->kondisi->nama_kondisi ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Lokasi Awal
                </td>
                <td class="info-value">
                    {{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Lokasi Tujuan
                </td>
                <td class="info-value">
                    {{ $barangKeluar->lokasi_tujuan ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
    {{-- =====================================================
         INFORMASI TRANSAKSI
    ====================================================== --}}
    <div class="section">
        <div class="section-title">
            Informasi Transaksi
        </div>
        <table class="info-table">
            <tr>
                <td class="info-label">
                    Tanggal Keluar
                </td>
                <td class="info-value">
                    {{ $barangKeluar->tanggal_keluar
                        ? \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y')
                        : '-' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Jenis Transaksi
                </td>
                <td class="info-value">
                    <span class="transaction-badge">
                        {{ $barangKeluar->jenis_transaksi ?? '-' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Penerima
                </td>
                <td class="info-value">
                    {{ $barangKeluar->penerima ?? '-' }}
                </td>
            </tr>
            <tr>
                <td class="info-label">
                    Petugas
                </td>
                <td class="info-value">
                    {{ $barangKeluar->user->username ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
    {{-- =====================================================
         CATATAN
    ====================================================== --}}
    <div class="section">
        <div class="section-title">
            Catatan
        </div>
        <div class="note-box">
            {{ $barangKeluar->catatan ?? '-' }}
        </div>
    </div>
    {{-- =====================================================
         TANDA TANGAN
    ====================================================== --}}
    <div class="signature">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    Mengetahui,
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        __________________________
                    </div>
                    <div>
                        Penanggung Jawab
                    </div>
                </td>
                <td class="signature-cell">
                    Petugas,
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        {{ $barangKeluar->user->username ?? '__________________________' }}
                    </div>
                    <div>
                        Petugas Inventori
                    </div>
                </td>
            </tr>
        </table>
    </div>
    {{-- =====================================================
         FOOTER
    ====================================================== --}}
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    Dokumen Detail Barang Keluar
                </td>
                <td class="footer-right">
                    Dicetak {{ now()->translatedFormat('d F Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
