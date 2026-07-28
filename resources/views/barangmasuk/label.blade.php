<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Label Barang Masuk (50×30mm)</title>
  <style>
    @page { margin: 0; }
    * { box-sizing: border-box; }
    html, body { margin:0; padding:0; font-family: Arial, sans-serif; color:#111; }

    /* Kanvas full halaman 50×30mm */
    .canvas { width:50mm; height:30mm; }

    /* Tabel 1 sel untuk centering vertikal+horizontal */
    table.full { width:50mm; height:30mm; border-collapse:collapse; table-layout:fixed; }
    td.center { vertical-align: middle; text-align: center; padding:0; }

    /* Kartu label: sedikit lebih kecil dari page supaya aman (no overflow) */
    .label {
      width:47.5mm;   /* < 50mm */
      height:27.5mm;  /* < 30mm */
      padding:1mm;
      margin:0 auto;  /* center horizontal */
      overflow:hidden;
      /*border-radius:0.6mm;  opsional */
    }

    /* Grid isi: kiri info, kanan QR */
    table.grid { width:100%; height:100%; border-collapse:collapse; table-layout:fixed; }
    td.left  { width:31mm; vertical-align:middle; padding:0; }
    td.right { width:15mm; vertical-align:middle; text-align:center; padding:0; }

    /* Info ringkas */
    table.info { width:100%; border-collapse:collapse; table-layout:fixed; font-size:2.05mm; line-height:1.15; }
    table.info td { padding:0.2mm 0; vertical-align:top; }
    td.lbl { width:11mm; font-weight:700; white-space:nowrap; }
    td.sep { width:1mm; text-align:center; }
    td.val { word-break: break-word; }

    /* QR 15mm supaya pas dengan teks */
    .qr-box {
      width:15mm; height:15mm;
      border:0.25mm solid #cfcfcf; border-radius:0.8mm;
      padding:0.4mm; background:#fff;
      margin:0 auto;
    }
    .qr-box img { width:100%; height:100%; object-fit:contain; display:block; }
  </style>
</head>
<body>
  <div class="canvas">
    <table class="full">
      <tr>
        <td class="center">
          <div class="label">
            <table class="grid">
              <tr>
                <td class="left">
                  <table class="info">
                    <tr>
                      <td class="lbl">Kode</td><td class="sep">:</td>
                      <td class="val">{{ $barangMasuk->kode_barang }}</td>
                    </tr>
                    <tr>
                      <td class="lbl">Nama</td><td class="sep">:</td>
                      <td class="val">{{ optional($barangMasuk->item)->nama_barang ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td class="lbl">Kategori</td><td class="sep">:</td>
                      <td class="val">{{ data_get($barangMasuk, 'item.kategori.kategori', '-') }}</td>
                    </tr>
                    <tr>
                      <td class="lbl">Kondisi</td><td class="sep">:</td>
                      <td class="val">{{ optional($barangMasuk->kondisi)->nama_kondisi ?? '-' }}</td>
                    </tr>
                    {{-- Kalau masih muat, boleh aktifkan:
                    <tr><td class="lbl">Qty</td><td class="sep">:</td><td class="val">{{ $barangMasuk->jumlah }}</td></tr>
                    <tr><td class="lbl">Lokasi</td><td class="sep">:</td><td class="val">{{ optional($barangMasuk->lokasi)->nama_lokasi ?? '-' }}</td></tr>
                    --}}
                  </table>
                </td>
                <td class="right">
                  <div class="qr-box">
                    @if($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code))
                      <img src="{{ public_path('storage/'.$barangMasuk->qr_code) }}" alt="QR">
                    @else
                      <span style="font-size:6px;color:#666">No QR</span>
                    @endif
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
