<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | Account Easy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--green-dark:#0d4f3c;--green-main:#1a7a57;--green-light:#4cde9e;}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;min-height:100vh;background:linear-gradient(135deg,#0d4f3c,#1a7a57);display:flex;align-items:center;justify-content:center;padding:20px}
        .box{background:white;border-radius:24px;padding:50px 40px;max-width:440px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.25)}
        .icon{font-size:4rem;margin-bottom:16px}
        h1{font-size:4rem;font-weight:800;color:#dc2626;line-height:1}
        h2{font-size:1.4rem;font-weight:700;color:var(--green-dark);margin:12px 0 8px}
        p{color:#6b7280;font-size:.9rem;margin-bottom:28px}
        .btn{display:inline-block;padding:13px 28px;background:linear-gradient(135deg,var(--green-main),var(--green-dark));color:white;border-radius:10px;text-decoration:none;font-weight:600;font-size:.9rem}
        .btn:hover{opacity:.9}
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">🚫</div>
        <h1>403</h1>
        <h2>Access Denied</h2>
        <p>You don't have permission to access this page. Please contact your administrator if you believe this is an error.</p>
        <a href="{{ url()->previous() != url()->current() ? url()->previous() : '/' }}" class="btn"><i class="fas fa-arrow-left" style="margin-right:8px"></i>Go Back</a>
    </div>
</body>
</html>
