<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Submitted — AccountEasy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#f0f2f5 0%,#e8f8f1 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
        .card { background:white; border-radius:20px; padding:48px 40px; max-width:500px; width:100%; box-shadow:0 8px 32px rgba(0,0,0,0.1); text-align:center; }
        .icon-wrap { width:80px; height:80px; background:linear-gradient(135deg,#d1fae5,#a7f3d0); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; font-size:2rem; }
        h2 { font-size:1.4rem; font-weight:800; color:#0d4f3c; margin-bottom:12px; }
        p { color:#6b7280; font-size:.88rem; line-height:1.7; }
        .steps { background:#f0fdf9; border-radius:12px; padding:20px; margin:24px 0; text-align:left; }
        .step { display:flex; gap:12px; align-items:flex-start; margin-bottom:12px; }
        .step:last-child { margin-bottom:0; }
        .step-icon { width:28px; height:28px; background:linear-gradient(135deg,#1a7a57,#0d4f3c); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:white; font-size:.75rem; }
        .step p { margin:0; font-size:.82rem; color:#374151; }
        .btn-back { background:linear-gradient(135deg,#1a7a57,#0d4f3c); color:white; border:none; border-radius:12px; padding:12px 28px; font-family:'Poppins',sans-serif; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-block; margin-top:8px; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon-wrap">⏳</div>
    <h2>Registration Submitted!</h2>
    <p>Thank you for registering with AccountEasy. Your registration is currently under review.</p>

    <div class="steps">
        <div class="step">
            <div class="step-icon"><i class="fas fa-check"></i></div>
            <p><strong>Step 1 done:</strong> Registration submitted successfully</p>
        </div>
        <div class="step">
            <div class="step-icon"><i class="fas fa-clock"></i></div>
            <p><strong>Step 2 — In progress:</strong> Developer verifying your payment receipt</p>
        </div>
        <div class="step">
            <div class="step-icon"><i class="fas fa-envelope"></i></div>
            <p><strong>Step 3 — Upcoming:</strong> Once approved, you can login with your admin credentials</p>
        </div>
    </div>

    <p style="font-size:.78rem">Approval usually takes within 1 business day. Please check your email for updates.</p>
    <br>
    <a href="{{ route('login') }}" class="btn-back"><i class="fas fa-arrow-left me-2"></i>Back to Login</a>
</div>
</body>
</html>
