<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
    <title>Berita Acara Penerimaan Barang</title>
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
            background: #DCFCE7;
            color: #15803D;
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
           INTRODUCTION
        ===================================================== */
        .intro {
            background: #FAF9F6;
            border: 1px solid #E4E4D9;
            padding: 12px 14px;
            margin-bottom: 20px;
            color: #475569;
        }
        .intro strong {
            color: #0B4F35;
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
            padding: 8px 10px;
            vertical-align: top;
        }
        
        /* =====================================================
           PARTIES
        ===================================================== */
        .party-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-left: -8px;
            margin-right: -8px;
        }
        .party-box {
            width: 50%;
            background: #FAF9F6;
            border: 1px solid #E4E4D9;
            padding: 12px;
            vertical-align: top;
        }
        .party-title {
            color: #0B4F35;
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .party-info {
            width: 100%;
            border-collapse: collapse;
        }
        .party-info td {
            border: none;
            padding: 3px 0;
            vertical-align: top;
        }
        .party-label {
            width: 30%;
            color: #64748B;
            font-weight: bold;
        }
        .party-value {
            color: #334155;
        }
        
        /* =====================================================
           DETAIL BARANG
        ===================================================== */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #E4E4D9;
        }
        .detail-table th {
            background: #0B4F35;
            color: #FFFFFF;
            font-size: 8.5px;
            font-weight: bold;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
        }
        .detail-table td {
            border-top: 1px solid #E4E4D9;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
            color: #475569;
        }
        .detail-table .text-left {
            text-align: left;
        }
        .detail-table .item-name {
            color: #334155;
            font-weight: bold;
        }
        .condition-badge {
            display: inline-block;
            background: #F0FDF4;
            color: #15803D;
            padding: 3px 7px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* =====================================================
           CATATAN
        ===================================================== */
        .note-box {
            background: #FAF9F6;
            border: 1px solid #E4E4D9;
            padding: 10px 12px;
            color: #475569;
            min-height: 45px;
        }
        
        /* =====================================================
           PENUTUP
        ===================================================== */
        .closing {
            color: #475569;
            margin-top: 5px;
        }
        
        /* =====================================================
           SIGNATURE
        ===================================================== */
        .signature {
            margin-top: 32px;
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
            height: 60px;
        }
        .signature-name {
            font-weight: bold;
            color: #334155;
        }
        .signature-role {
            color: #64748B;
            font-size: 9px;
            margin-top: 2px;
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
                        BERITA ACARA PENERIMAAN BARANG
                    </div>
                    <div class="document-number">
                        No. BA:
                        BA-PB/{{ $barangMasuk->id }}/{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('m') }}/{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('Y') }}
                    </div>
                </td>
                <td class="header-right">
                                        <span class="status">
                        BARANG MASUK
                    </span>
                </td>
            </tr>
        </table>
    </div>
    
    {{-- =====================================================
         PEMBUKA
    ====================================================== --}}
    <div class="intro">
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
        telah dilakukan penerimaan barang dari pihak pengirim
        <strong>
            {{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}
        </strong>.
    </div>
    
    {{-- =====================================================
         PIHAK TERKAIT
    ====================================================== --}}
    <div class="section">
                <div class="section-title">
            Pihak Terkait
        </div>
        <table class="party-table">
                        <tr>
                                {{-- PIHAK PENGIRIM --}}
                <td class="party-box">
                                        <div class="party-title">
                        Pihak Pengirim
                    </div>
                    <table class="party-info">
                                                <tr>
                                                        <td class="party-label">
                                Nama
                            </td>
                            <td class="party-value">
                                {{ $barangMasuk->pemasok->nama_pemasok ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                                                        <td class="party-label">
                                Alamat
                            </td>
                            <td class="party-value">
                                {{ $barangMasuk->pemasok->alamat ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                                                        <td class="party-label">
                                No. HP
                            </td>
                            <td class="party-value">
                                {{ $barangMasuk->pemasok->no_hp ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
                
                {{-- PIHAK PENERIMA --}}
                <td class="party-box">
                                        <div class="party-title">
                        Pihak Penerima
                    </div>
                    <table class="party-info">
                                                <tr>
                                                        <td class="party-label">
                                Nama
                            </td>
                            <td class="party-value">
                                {{ $barangMasuk->user->name ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                                                        <td class="party-label">
                                Jabatan
                            </td>
                            <td class="party-value">
                                {{ $barangMasuk->user->role ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                                                        <td class="party-label">
                                Lokasi
                            </td>
                            <td class="party-value">
                                {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    
    {{-- =====================================================
         DETAIL BARANG
    ====================================================== --}}
    <div class="section">
                <div class="section-title">
            Detail Barang Diterima
        </div>
        <table class="detail-table">
                        <thead>
                                <tr>
                                        <th style="width: 5%;">
                        No
                    </th>
                    <th style="width: 15%;">
                        Kode Barang
                    </th>
                    <th style="width: 24%;">
                        Nama Barang
                    </th>
                    <th style="width: 10%;">
                        Jumlah
                    </th>
                    <th style="width: 10%;">
                        Satuan
                    </th>
                    <th style="width: 13%;">
                        Lokasi
                    </th>
                    <th style="width: 13%;">
                        Kondisi
                    </th>
                    <th style="width: 10%;">
                        Tanggal
                    </th>
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
                    <td class="text-left item-name">
                        {{ $barangMasuk->item->nama_barang ?? '-' }}
                    </td>
                    <td>
                        {{ number_format($barangMasuk->jumlah, 0, ',', '.') }}
                    </td>
                    <td>
                        {{ $barangMasuk->item->satuan->nama_satuan ?? '-' }}
                    </td>
                    <td>
                        {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}
                    </td>
                    <td>
                                                <span class="condition-badge">
                            {{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}
                        </span>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    {{-- =====================================================
         INFORMASI TRANSAKSI
    ====================================================== --}}
    <div class="section">
                <div class="section-title">
            Informasi Penerimaan
        </div>
        <table class="info-table">
                        <tr>
                                <td class="info-label">
                    Tanggal Masuk
                </td>
                <td class="info-value">
                    {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                                <td class="info-label">
                    Lokasi Penyimpanan
                </td>
                <td class="info-value">
                    {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}
                </td>
            </tr>
            <tr>
                                <td class="info-label">
                    Kondisi Barang
                </td>
                <td class="info-value">
                    {{ $barangMasuk->kondisi->nama_kondisi ?? '-' }}
                </td>
            </tr>
            <tr>
                                <td class="info-label">
                    Petugas
                </td>
                <td class="info-value">
                    {{ $barangMasuk->user->name ?? '-' }}
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
                        {{ $barangMasuk->catatan ?? '-' }}
        </div>
    </div>
    
    {{-- =====================================================
         PENUTUP
    ====================================================== --}}
    <div class="section">
                <div class="section-title">
            Pernyataan
        </div>
        <div class="closing">
                        Demikian berita acara penerimaan barang ini dibuat dengan
            sebenar-benarnya sebagai bukti bahwa barang telah diterima
            dan diserahkan kepada pihak penerima dalam kondisi sebagaimana
            tercantum dalam dokumen ini.
        </div>
        <div style="margin-top: 10px;">
                        {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }},
            {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->translatedFormat('d F Y') }}
        </div>
    </div>
    
    {{-- =====================================================
         TANDA TANGAN
    ====================================================== --}}
    <div class="signature">
                <table class="signature-table">
                        <tr>
                                <td class="signature-cell">
                                        Pihak Pengirim
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        {{ $barangMasuk->pemasok->nama_pemasok ?? '__________________________' }}
                    </div>
                    <div class="signature-role">
                        Pemasok
                    </div>
                </td>
                
                <td class="signature-cell">
                                        Pihak Penerima
                    <div class="signature-space"></div>
                    <div class="signature-name">
                        {{ $barangMasuk->user->name ?? '__________________________' }}
                    </div>
                    <div class="signature-role">
                        {{ $barangMasuk->user->role ?? 'Petugas Inventori' }}
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
                    Berita Acara Penerimaan Barang
                </td>
                <td class="footer-right">
                    Dicetak
                    {{ now()->translatedFormat('d F Y H:i') }}
                </td>
            </tr>
        </table>
    </div>
    
</body>
</html>
