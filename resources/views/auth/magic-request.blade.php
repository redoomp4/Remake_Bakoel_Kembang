@php($title = 'Login Tanpa Password')
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>

  {{-- Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    :root{
      --bg:#f3f4f6; --card:#f9f9f9; --text:#333; --muted:#666;
      --brand1:#6c63ff; --brand2:#8a63ff;
      --accent1:#ff416c; --accent2:#ff4b2b;
      --success-bg:#e7f8ef; --success:#0b7a43;
      --error-bg:#ffebee; --error:#c62828;
      --border:#e5e7eb;
    }
    *{box-sizing:border-box}
    body{
      margin:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:var(--bg);
    }
    .auth-wrapper{
      min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px;
    }
    .auth-card{
      width:100%; max-width:520px; background:var(--card); border-radius:16px;
      padding:2rem; box-shadow:0 12px 32px rgba(0,0,0,.08); border:1px solid var(--border);
    }
    h1{ margin:0 0 .5rem; color:var(--text); font-weight:800; letter-spacing:.2px;}
    .subtitle{color:var(--muted); line-height:1.6; margin-bottom:1rem}
    label{display:block; margin:.75rem 0 .5rem; color:var(--text); font-weight:600;}
    input[type="email"]{
      width:100%; height:44px; padding:.5rem .75rem; border:1px solid #ccc; border-radius:10px;
      font-size:1rem; outline:none; transition:border .15s, box-shadow .15s; background:#fff;
    }
    input[type="email"]:focus{
      border-color:#7DB98F; box-shadow:0 0 0 3px rgba(124,140,255,.15);
    }
    .btn-submit{
      width:100%; height:46px; border:none; border-radius:10px; color:#fff; font-weight:700;
      cursor:pointer; margin-top:1rem; display:inline-flex; align-items:center; justify-content:center;
      background:#396446;
      box-shadow:0 10px 22px rgba(125,185,143,.35);
    }
    .btn-submit:hover{ box-shadow:0 10px 22px rgba(108,99,255,.3) }
    .btn-submit:active{ transform:translateY(1px) }
    .btn-submit:disabled{ opacity:.65; cursor:not-allowed }
    .btn-icon{ font-size:1.1rem; margin-right:.5rem }

    .small-link{ font-size:.92rem; color:#7DB98F; text-decoration:none; font-weight:700}
    .small-link:hover{ text-decoration:underline }

    .alert{
      border-radius:10px; padding:.75rem .9rem; font-size:.95rem; margin:.9rem 0;
      border:1px solid transparent; display:flex; gap:.5rem; align-items:flex-start;
    }
    .alert-success{ background:var(--success-bg); color:var(--success); border-color:#bfead2 }
    .alert-error{ background:var(--error-bg); color:var(--error); border-color:#ffc6cc }

    .footer-note{ text-align:center; font-size:.8rem; color:#6b7280; margin-top:.75rem}
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h1>Login Tanpa Password</h1>
      <p class="subtitle">
        Masukkan email Anda, kami akan mengirim tautan <em>magic link</em> untuk login instan (berlaku 15 menit).
      </p>

      {{-- Status sukses --}}
      @if (session('status'))
        <div class="alert alert-success">
          <i class="bi bi-check-circle-fill"></i>
          <div>{{ session('status') }}</div>
        </div>
      @endif

      {{-- Error validasi --}}
      @if ($errors->any())
        <div class="alert alert-error">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <div>
            <ul style="margin:0 0 0 1rem">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif 

      <form method="POST" action="{{ route('magic.request') }}" onsubmit="disableSubmit(this)">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@contoh.com" required autofocus>
        {{--@error('email')
          <div class="alert alert-error" style="margin-top:.5rem">
            <i class="bi bi-x-circle-fill"></i><div>{{ $message }}</div>
          </div>
        @enderror --}}

        <button type="submit" class="btn-submit" id="submitBtn">
          <i class="bi bi-lightning-charge-fill btn-icon"></i>
          <span id="btnText">Kirim Magic Link</span>
        </button>
      </form>

      <p style="margin-top:14px; color:#555;">
        Sudah punya password?
        <a class="small-link" href="{{ route('login') }}">Login biasa</a>.
      </p>

      <p class="footer-note">
        Tautan hanya berlaku 15 menit dan dapat digunakan satu kali.
      </p>
    </div>
  </div>

  <script>
    function disableSubmit(form){
      const btn = document.getElementById('submitBtn');
      const txt = document.getElementById('btnText');
      btn.disabled = true;
      txt.textContent = 'Mengirim…';
    }
  </script>
</body>
</html>
