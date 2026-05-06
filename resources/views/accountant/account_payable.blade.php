@extends('layouts.app')
@section('title','Account Payable')
@section('page_title','Account Payable')
@section('page_subtitle','Manage vendor bills')

@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries</a>
<a href="{{ route('accountant.account_receivable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> Account Receivable</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-receipt"></i></span> Account Payable</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-amber-soft"><i class="fas fa-receipt"></i></div>
            <div class="stat-value text-amber">RM {{ number_format($totalAP, 2) }}</div>
            <div class="stat-label">Total Outstanding AP</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-red-soft"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-value text-red">{{ $overdueCount }}</div>
            <div class="stat-label">Overdue Bills</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-tasks"></i></div>
            <div class="stat-value text-green">{{ $bills->total() }}</div>
            <div class="stat-label">Total Bills</div>
        </div>
    </div>
</div>

<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                <option value="overdue" {{ request('status')=='overdue'?'selected':'' }}>Overdue</option>
            </select>
        </div>
        <div class="col-md-8 d-flex gap-2">
            <button type="submit" class="btn btn-green btn-sm px-3"><i class="fas fa-search me-1"></i> Filter</button>
            <a href="{{ route('accountant.account_payable') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
            <a href="{{ route('accountant.account_payable.create') }}" class="btn btn-sm px-3 ms-auto" style="background:#d97706;color:white;border-radius:8px;font-weight:600">
                <i class="fas fa-plus me-1"></i> Record Bill
            </a>
        </div>
    </form>
</div>

<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-receipt me-2" style="color:#d97706"></i>Bills ({{ $bills->total() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>Bill No.</th><th>Vendor</th><th>Bill Date</th><th>Due Date</th><th>Total (RM)</th><th>Balance (RM)</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td><strong style="color:#d97706">{{ $bill->bill_number }}</strong></td>
                    <td>{{ $bill->vendor->name ?? '-' }}</td>
                    <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                    <td>{{ $bill->due_date->format('d/m/Y') }}
                        @if($bill->due_date < now() && !in_array($bill->status,['paid'])) <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.65rem;margin-left:4px">OVERDUE</span> @endif
                    </td>
                    <td><strong>{{ number_format($bill->total_amount, 2) }}</strong></td>
                    <td><strong>{{ number_format($bill->balance_due, 2) }}</strong></td>
                    <td>
                        @php $colors = ['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp
                        <span class="status-badge {{ $colors[$bill->status] ?? 'badge-draft' }}">{{ ucfirst($bill->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('accountant.account_payable.show', $bill->id) }}" class="btn btn-sm px-2" style="background:#fef3c7;color:#92400e;border-radius:6px;font-size:.72rem">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-receipt fa-2x d-block mb-2"></i>No bills found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
    <div class="p-3">{{ $bills->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
