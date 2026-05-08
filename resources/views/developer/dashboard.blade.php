<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Dashboard — AccountEasy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#0d1b2a; min-height:100vh; color:white; }
        .topbar { background:rgba(255,255,255,0.05); border-bottom:1px solid rgba(255,255,255,0.08); padding:14px 28px; display:flex; justify-content:space-between; align-items:center; }
        .topbar-brand { font-size:1rem; font-weight:800; }
        .topbar-brand span { color:#4cde9e; }
        .topbar-right { display:flex; align-items:center; gap:12px; }
        .logout-btn { background:rgba(220,38,38,0.2); border:1px solid rgba(220,38,38,0.3); color:#fca5a5; padding:7px 16px; border-radius:8px; font-family:'Poppins',sans-serif; font-size:.82rem; font-weight:600; cursor:pointer; text-decoration:none; }
        .logout-btn:hover { background:rgba(220,38,38,0.3); color:white; }
        .main { padding:28px; max-width:1100px; margin:0 auto; }
        .stat-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:28px; }
        .stat-box { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:20px; }
        .stat-num { font-size:2rem; font-weight:800; line-height:1; }
        .stat-lbl { font-size:.75rem; color:rgba(255,255,255,0.5); margin-top:6px; }
        .section-card { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:22px; margin-bottom:24px; }
        .section-title { font-size:.9rem; font-weight:700; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .table { color:rgba(255,255,255,0.85); }
        .table th { color:rgba(255,255,255,0.4); font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid rgba(255,255,255,0.08); padding:10px 12px; background:transparent; }
        .table td { border-bottom:1px solid rgba(255,255,255,0.05); padding:12px; font-size:.83rem; vertical-align:middle; }
        .table tr:last-child td { border-bottom:none; }
        .badge-pending { background:#fef3c7;color:#92400e; }
        .badge-approved { background:#d1fae5;color:#065f46; }
        .badge-rejected { background:#fee2e2;color:#991b1b; }
        .status-badge { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
        .btn-approve { background:#d1fae5; color:#065f46; border:none; border-radius:8px; padding:5px 14px; font-size:.78rem; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif; }
        .btn-approve:hover { background:#a7f3d0; }
        .btn-reject-btn { background:#fee2e2; color:#991b1b; border:none; border-radius:8px; padding:5px 14px; font-size:.78rem; font-weight:600; cursor:pointer; font-family:'Poppins',sans-serif; }
        .btn-reject-btn:hover { background:#fecaca; }
        .receipt-thumb { width:48px; height:48px; object-fit:cover; border-radius:8px; cursor:pointer; border:2px solid rgba(255,255,255,0.15); }
        .qr-section { display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap; }
        .qr-preview { width:160px; height:160px; border-radius:12px; object-fit:contain; background:white; padding:8px; }
        .qr-placeholder { width:160px; height:160px; border-radius:12px; background:rgba(255,255,255,0.08); border:2px dashed rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; flex-direction:column; gap:8px; color:rgba(255,255,255,0.4); font-size:.78rem; }
        .form-control-dark { background:rgba(255,255,255,0.08); border:1.5px solid rgba(255,255,255,0.12); border-radius:10px; color:white; font-family:'Poppins',sans-serif; padding:9px 14px; font-size:.88rem; }
        .form-control-dark:focus { background:rgba(255,255,255,0.12); border-color:#4cde9e; color:white; box-shadow:0 0 0 3px rgba(76,222,158,0.1); }
        .form-control-dark::placeholder { color:rgba(255,255,255,0.3); }
        .btn-green-dev { background:linear-gradient(135deg,#1a7a57,#0d4f3c); color:white; border:none; border-radius:10px; padding:10px 20px; font-family:'Poppins',sans-serif; font-weight:600; font-size:.85rem; cursor:pointer; transition:all .2s; }
        .btn-green-dev:hover { background:linear-gradient(135deg,#22a06b,#1a7a57); transform:translateY(-1px); }
        .no-data { padding:32px; text-align:center; color:rgba(255,255,255,0.3); font-size:.85rem; }
        .alert-success-dark { background:rgba(76,222,158,0.15); border:1px solid rgba(76,222,158,0.3); color:#4cde9e; padding:12px 16px; border-radius:10px; font-size:.83rem; margin-bottom:16px; }
        .alert-error-dark { background:rgba(220,38,38,0.15); border:1px solid rgba(220,38,38,0.3); color:#fca5a5; padding:12px 16px; border-radius:10px; font-size:.83rem; margin-bottom:16px; }
    </style>
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">🔧 Developer<span>Portal</span> — AccountEasy</div>
    <div class="topbar-right">
        <span style="font-size:.78rem;color:rgba(255,255,255,0.4)">Logged in as developer</span>
        <a href="{{ route('developer.logout') }}" class="logout-btn"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
    </div>
</div>

<div class="main">

    @if(session('success'))
    <div class="alert-success-dark"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error-dark"><i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}</div>
    @endif

    {{-- Stats --}}
    <div class="stat-row">
        <div class="stat-box">
            <div class="stat-num" style="color:#4cde9e">{{ $pendingPayments->count() }}</div>
            <div class="stat-lbl">Pending Approvals</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:#60a5fa">{{ $totalCompanies }}</div>
            <div class="stat-lbl">Total Companies</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:#a78bfa">{{ $activeCompanies }}</div>
            <div class="stat-lbl">Active Companies</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:#fbbf24">{{ $approvedPayments->count() }}</div>
            <div class="stat-lbl">Total Approved</div>
        </div>
    </div>

    {{-- QR Code Management --}}
    <div class="section-card">
        <div class="section-title"><i class="fas fa-qrcode" style="color:#4cde9e"></i>Bank QR Code (Payment)</div>
        <div class="qr-section">
            <div>
                @if($qrImage)
                    <img src="{{ asset('storage/'.$qrImage) }}" class="qr-preview" alt="QR Code">
                    <div style="font-size:.72rem;color:rgba(255,255,255,0.4);margin-top:8px;text-align:center">Current QR shown on registration page</div>
                @else
                    <div class="qr-placeholder">
                        <i class="fas fa-qrcode fa-2x"></i>
                        <span>No QR uploaded</span>
                    </div>
                @endif
            </div>
            <div style="flex:1;min-width:220px">
                <form method="POST" action="{{ route('developer.update_qr') }}" enctype="multipart/form-data">
                    @csrf
                    <label style="font-size:.8rem;font-weight:600;color:rgba(255,255,255,0.6);display:block;margin-bottom:8px">Upload New QR Code</label>
                    <input type="file" name="qr_image" class="form-control form-control-dark mb-3" accept="image/*" required>
                    <div style="font-size:.72rem;color:rgba(255,255,255,0.3);margin-bottom:12px">Upload your bank QR image. Companies will see this when registering to make payment of RM50.</div>
                    <button type="submit" class="btn-green-dev"><i class="fas fa-upload me-2"></i>Update QR Code</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Pending Payments --}}
    <div class="section-card">
        <div class="section-title"><i class="fas fa-clock" style="color:#fbbf24"></i>Pending Payment Approvals ({{ $pendingPayments->count() }})</div>
        @if($pendingPayments->isEmpty())
            <div class="no-data"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:rgba(76,222,158,0.3)"></i>No pending payments</div>
        @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Company</th><th>Contact</th><th>Email</th><th>Amount</th><th>Date</th><th>Receipt</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($pendingPayments as $p)
                    <tr>
                        <td><strong>{{ $p->company_name }}</strong></td>
                        <td>{{ $p->contact_name }}</td>
                        <td style="color:rgba(255,255,255,0.5)">{{ $p->contact_email }}</td>
                        <td><strong style="color:#4cde9e">RM {{ number_format($p->amount, 2) }}</strong></td>
                        <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($p->receipt_path)
                                <img src="{{ asset('storage/'.$p->receipt_path) }}" class="receipt-thumb" onclick="viewReceipt('{{ asset('storage/'.$p->receipt_path) }}')" title="Click to view">
                            @else
                                <span style="color:rgba(255,255,255,0.3);font-size:.75rem">No receipt</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('developer.approve_payment', $p->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-approve" onclick="return confirm('Approve payment for {{ $p->company_name }}?')">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>
                                </form>
                                <button class="btn-reject-btn" onclick="showRejectModal({{ $p->id }}, '{{ $p->company_name }}')">
                                    <i class="fas fa-times me-1"></i>Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Recent Approved --}}
    <div class="section-card">
        <div class="section-title"><i class="fas fa-check-circle" style="color:#4cde9e"></i>Recently Approved</div>
        @if($approvedPayments->isEmpty())
            <div class="no-data">No approved payments yet</div>
        @else
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Company</th><th>Contact</th><th>Amount</th><th>Approved</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($approvedPayments as $p)
                    <tr>
                        <td><strong>{{ $p->company_name }}</strong></td>
                        <td>{{ $p->contact_name }}</td>
                        <td><strong style="color:#4cde9e">RM {{ number_format($p->amount, 2) }}</strong></td>
                        <td>{{ $p->approved_at?->format('d/m/Y') ?? '-' }}</td>
                        <td><span class="status-badge badge-approved">Approved</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Receipt Viewer Modal --}}
<div id="receiptModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;display:none;align-items:center;justify-content:center">
    <div style="position:relative;max-width:90vw;max-height:90vh">
        <button onclick="closeReceipt()" style="position:absolute;top:-12px;right:-12px;background:#dc2626;border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem;z-index:1">×</button>
        <img id="receiptImg" src="" style="max-width:85vw;max-height:85vh;border-radius:12px;object-fit:contain">
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content" style="background:#1a2535;border:1px solid rgba(255,255,255,0.1);border-radius:16px">
            <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,0.08);padding:18px 22px">
                <h5 class="modal-title" style="color:white;font-size:.95rem;font-weight:700">Reject Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body p-4">
                    <p style="font-size:.83rem;color:rgba(255,255,255,0.6);margin-bottom:16px">Rejecting payment for: <strong id="rejectCompanyName" style="color:white"></strong></p>
                    <label style="font-size:.82rem;font-weight:600;color:rgba(255,255,255,0.7);display:block;margin-bottom:8px">Reason for rejection *</label>
                    <textarea name="reason" class="form-control form-control-dark" rows="3" placeholder="Explain why this payment is rejected..." required></textarea>
                </div>
                <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,0.08);padding:14px 22px;gap:10px">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-reject-btn px-4 py-2">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
function viewReceipt(url) {
    document.getElementById('receiptImg').src = url;
    const m = document.getElementById('receiptModal');
    m.style.display = 'flex';
}
function closeReceipt() {
    document.getElementById('receiptModal').style.display = 'none';
}
function showRejectModal(id, name) {
    document.getElementById('rejectCompanyName').textContent = name;
    document.getElementById('rejectForm').action = `/developer/payments/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
document.getElementById('receiptModal').addEventListener('click', function(e) {
    if (e.target === this) closeReceipt();
});
</script>
</body>
</html>
