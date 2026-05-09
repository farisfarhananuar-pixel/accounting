@extends('layouts.app')
@section('title','Create Bill')
@section('page_title','Create Bill')
@section('page_subtitle','Record new vendor bill')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries</a>
<a href="{{ route('accountant.account_receivable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> Account Receivable</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-receipt"></i></span> Account Payable</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
@endsection

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('accountant.account_payable') }}" class="btn btn-outline-secondary px-3">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
    <h6 class="mb-0" style="color:var(--green-dark);font-weight:700;align-self:center">
        <i class="fas fa-receipt me-2" style="color:#7c3aed"></i>New Bill — {{ $nextNumber }}
    </h6>
</div>

@if($errors->any())
<div class="alert alert-dismissible fade show mb-4" style="background:#fee2e2;color:#991b1b;border:none;border-radius:12px" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('accountant.account_payable.store') }}" id="billForm">
@csrf
<div class="row g-4">
    {{-- Left: Header --}}
    <div class="col-lg-8">
        <div class="chart-card mb-4">
            <h6 class="mb-3" style="color:var(--green-dark);font-weight:700"><i class="fas fa-info-circle me-2"></i>Bill Details</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Vendor <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <select name="vendor_id" id="vendorSelect" class="form-select" required>
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id')==$v->id?'selected':'' }}>{{ $v->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="document.getElementById('quickAddVendorModal').style.display='flex'"
                            class="btn btn-sm flex-shrink-0"
                            style="background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe;border-radius:8px;white-space:nowrap;font-weight:600"
                            title="Add new vendor">
                            <i class="fas fa-plus"></i> New
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Bill Number</label>
                    <input type="text" class="form-control" value="{{ $nextNumber }}" disabled style="background:#f9fafb">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Vendor Invoice No.</label>
                    <input type="text" name="vendor_invoice_number" class="form-control" placeholder="e.g. VIN-2025-001" value="{{ old('vendor_invoice_number') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Bill Date <span class="text-danger">*</span></label>
                    <input type="date" name="bill_date" class="form-control" value="{{ old('bill_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Line Items --}}
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0" style="color:var(--green-dark);font-weight:700"><i class="fas fa-list me-2"></i>Line Items</h6>
                <button type="button" onclick="addLine()" class="btn btn-sm" style="background:var(--green-main);color:white;border-radius:8px;font-weight:600">
                    <i class="fas fa-plus me-1"></i>Add Line
                </button>
            </div>
            <div class="table-responsive">
                <table class="table" id="linesTable">
                    <thead>
                        <tr>
                            <th style="min-width:200px">Description *</th>
                            <th style="width:90px">Qty *</th>
                            <th style="width:130px">Unit Price (RM) *</th>
                            <th style="width:90px">Tax %</th>
                            <th style="width:120px">Amount (RM)</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        <tr class="line-row">
                            <td><input type="text" name="lines[0][description]" class="form-control form-control-sm" placeholder="Item description" required></td>
                            <td><input type="number" name="lines[0][quantity]" class="form-control form-control-sm qty" placeholder="1" min="0.01" step="0.01" required oninput="recalc(this)"></td>
                            <td><input type="number" name="lines[0][unit_price]" class="form-control form-control-sm price" placeholder="0.00" min="0" step="0.01" required oninput="recalc(this)"></td>
                            <td><input type="number" name="lines[0][tax_rate]" class="form-control form-control-sm tax" placeholder="0" min="0" max="100" step="0.1" value="0" oninput="recalc(this)"></td>
                            <td><input type="text" class="form-control form-control-sm amount-display" value="0.00" readonly style="background:#f9fafb;font-weight:600;color:#7c3aed"></td>
                            <td><button type="button" class="btn btn-sm" style="color:#dc2626;background:#fee2e2;border-radius:6px" onclick="removeLine(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: Summary --}}
    <div class="col-lg-4">
        <div class="chart-card" style="position:sticky;top:80px">
            <h6 class="mb-3" style="color:var(--green-dark);font-weight:700"><i class="fas fa-calculator me-2"></i>Summary</h6>
            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem">
                <span style="color:#6b7280">Subtotal</span>
                <strong id="subtotalDisplay">RM 0.00</strong>
            </div>
            <div class="d-flex justify-content-between mb-2" style="font-size:.85rem">
                <span style="color:#6b7280">Tax</span>
                <strong id="taxDisplay">RM 0.00</strong>
            </div>
            <hr style="border-color:#e5e7eb">
            <div class="d-flex justify-content-between mb-4" style="font-size:1rem">
                <span style="font-weight:700;color:var(--green-dark)">Total</span>
                <strong id="totalDisplay" style="color:#7c3aed;font-size:1.1rem">RM 0.00</strong>
            </div>
            <button type="submit" class="btn btn-green w-100 py-2" style="font-size:.9rem">
                <i class="fas fa-save me-2"></i>Create Bill
            </button>
            <a href="{{ route('accountant.account_payable') }}" class="btn btn-outline-secondary w-100 mt-2 py-2" style="font-size:.9rem">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </div>
</div>
</form>

{{-- Quick Add Vendor Modal --}}
<div id="quickAddVendorModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);margin:16px">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center">
            <h6 style="margin:0;font-weight:700;color:#4c1d95"><i class="fas fa-truck me-2" style="color:#7c3aed"></i>Add New Vendor</h6>
            <button type="button" onclick="document.getElementById('quickAddVendorModal').style.display='none';document.getElementById('qav-form').reset();document.getElementById('qav-error').textContent=''" style="background:none;border:none;font-size:1.2rem;color:#9ca3af;cursor:pointer">&times;</button>
        </div>
        <div style="padding:20px 24px">
            <p style="font-size:.82rem;color:#6b7280;margin-bottom:16px">Vendor will be added and automatically selected.</p>
            <div id="qav-error" style="color:#dc2626;font-size:.82rem;margin-bottom:10px;font-weight:600"></div>
            <form id="qav-form">
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" id="qav-name" class="form-control" placeholder="e.g. Pembekal XYZ Sdn Bhd">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Email</label>
                    <input type="email" id="qav-email" class="form-control" placeholder="vendor@email.com">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Phone</label>
                    <input type="text" id="qav-phone" class="form-control" placeholder="e.g. 03-1234 5678">
                </div>
            </form>
        </div>
        <div style="padding:12px 24px 20px;display:flex;gap:10px;justify-content:flex-end">
            <button type="button" onclick="document.getElementById('quickAddVendorModal').style.display='none';document.getElementById('qav-form').reset();document.getElementById('qav-error').textContent=''" class="btn btn-sm btn-outline-secondary">Cancel</button>
            <button type="button" id="qav-submit" class="btn btn-sm" style="background:#7c3aed;color:#fff;font-weight:600;border-radius:8px">
                <i class="fas fa-plus me-1"></i> Add Vendor
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Quick-add vendor modal ────────────────────────────────────────────
document.getElementById('qav-submit').addEventListener('click', async () => {
    const name  = document.getElementById('qav-name').value.trim();
    const email = document.getElementById('qav-email').value.trim();
    const phone = document.getElementById('qav-phone').value.trim();
    const errEl = document.getElementById('qav-error');
    errEl.textContent = '';

    if (!name) { errEl.textContent = 'Vendor name is required.'; return; }

    document.getElementById('qav-submit').disabled = true;
    document.getElementById('qav-submit').innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    try {
        const res = await fetch('{{ route("accountant.vendors.quick_add") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ name, email, phone }),
        });
        const data = await res.json();
        if (!res.ok) {
            errEl.textContent = data.message || 'Failed to add vendor.';
        } else {
            const sel = document.getElementById('vendorSelect');
            const opt = new Option(`${data.name} (${data.code})`, data.id, true, true);
            sel.appendChild(opt);
            document.getElementById('quickAddVendorModal').style.display = 'none';
            document.getElementById('qav-form').reset();
        }
    } catch(e) {
        errEl.textContent = 'Network error. Please try again.';
    } finally {
        document.getElementById('qav-submit').disabled = false;
        document.getElementById('qav-submit').innerHTML = '<i class="fas fa-plus me-1"></i> Add Vendor';
    }
});

// ── Line items ────────────────────────────────────────────────────────
let lineIndex = 1;

function recalc(el) {
    const row = el.closest('tr');
    const qty = parseFloat(row.querySelector('.qty').value) || 0;
    const price = parseFloat(row.querySelector('.price').value) || 0;
    const tax = parseFloat(row.querySelector('.tax').value) || 0;
    const lineAmt = qty * price;
    const taxAmt = lineAmt * (tax / 100);
    row.querySelector('.amount-display').value = (lineAmt + taxAmt).toFixed(2);
    updateSummary();
}

function updateSummary() {
    let subtotal = 0, taxTotal = 0;
    document.querySelectorAll('.line-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const tax = parseFloat(row.querySelector('.tax').value) || 0;
        const lineAmt = qty * price;
        subtotal += lineAmt;
        taxTotal += lineAmt * (tax / 100);
    });
    document.getElementById('subtotalDisplay').textContent = 'RM ' + subtotal.toFixed(2);
    document.getElementById('taxDisplay').textContent = 'RM ' + taxTotal.toFixed(2);
    document.getElementById('totalDisplay').textContent = 'RM ' + (subtotal + taxTotal).toFixed(2);
}

function addLine() {
    const tbody = document.getElementById('linesBody');
    const row = document.createElement('tr');
    row.className = 'line-row';
    row.innerHTML = `
        <td><input type="text" name="lines[${lineIndex}][description]" class="form-control form-control-sm" placeholder="Item description" required></td>
        <td><input type="number" name="lines[${lineIndex}][quantity]" class="form-control form-control-sm qty" placeholder="1" min="0.01" step="0.01" required oninput="recalc(this)"></td>
        <td><input type="number" name="lines[${lineIndex}][unit_price]" class="form-control form-control-sm price" placeholder="0.00" min="0" step="0.01" required oninput="recalc(this)"></td>
        <td><input type="number" name="lines[${lineIndex}][tax_rate]" class="form-control form-control-sm tax" placeholder="0" min="0" max="100" step="0.1" value="0" oninput="recalc(this)"></td>
        <td><input type="text" class="form-control form-control-sm amount-display" value="0.00" readonly style="background:#f9fafb;font-weight:600;color:#7c3aed"></td>
        <td><button type="button" class="btn btn-sm" style="color:#dc2626;background:#fee2e2;border-radius:6px" onclick="removeLine(this)"><i class="fas fa-trash"></i></button></td>`;
    tbody.appendChild(row);
    lineIndex++;
}

function removeLine(btn) {
    const rows = document.querySelectorAll('.line-row');
    if (rows.length <= 1) { alert('At least one line item is required.'); return; }
    btn.closest('tr').remove();
    updateSummary();
}
</script>
@endpush
