
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 50mm;
            height: 30mm;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            overflow: hidden;
        }

        /*
        |--------------------------------------------------------------------------
        | LABEL 50 x 30 MM
        |--------------------------------------------------------------------------
        */
        .label {
            position: relative;

            width: 50mm;
            height: 30mm;

            overflow: hidden;

            padding: 0;
            margin: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | NAMA / BRAND
        |--------------------------------------------------------------------------
        */
        .brand {
            position: absolute;

            left: 2.5mm;
            top: 2mm;

            width: 28mm;

            font-size: 2.8mm;
            line-height: 1.1;

            font-weight: bold;
            color: #0B4F35;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA ITEM
        |--------------------------------------------------------------------------
        */

        .item-row {
            position: absolute;

            left: 2.5mm;

            width: 28mm;

            font-size: 2.0mm;
            line-height: 1.15;
        }

        .item-label {
            position: absolute;

            left: 0;
            top: 0;

            width: 8mm;

            font-weight: bold;
        }

        .item-separator {
            position: absolute;

            left: 8.5mm;
            top: 0;

            width: 1.5mm;

            text-align: center;
        }

        .item-value {
            position: absolute;

            left: 10.5mm;
            top: 0;

            width: 17.5mm;

            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /*
        |--------------------------------------------------------------------------
        | POSISI MASING-MASING DATA
        |--------------------------------------------------------------------------
        */

        .kode {
            top: 9mm;
        }

        .nama {
            top: 13mm;
        }

        .kategori {
            top: 17mm;
        }

        .satuan {
            top: 21mm;
        }

        /*
        |--------------------------------------------------------------------------
        | QR CODE
        |--------------------------------------------------------------------------
        */

        .qr-box {
            position: absolute;

            right: 2.5mm;
            top: 8mm;

            width: 14mm;
            height: 14mm;

            padding: 0.4mm;

            border: 0.2mm solid #d5d5d5;
            border-radius: 0.7mm;

            background: #fff;
        }

        .qr-box img {
            display: block;

            width: 100%;
            height: 100%;

            object-fit: contain;
        }

        .no-qr {
            width: 100%;
            height: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;

            font-size: 1.7mm;
            color: #777;
        }

        /*
        |--------------------------------------------------------------------------
        | KETERANGAN KECIL
        |--------------------------------------------------------------------------
        */

        .caption {
            position: absolute;

            right: 2.5mm;
            top: 22.5mm;

            width: 14mm;

            text-align: center;

            font-size: 1.5mm;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="label">
        {{-- BRAND / NAMA USER --}}
        <div class="brand">
            {{ $item->user->name ?? 'BAKOEL KEMBANG' }}
        </div>


        {{-- KODE --}}
        <div class="item-row kode">
            <div class="item-label">
                Kode
            </div>

            <div class="item-separator">
                :
            </div>

            <div class="item-value">
                {{ $item->kode_barang ?? '-' }}
            </div>
        </div>


        {{-- NAMA --}}
        <div class="item-row nama">
            <div class="item-label">
                Nama
            </div>

            <div class="item-separator">
                :
            </div>

            <div class="item-value">
                {{ $item->nama_barang ?? '-' }}
            </div>
        </div>


        {{-- KATEGORI --}}
        <div class="item-row kategori">
            <div class="item-label">
                Kategori
            </div>

            <div class="item-separator">
                :
            </div>

            <div class="item-value">
                {{ $item->kategori->kategori ?? '-' }}
            </div>
        </div>


        {{-- SATUAN --}}
        <div class="item-row satuan">
            <div class="item-label">
                Satuan
            </div>

            <div class="item-separator">
                :
            </div>

            <div class="item-value">
                {{ $item->satuan->nama_satuan ?? '-' }}
            </div>
        </div>


        {{-- QR CODE --}}
        <div class="qr-box">

            @if ($item->qr_code && Storage::disk('public')->exists($item->qr_code))
                <img src="{{ public_path('storage/' . $item->qr_code) }}" alt="QR {{ $item->kode_barang }}">
            @else
                <div class="no-qr">
                    QR<br>
                    tidak tersedia
                </div>
            @endif

        </div>


        {{-- CAPTION --}}
        {{-- <div class="caption">
            Identitas Barang
        </div> --}}

    </div>

</body>

</html>
