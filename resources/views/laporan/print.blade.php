<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        /* Setup halaman cetak untuk DomPDF */
        @page { margin: 20px 24px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #000; }

        h2 { text-align: center; margin: 0 0 4px; }
        p.periode, p.footer-info { text-align: center; margin: 0; font-size: 11px; }

        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
        th { background: #f3f3f3; font-weight: 700; }

        /* Header tabel repeat di setiap halaman */
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }

        /* Daftar filter */
        .filters { margin: 10px 0 0 0; font-size: 11px; }
        .filters ul { margin: 4px 0 0 18px; padding: 0; }
        .filters li { margin: 0; }

        .footer-info { margin-top: 14px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>

    <h2>Laporan Stok Barang ({{ $nama }})</h2>
    <p class="periode">Periode {{ $tanggalAwal }} s/d {{ $tanggalAkhir }}</p>

    @php
        // Siapkan total
        $grandMasuk = 0;
        $grandKeluar = 0;
        $grandStok = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 12%">Kode</th>
                <th style="width: 22%">Nama</th>
                <th style="width: 10%">Harga Dasar</th>
                <th style="width: 10%">Total Masuk</th>
                <th style="width: 10%">Total Keluar</th>
                <th style="width: 10%">Stok Akhir</th>
                <th style="width: 13%">Lokasi</th>
                <th style="width: 13%">Username</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $item)
            @php
                // pastikan key ada; controller kita sudah kirim array shape yang konsisten
                $harga  = (float)($item['harga_dasar'] ?? 0);
                $masuk  = (float)($item['total_masuk'] ?? 0);
                $keluar = (float)($item['total_keluar'] ?? 0);
                $stok   = (float)($item['stok_akhir'] ?? ($masuk - $keluar));
                $grandMasuk  += $masuk;
                $grandKeluar += $keluar;
                $grandStok   += $stok;
            @endphp
            <tr>
                <td>{{ $item['kode_barang'] ?? '-' }}</td>
                <td style="text-align:left">{{ $item['nama_barang'] ?? '-' }}</td>
                <td>Rp {{ number_format($harga, 0, ',', '.') }}</td>
                <td>{{ number_format($masuk) }}</td>
                <td>{{ number_format($keluar) }}</td>
                <td>{{ number_format($stok) }}</td>
                <td>{{ $item['lokasi'] ?? '-' }}</td>
                <td>{{ $item['username'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center; font-style:italic;">Tidak ada data untuk periode/kriteria ini.</td>
            </tr>
        @endforelse
        </tbody>

        @if(!empty($data) && count($data) > 0)
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right">GRAND TOTAL</th>
                <th>{{ number_format($grandMasuk) }}</th>
                <th>{{ number_format($grandKeluar) }}</th>
                <th>{{ number_format($grandStok) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Filter aktif --}}
    <div class="filters">
        <strong>Berdasarkan kriteria:</strong>
        <ul>
            @foreach(($filters ?? []) as $label => $value)
                @if(!is_null($value) && $value !== '')
                    <li>{{ $label }}: {{ $value }}</li>
                @endif
            @endforeach
        </ul>
    </div>

    <p class="footer-info" style="text-align:right">
        <i>Laporan ini dicetak oleh <strong>{{ $username }}</strong> pada <strong>{{ $tanggalCetak }}</strong></i>
    </p>

</body>
</html>
