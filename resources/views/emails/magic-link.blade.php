<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Tanpa Password - Bakoel Kembang</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #041E14;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1A1A1A;
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
        }
        .content {
            padding: 36px 32px;
            background-color: #ffffff;
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
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer {
            background-color: #FAF9F6;
            padding: 24px 32px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div style="padding: 40px 16px; background-color: #041E14;">
        <div class="container">
            <div class="header">
                <div style="font-size: 36px; line-height: 1;">🌸</div>
                <div class="header-title">BAKOEL KEMBANG</div>
                <div style="font-size: 11px; color: #8FA882; text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">Login Tanpa Password</div>
            </div>

            <div class="content">
                <div style="font-size: 18px; font-weight: 800; color: #0B4F35; margin-bottom: 12px;">Permintaan Login</div>
                <p style="font-size: 15px; line-height: 1.6; color: #475569;">
                    Klik tombol di bawah ini untuk langsung masuk ke akun Bakoel Kembang Anda tanpa perlu mengetikkan kata sandi:
                </p>

                <div class="btn-wrapper">
                    <a href="{{ $url }}" class="btn" target="_blank">MASUK KE AKUN SAYA</a>
                </div>

                <div style="background-color: #F0F7F4; border-radius: 16px; padding: 16px; font-size: 13px; color: #0B4F35;">
                    🔒 Tautan ini hanya berlaku selama <strong>15 menit</strong> dan hanya dapat digunakan 1 (satu) kali. Abaikan email ini jika Anda tidak meminta login.
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Bakoel Kembang Nursery. Hak cipta dilindungi.
            </div>
        </div>
    </div>
</body>
</html>
