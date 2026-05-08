<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Company — AccountEasy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:linear-gradient(135deg,#f0f2f5 0%,#e8f8f1 100%); min-height:100vh; padding:32px 16px; }
        .container-wrap { max-width:860px; margin:0 auto; }
        .header { text-align:center; margin-bottom:32px; }
        .header .brand-icon { width:56px; height:56px; background:linear-gradient(135deg,#1a7a57,#0d4f3c); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin:0 auto 12px; }
        .header h1 { font-size:1.5rem; font-weight:800; color:#0d4f3c; }
        .header p { color:#6b7280; font-size:.85rem; margin-top:4px; }
        .card { background:white; border-radius:18px; padding:28px; box-shadow:0 4px 20px rgba(0,0,0,0.08); margin-bottom:20px; }
        .card-title { font-size:.9rem; font-weight:700; color:#0d4f3c; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
        .form-label { font-size:.8rem; font-weight:600; color:#374151; }
        .form-control { border-radius:10px; border:1.5px solid #e5e7eb; font-family:'Poppins',sans-serif; font-size:.88rem; padding:10px 14px; }
        .form-control:focus { border-color:#1a7a57; box-shadow:0 0 0 3px rgba(26,122,87,0.1); }
        .qr-section { background:#f0fdf9; border:2px solid #bbf7d0; border-radius:14px; padding:24px; text-align:center; }
        .qr-img { width:180px; height:180px; object-fit:contain; border-radius:12px; margin:0 auto 16px; display:block; background:white; padding:8px; border:1px solid #e5e7eb; }
        .qr-placeholder { width:180px; height:180px; background:#f3f4f6; border:2px dashed #e5e7eb; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:8px; color:#9ca3af; margin:0 auto 16px; }
        .amount-badge { background:#d1fae5; color:#065f46; padding:8px 20px; border-radius:30px; font-size:1.1rem; font-weight:800; display:inline-block; margin-bottom:12px; }
        .qr-steps { text-align:left; margin-top:16px; }
        .qr-steps p { font-size:.78rem; color:#374151; margin-bottom:6px; display:flex; align-items:flex-start; gap:8px; }
        .qr-steps .step-num { width:20px; height:20px; background:#1a7a57; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; color:white; flex-shrink:0; margin-top:1px; }
        .btn-submit { background:linear-gradient(135deg,#1a7a57,#0d4f3c); color:white; border:none; border-radius:12px; padding:13px; font-family:'Poppins',sans-serif; font-weight:700; font-size:.95rem; width:100%; cursor:pointer; transition:all .2s; }
        .btn-submit:hover { background:linear-gradient(135deg,#22a06b,#1a7a57); transform:translateY(-1px); }
        .login-link { text-align:center; margin-top:16px; font-size:.8rem; color:#6b7280; }
        .login-link a { color:#1a7a57; font-weight:600; text-decoration:none; }
        .required { color:#dc2626; }
        .file-upload-area { border:2px dashed #e5e7eb; border-radius:10px; padding:20px; text-align:center; cursor:pointer; transition:all .2s; position:relative; }
        .file-upload-area:hover { border-color:#1a7a57; background:#f0fdf9; }
        .file-upload-area input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; }
        .file-upload-area.has-file { border-color:#1a7a57; background:#f0fdf9; }
        .preview-img { max-width:100%; max-height:150px; border-radius:8px; margin-top:12px; display:none; }
    </style>
</head>
<body>
<div class="container-wrap">
    <div class="header">
        <div class="brand-icon">💼</div>
        <h1>Register Your Company</h1>
        <p>Set up AccountEasy for your organisation — one-time registration fee of RM50</p>
    </div>

    @if($errors->any())
    <div class="alert mb-4" style="background:#fee2e2;color:#991b1b;border:none;border-radius:12px;font-size:.83rem">
        <i class="fas fa-exclamation-circle me-2"></i>
        @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                {{-- Company Info --}}
                <div class="card mb-4">
                    <div class="card-title"><i class="fas fa-building" style="color:#1a7a57"></i>Company Information</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Company Name <span class="required">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="e.g. ABC Sdn Bhd" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registration No. (SSM)</label>
                            <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number') }}" placeholder="e.g. 202301012345">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Phone</label>
                            <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone') }}" placeholder="e.g. 03-12345678">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Company Email</label>
                            <input type="email" name="company_email" class="form-control" value="{{ old('company_email') }}" placeholder="company@example.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Company Address</label>
                            <textarea name="company_address" class="form-control" rows="2" placeholder="Full company address">{{ old('company_address') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Admin Account --}}
                <div class="card">
                    <div class="card-title"><i class="fas fa-user-shield" style="color:#0369a1"></i>Admin Account</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name <span class="required">*</span></label>
                            <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}" placeholder="Your full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="required">*</span></label>
                            <input type="text" name="admin_username" class="form-control" value="{{ old('admin_username') }}" placeholder="e.g. admin_abc" required autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}" placeholder="admin@company.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <input type="password" name="admin_password" class="form-control" placeholder="Min 8 characters" required autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password <span class="required">*</span></label>
                            <input type="password" name="admin_password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                {{-- Payment QR --}}
                <div class="card mb-4">
                    <div class="card-title"><i class="fas fa-qrcode" style="color:#1a7a57"></i>Payment — RM50 One-Time Fee</div>
                    <div class="qr-section">
                        <div class="amount-badge">RM 50.00</div>
                        @if($qrImage)
                            <img src="{{ $qrImage }}" class="qr-img" alt="Bank QR Code">
                            <p style="font-size:.82rem;font-weight:600;color:#065f46">Scan QR to pay RM50</p>
                        @else
                            <div class="qr-placeholder">
                                <i class="fas fa-qrcode fa-3x"></i>
                                <span style="font-size:.75rem">QR code will be available soon</span>
                            </div>
                        @endif
                        <div class="qr-steps">
                            <p><span class="step-num">1</span>Scan the QR code above using your banking app</p>
                            <p><span class="step-num">2</span>Pay exactly <strong>RM50.00</strong> as one-time registration fee</p>
                            <p><span class="step-num">3</span>Take a screenshot of the payment confirmation</p>
                            <p><span class="step-num">4</span>Upload the receipt below</p>
                        </div>
                    </div>
                </div>

                {{-- Receipt Upload --}}
                <div class="card">
                    <div class="card-title"><i class="fas fa-receipt" style="color:#d97706"></i>Upload Payment Receipt <span class="required">*</span></div>
                    <div class="file-upload-area" id="uploadArea" onclick="document.getElementById('receiptInput').click()">
                        <input type="file" name="payment_receipt" id="receiptInput" accept="image/*" onchange="previewReceipt(this)" style="display:none">
                        <div id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt fa-2x" style="color:#9ca3af;margin-bottom:8px;display:block"></i>
                            <p style="font-size:.82rem;color:#374151;font-weight:600;margin-bottom:4px">Click to upload receipt</p>
                            <p style="font-size:.72rem;color:#9ca3af">JPG, PNG or GIF. Max 5MB</p>
                        </div>
                        <img id="receiptPreview" class="preview-img" alt="Receipt preview">
                    </div>
                    <p style="font-size:.72rem;color:#9ca3af;margin-top:8px">
                        <i class="fas fa-info-circle me-1"></i>After approval, you'll be able to log in. Approval typically within 1 business day.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane me-2"></i>Submit Registration
            </button>
        </div>
    </form>

    <div class="login-link">
        Already registered and approved? <a href="{{ route('login') }}">Login here</a>
    </div>
</div>

<script>
function previewReceipt(input) {
    const area = document.getElementById('uploadArea');
    const preview = document.getElementById('receiptPreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.innerHTML = '<p style="font-size:.8rem;font-weight:600;color:#065f46"><i class="fas fa-check-circle me-1"></i>' + input.files[0].name + '</p>';
            area.classList.add('has-file');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
