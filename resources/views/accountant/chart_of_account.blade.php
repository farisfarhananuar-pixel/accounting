@extends('layouts.app')
@section('title','Chart of Accounts')
@section('page_title','Chart of Accounts')
@section('page_subtitle','Manage your account structure')

@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries</a>
<a href="{{ route('accountant.account_receivable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> Account Receivable</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-receipt"></i></span> Account Payable</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Add Account Form --}}
    <div class="col-12 col-md-4">
        <div class="chart-card">
            <h6 style="color:var(--green-dark);font-weight:700;margin-bottom:20px"><i class="fas fa-plus-circle me-2" style="color:var(--green-main)"></i>Add New Account</h6>
            @if($errors->any())
            <div class="alert" style="background:#fef2f2;color:#dc2626;border-radius:8px;padding:10px 14px;font-size:.82rem;margin-bottom:16px">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}
            </div>
            @endif
            <form method="POST" action="{{ route('accountant.chart_of_account.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Account Code <span class="text-danger">*</span></label>
                    <input type="text" name="account_code" class="form-control" placeholder="e.g. 1010" value="{{ old('account_code') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Account Name <span class="text-danger">*</span></label>
                    <input type="text" name="account_name" class="form-control" placeholder="e.g. Cash on Hand" value="{{ old('account_name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Account Type <span class="text-danger">*</span></label>
                    <select name="account_type" class="form-select" required id="typeSelect" onchange="updateCategories()">
                        <option value="">-- Select Type --</option>
                        <option value="asset">Asset</option>
                        <option value="liability">Liability</option>
                        <option value="equity">Equity</option>
                        <option value="revenue">Revenue</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Category <span class="text-danger">*</span></label>
                    <select name="account_category" class="form-select" required id="categorySelect">
                        <option value="">-- Select Type First --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Optional description" value="{{ old('description') }}">
                </div>
                <button type="submit" class="btn btn-green w-100"><i class="fas fa-plus me-2"></i> Add Account</button>
            </form>
        </div>
    </div>

    {{-- Account List --}}
    <div class="col-12 col-md-8">
        <div class="data-table">
            <div class="table-header">
                <h6><i class="fas fa-list me-2" style="color:var(--green-main)"></i>Accounts ({{ $accounts->total() }})</h6>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Code</th><th>Name</th><th>Type</th><th>Category</th><th>Balance (RM)</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($accounts as $acc)
                        <tr>
                            <td><strong style="color:var(--green-main)">{{ $acc->account_code }}</strong></td>
                            <td>{{ $acc->account_name }}</td>
                            <td>
                                @php $tc=['asset'=>'badge-approved','liability'=>'badge-rejected','equity'=>'badge-pending','revenue'=>'badge-paid','expense'=>'badge-draft'] @endphp
                                <span class="status-badge {{ $tc[$acc->account_type]??'badge-draft' }}">{{ ucfirst($acc->account_type) }}</span>
                            </td>
                            <td><small class="text-muted">{{ str_replace('_',' ',ucfirst($acc->account_category)) }}</small></td>
                            <td><strong>{{ number_format($acc->current_balance, 2) }}</strong></td>
                            <td>
                                @if($acc->is_active)
                                <span class="status-badge badge-approved">Active</span>
                                @else
                                <span class="status-badge badge-rejected">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-list fa-2x d-block mb-2"></i>No accounts found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($accounts->hasPages())
            <div class="p-3">{{ $accounts->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const cats = {
    asset: ['current_asset','fixed_asset'],
    liability: ['current_liability','long_term_liability'],
    equity: ['equity'],
    revenue: ['revenue','other_income'],
    expense: ['cost_of_goods','operating_expense','other_expense']
};
const labels = {current_asset:'Current Asset',fixed_asset:'Fixed Asset',current_liability:'Current Liability',long_term_liability:'Long-term Liability',equity:'Equity',revenue:'Revenue',other_income:'Other Income',cost_of_goods:'Cost of Goods',operating_expense:'Operating Expense',other_expense:'Other Expense'};

function updateCategories() {
    const type = document.getElementById('typeSelect').value;
    const sel = document.getElementById('categorySelect');
    sel.innerHTML = '<option value="">-- Select Category --</option>';
    (cats[type]||[]).forEach(c => sel.innerHTML += `<option value="${c}">${labels[c]}</option>`);
}
</script>
@endpush
