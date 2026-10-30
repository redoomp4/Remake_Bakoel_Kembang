<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Akun - Bakoel Kembang</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #041E14;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1A1A1A;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #FAF9F6;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        .header {
            background: linear-gradient(135deg, #0B4F35 0%, #073A27 100%);
            padding: 36px 32px;
            text-align: center;
            color: #ffffff;
        }
        .header-title {
            font-size: 26px;
            font-weight: 900;
            margin: 12px 0 4px 0;
            letter-spacing: -0.5px;
        }
        .header-subtitle {
            font-size: 11px;
            color: #8FA882;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
        }
        .content {
            padding: 36px 32px;
            background-color: #ffffff;
        }
        .greeting {
            font-size: 20px;
            font-weight: 800;
            color: #0B4F35;
            margin-bottom: 12px;
        }
        .text {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 24px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #0B4F35 0%, #073A27 100%);
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 800;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(11, 79, 53, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-box {
            background-color: #F0F7F4;
            border: 1px solid #D4E8DF;
            border-radius: 16px;
            padding: 16px 20px;
            font-size: 13px;
            color: #0B4F35;
            line-height: 1.5;
            margin-top: 24px;
        }
        .footer {
            background-color: #FAF9F6;
            padding: 24px 32px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
            border-top: 1px solid #E4E4D9;
        }
        .fallback-link {
            font-size: 12px;
            word-break: break-all;
            color: #0B4F35;
        }
    </style>
</head>
<body>
    <div style="padding: 40px 16px; background-color: #041E14;">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div style="font-size: 36px; line-height: 1;">🌸</div>
                <div class="header-title">BAKOEL KEMBANG</div>
                <div class="header-subtitle">Verifikasi Alamat Email</div>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="greeting">Halo {{ $user->name }},</div>
                
                <p class="text">
                    Selamat datang di <strong>Bakoel Kembang</strong>! Terima kasih telah melakukan pendaftaran akun. Untuk memastikan keamanan akun dan mengaktifkan akses penuh ke sistem inventaris & laporan kas kebun, silakan konfirmasi alamat email Anda.
                </p>

                <div class="btn-wrapper">
                    <a href="{{ $url }}" class="btn" target="_blank">VERIFIKASI ALAMAT EMAIL SAYA</a>
                </div>

                <div class="info-box">
                    💡 <strong>Tips Bebas Kendala:</strong> Tautan verifikasi ini berlaku selama 60 menit dan hanya dapat digunakan 1 (satu) kali.
                </div>

                <div style="margin-top: 32px; pt: 24px; border-top: 1px solid #E2E8F0; font-size: 12px; color: #64748B;">
                    Jika tombol di atas tidak dapat diklik, salin dan tempel URL berikut ke browser Anda:<br>
                    <a href="{{ $url }}" class="fallback-link">{{ $url }}</a>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                &copy; {{ date('Y') }} Bakoel Kembang Nursery. Hak cipta dilindungi.<br>
                Pesan ini dikirim secara otomatis. Mohon tidak membalas email ini secara langsung.
            </div>
        </div>
    </div>
</body>
</html>
