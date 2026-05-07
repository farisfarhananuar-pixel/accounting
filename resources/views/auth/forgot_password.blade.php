<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Account Easy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --green-dark:#0d4f3c; --green-main:#1a7a57; --green-light:#4cde9e; --green-pale:#e8f8f1; }
        body { font-family:'Poppins',sans-serif; min-height:100vh; background: linear-gradient(135deg,#0d4f3c,#1a7a57,#0ea5a0); display:flex; align-items:center; justify-content:center; padding:20px; }
        .card { border:none; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.2); max-width:440px; width:100%; padding:40px; }
        .brand { text-align:center; margin-bottom:30px; }
        .brand .icon { font-size:3rem; margin-bottom:10px; }
        .brand h1 { font-size:1.6rem; font-weight:700; color:var(--green-dark); }
        .brand p { color:#6b7280; font-size:0.9rem; }
        .form-control { border:2px solid #e5e7eb; border-radius:10px; padding:12px 16px; font-family:'Poppins',sans-serif; }
        .form-control:focus { border-color:var(--green-main); box-shadow:0 0 0 3px rgba(26,122,87,0.1); }
        .btn-primary { background:linear-gradient(135deg,var(--green-main),var(--green-dark)); border:none; border-radius:10px; padding:13px; font-family:'Poppins',sans-serif; font-weight:600; }
        .btn-primary:hover { background:linear-gradient(135deg,var(--green-dark),var(--green-main)); transform:translateY(-1px); }
        .alert-success { background:var(--green-pale); border:1px solid #a7f3d0; border-radius:10px; color:var(--green-dark); }
        .alert-danger { background:#fef2f2; border:1px solid #fecaca; border-radius:10px; color:#dc2626; }
        a { color:var(--green-main); text-decoration:none; } a:hover { color:var(--green-dark); }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <div class="icon">🔑</div>
            <h1>Forgot Password?</h1>
            <p>Enter your email and we'll send a reset link</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success p-3 mb-3"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger p-3 mb-3"><i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-600" style="font-size:.85rem;font-weight:600">Email Address</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                    placeholder="your@email.com" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-paper-plane me-2"></i> Send Reset Link
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
        </div>
    </div>
</body>
</html>
