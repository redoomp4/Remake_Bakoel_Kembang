<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Omzet Penjualan</title>
    <style>
        @page { margin: 20px 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; }

        .header { text-align: center; margin-bottom: 12px; }
        .header h2 { margin: 0 0 4px; font-size: 16px; color: #0b4f35; font-weight: bold; }
        .header p.periode { margin: 0; font-size: 11px; color: #64748b; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: center; vertical-align: middle; }
        th { background: #f1f5f9; font-weight: bold; color: #0f172a; font-size: 10.5px; }

        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .total-row { background: #ecfdf5; font-weight: bold; color: #065f46; }

        .summary-box {
            margin-top: 14px;
            width: 100%;
            border-collapse: collapse;
        }
        .summary-box td {
            border: 1px solid #a7f3d0;
            background: #f0fdf4;
            padding: 8px 12px;
            text-align: center;
        }
        .summary-box .title { font-size: 10px; font-weight: bold; color: #065f46; text-transform: uppercase; }
        .summary-box .val { font-size: 14px; font-weight: bold; color: #047857; margin-top: 2px; }

        .filters { margin-top: 14px; font-size: 10.5px; color: #475569; }
        .filters ul { margin: 4px 0 0 16px; padding: 0; }
        .filters li { margin: 0; }

        .footer-info { margin-top: 14px; text-align: right; font-size: 10px; color: #64748b; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Omzet Penjualan Bunga ({{ $nama }})</h2>
        <p class="periode">Periode {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}</p>
    </div>

    @php
        $grandQty = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 28%">Nama Barang / Bunga</th>
                <th style="width: 14%">Lokasi</th>
                <th style="width: 10%">Kondisi</th>
                <th style="width: 8%">Jumlah</th>
                <th style="width: 10%">Harga Jual</th>
                <th style="width: 10%">Total Omzet</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                @php
                    $qty = (int) ($row['jumlah_keluar'] ?? 0);
                    $grandQty += $qty;
                    $tgl = $row['tanggal'] ?? null;
                    $tglFormatted = '-';
                    if (!empty($tgl)) {
                        try {
                            $tglFormatted = \Carbon\Carbon::parse($tgl)->format('d/m/Y H:i');
                        } catch (\Throwable $e) {
                            $tglFormatted = (string) $tgl;
                        }
                    }
                    $hargaJual = (float) ($row['harga_jual'] ?? 0);
                    $omzetItem = (float) ($row['omzet_item'] ?? ($qty * $hargaJual));
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tglFormatted }}</td>
                    <td class="text-left font-bold">{{ $row['nama_barang'] ?? '-' }}</td>
                    <td>{{ $row['lokasi'] ?? '-' }}</td>
                    <td>{{ $row['kondisi'] ?? '-' }}</td>
                    <td class="font-bold">{{ number_format($qty, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($omzetItem, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; font-style:italic; padding: 14px; color: #64748b;">
                        Tidak ada data penjualan untuk periode / kriteria ini.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if(!empty($data) && count($data) > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL OMZET</td>
                <td>{{ number_format($grandQty, 0, ',', '.') }}</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Ringkasan Kartu Atas untuk Ibu-ibu --}}
    <table class="summary-box">
        <tr>
            <td>
                <div class="title">Total Omzet Penjualan</div>
                <div class="val">Rp {{ number_format($total_omzet, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="title">Total Modal Riil (FIFO)</div>
                <div class="val" style="color: #b45309;">Rp {{ number_format($total_modal ?? 0, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="title">Total Laba Bersih Riil</div>
                <div class="val" style="color: #0369a1;">Rp {{ number_format($total_laba ?? 0, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Filter aktif --}}
    <div class="filters">
        <strong>Berdasarkan kriteria filter:</strong>
        <ul>
            @foreach($filters as $label => $value)
                @if(!is_null($value) && $value !== '')
                    <li>{{ $label }}: {{ $value }}</li>
                @endif
            @endforeach
        </ul>
    </div>

    <p class="footer-info">
        Laporan ini dicetak oleh <strong>{{ $username }}</strong> pada <strong>{{ $tanggalCetak }}</strong>
    </p>

</body>
</html>
