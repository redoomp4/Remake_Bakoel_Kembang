<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok & Aset Bunga</title>
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

        .filters { margin-top: 16px; font-size: 10.5px; color: #475569; }
        .filters ul { margin: 4px 0 0 16px; padding: 0; }
        .filters li { margin: 0; }

        .footer-info { margin-top: 16px; text-align: right; font-size: 10px; color: #64748b; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Stok &amp; Nilai Aset Bunga ({{ $nama }})</h2>
        <p class="periode">Periode {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}</p>
    </div>

    @php
        $grandStok = 0;
        $grandAset = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 17%">No. Nota / Batch</th>
                <th style="width: 12%">Tgl Masuk</th>
                <th style="width: 24%">Nama Bunga</th>
                <th style="width: 12%">Lokasi</th>
                <th style="width: 10%">Sisa Stok</th>
                <th style="width: 10%">Harga Beli</th>
                <th style="width: 10%">Nilai Aset</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $item)
            @php
                $stok = (float) ($item['sisa_stok'] ?? $item['stok_akhir'] ?? 0);
                $harga = (float) ($item['harga_beli'] ?? $item['harga_satuan'] ?? $item['harga_dasar'] ?? 0);
                $nilaiAset = (float) ($item['nilai_aset'] ?? ($stok * $harga));
                $grandStok += $stok;
                $grandAset += $nilaiAset;

                $tgl = $item['tanggal_masuk'] ?? null;
                $tglStr = $tgl instanceof \Carbon\CarbonInterface ? $tgl->format('d/m/Y') : ($tgl ? date('d/m/Y', strtotime((string)$tgl)) : '-');
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['batch_code'] ?? '-' }}</td>
                <td>{{ $tglStr }}</td>
                <td class="text-left font-bold">
                    {{ $item['nama_barang'] ?? '-' }}
                    @if(!empty($item['kode_barang']) && $item['kode_barang'] !== '-')
                        <br><span style="font-size: 9px; color: #64748b; font-weight: normal;">Kode: {{ $item['kode_barang'] }}</span>
                    @endif
                </td>
                <td>{{ $item['lokasi'] ?? '-' }}</td>
                <td class="font-bold">{{ number_format($stok, 0, ',', '.') }} {{ $item['satuan'] ?? '' }}</td>
                <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($nilaiAset, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center; font-style:italic; padding: 14px; color: #64748b;">
                    Tidak ada data stok untuk periode / kriteria ini.
                </td>
            </tr>
        @endforelse
        </tbody>

        @if(!empty($data) && count($data) > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL KESELURUHAN</td>
                <td>{{ number_format($grandStok, 0, ',', '.') }}</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalAset ?? $grandAset, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Filter aktif --}}
    <div class="filters">
        <strong>Berdasarkan kriteria filter:</strong>
        <ul>
            @foreach(($filters ?? []) as $label => $value)
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
