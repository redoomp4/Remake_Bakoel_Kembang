<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Arus Barang</title>
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
        .badge-masuk { color: #047857; font-weight: bold; }
        .badge-keluar { color: #b91c1c; font-weight: bold; }
        .filters { margin-top: 14px; font-size: 10.5px; color: #475569; }
        .filters ul { margin: 4px 0 0 16px; padding: 0; }
        .filters li { margin: 0; }
        .footer-info { margin-top: 14px; text-align: right; font-size: 10px; color: #64748b; font-style: italic; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Arus Keluar Masuk Barang ({{ $nama }})</h2>
        <p class="periode">Periode {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 14%">Tanggal</th>
                <th style="width: 8%">Jenis</th>
                <th style="width: 28%">Nama Barang / Bunga</th>
                <th style="width: 12%">Harga Satuan</th>
                <th style="width: 8%">Jumlah</th>
                <th style="width: 12%">Lokasi</th>
                <th style="width: 13%">Pihak / Mitra</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                @php
                    $tgl = $row['tanggal'] ?? null;
                    $tglFormatted = '-';
                    if (!empty($tgl)) {
                        try {
                            $tglFormatted = \Carbon\Carbon::parse($tgl)->format('d/m/Y');
                        } catch (\Throwable $e) {
                            $tglFormatted = (string) $tgl;
                        }
                    }
                    $harga = (float) ($row['harga_satuan'] ?? $row['harga_beli'] ?? $row['harga_jual'] ?? $row['harga_dasar'] ?? 0);
                    $jenis = $row['jenis'] ?? '-';
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tglFormatted }}</td>
                    <td class="{{ $jenis === 'Masuk' ? 'badge-masuk' : 'badge-keluar' }}">{{ $jenis }}</td>
                    <td class="text-left font-bold">
                        {{ $row['nama_barang'] ?? '-' }}
                        @if(!empty($row['kode_barang']) && $row['kode_barang'] !== '-')
                            <br><span style="font-size: 9px; color: #64748b; font-weight: normal;">Kode: {{ $row['kode_barang'] }}</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="font-bold">{{ number_format((int) ($row['jumlah'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ $row['lokasi'] ?? '-' }}</td>
                    <td>{{ $row['pihak'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; font-style:italic; padding: 14px; color: #64748b;">
                        Tidak ada data arus barang untuk periode / kriteria ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
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
