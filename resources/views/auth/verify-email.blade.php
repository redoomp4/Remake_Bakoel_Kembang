@php($title = 'Verifikasi Email')
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>

  {{-- Bootstrap Icons CDN (biar selaras dengan login custom) --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    :root{
      --bg:#f3f4f6; --card:#f9f9f9; --text:#333; --muted:#666; --border:#e5e7eb;
      --btn:#396446; --btn-hover:#2f5138;
      --success-bg:#e7f8ef; --success:#0b7a43;
    }
    *{box-sizing:border-box;}
    body{margin:0;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;background:var(--bg);}
    .auth-wrapper{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
    .auth-card{
      width:100%;max-width:560px;background:var(--card);border-radius:16px;padding:2rem;
      box-shadow:0 12px 32px rgba(0,0,0,.08);border:1px solid var(--border);
    }
    h1{margin:0 0 .5rem;color:var(--text);font-weight:800;letter-spacing:.2px;text-align:center;}
    .subtitle{color:var(--muted);line-height:1.7;margin:10px 0 18px;text-align:center;}
    .email-badge{
      display:inline-block;background:#eef2ff;color:#374151;border:1px solid #dbe4ff;
      padding:.35rem .6rem;border-radius:8px;font-weight:600;
    }
    .alert{border-radius:10px;padding:.75rem .9rem;font-size:.95rem;margin:.9rem 0;border:1px solid transparent;display:flex;gap:.5rem;align-items:flex-start;}
    .alert-success{background:var(--success-bg);color:var(--success);border-color:#bfead2;}
    .btn{
      width:100%;height:46px;border:none;border-radius:10px;color:#fff;font-weight:700;cursor:pointer;
      display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
      background:var(--btn);box-shadow:0 8px 18px rgba(57,100,70,.25);transition:transform .08s ease,box-shadow .15s ease,opacity .15s ease;
    }
    .btn:hover{background:var(--btn-hover);box-shadow:0 10px 22px rgba(57,100,70,.35);}
    .btn:active{transform:translateY(1px);}
    .btn:disabled{opacity:.65;cursor:not-allowed;}
    .btn-icon{font-size:1.1rem;}
    .footer-note{ text-align:center;font-size:.85rem;color:#6b7280;margin-top:8px;}

    .alert-danger{background:#fee2e2;color:#b91c1c;border-color:#fecaca;}

  </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h1>Verifikasi Email Anda</h1>

      <p class="subtitle">
        Terima kasih telah mendaftar! Sebelum mulai, silakan verifikasi email Anda
        dengan mengklik tautan yang baru saja kami kirim.<br><br>
        @auth
          Email terdaftar: <span class="email-badge"><i class="bi bi-envelope-fill"></i> {{ Auth::user()->email }}</span>
        @endauth
        <br><br>
        Jika belum menerima email, klik tombol di bawah untuk mengirim ulang.
        Jangan lupa periksa folder <em>Spam/Junk</em>.
      </p>

      @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success">
          <i class="bi bi-check-circle-fill"></i>
          <div>Tautan verifikasi baru telah dikirim ke email Anda.</div>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <div>{{ session('error') }}</div>
        </div>
      @endif


      <form method="POST" action="{{ route('verification.send') }}" onsubmit="disableBtn(this)">
        @csrf
        <button type="submit" class="btn" id="resendBtn">
          <i class="bi bi-send-fill btn-icon"></i>
          <span id="btnText">Kirim Ulang Email Verifikasi</span>
        </button>
      </form>

      <p class="footer-note">
        Tautan verifikasi hanya berlaku beberapa menit dan dapat digunakan satu kali.
      </p>
    </div>
  </div>

  <script>
    function disableBtn(form){
      const btn=document.getElementById('resendBtn');
      const txt=document.getElementById('btnText');
      btn.disabled=true; txt.textContent='Mengirim…';
    }
  </script>
</body>
</html>
