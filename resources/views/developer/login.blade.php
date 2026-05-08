<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Login — AccountEasy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#0d1b2a 0%,#1a2f3f 50%,#0d2818 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .card { background:white; border-radius:20px; padding:40px; max-width:420px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.3); }
        .brand { text-align:center; margin-bottom:28px; }
        .brand-icon { width:64px; height:64px; background:linear-gradient(135deg,#1a2f3f,#0d4f3c); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:1.8rem; }
        .brand h2 { font-size:1.3rem; font-weight:800; color:#0d1b2a; }
        .brand p { font-size:.78rem; color:#9ca3af; margin-top:2px; }
        .dev-badge { background:#f0fdf4; border:1px solid #bbf7d0; color:#065f46; padding:8px 16px; border-radius:8px; font-size:.78rem; font-weight:600; text-align:center; margin-bottom:20px; }
        .form-label { font-size:.82rem; font-weight:600; color:#374151; }
        .form-control { border-radius:10px; border:1.5px solid #e5e7eb; font-family:'Poppins',sans-serif; font-size:.88rem; padding:10px 14px; }
        .form-control:focus { border-color:#1a7a57; box-shadow:0 0 0 3px rgba(26,122,87,0.1); }
        .btn-dev { background:linear-gradient(135deg,#0d1b2a,#1a2f3f); color:white; border:none; border-radius:10px; padding:11px; font-family:'Poppins',sans-serif; font-weight:600; font-size:.9rem; width:100%; cursor:pointer; transition:all .2s; }
        .btn-dev:hover { background:linear-gradient(135deg,#1a2f3f,#0d4f3c); transform:translateY(-1px); }
        .back-link { display:block; text-align:center; margin-top:16px; font-size:.78rem; color:#9ca3af; text-decoration:none; }
        .back-link:hover { color:#1a7a57; }
    </style>
</head>
<body>
<div class="card">
    <div class="brand">
        <div class="brand-icon">🔧</div>
        <h2>Developer Portal</h2>
        <p>AccountEasy System Management</p>
    </div>
    <div class="dev-badge"><i class="fas fa-shield-alt me-2"></i>Restricted Access — Developers Only</div>

    @if($errors->any())
    <div class="alert mb-3" style="background:#fee2e2;color:#991b1b;border:none;border-radius:10px;font-size:.82rem">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('developer.login.post') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Developer username" required autocomplete="off">
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Developer password" required>
        </div>
        <button type="submit" class="btn-dev"><i class="fas fa-sign-in-alt me-2"></i>Enter Developer Portal</button>
    </form>
    <a href="{{ route('login') }}" class="back-link"><i class="fas fa-arrow-left me-1"></i>Back to main login</a>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
