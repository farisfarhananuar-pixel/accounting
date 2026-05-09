@extends('layouts.app')
@section('title','Account Receivable')
@section('page_title','Account Receivable')
@section('page_subtitle','Manage customer invoices')

@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries</a>
<a href="{{ route('accountant.account_receivable') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> Account Receivable</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-receipt"></i></span> Account Payable</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-blue-soft"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="stat-value" style="color:#0369a1">RM {{ number_format($totalAR, 2) }}</div>
            <div class="stat-label">Total Outstanding AR</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-red-soft"><i class="fas fa-exclamation-circle"></i></div>
            <div class="stat-value text-red">{{ $overdueCount }}</div>
            <div class="stat-label">Overdue Invoices</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value text-green">RM {{ number_format($paidThisMonth, 2) }}</div>
            <div class="stat-label">Paid This Month</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search customer or invoice no..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                <option value="overdue" {{ request('status')=='overdue'?'selected':'' }}>Overdue</option>
            </select>
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button type="submit" class="btn btn-green btn-sm px-3"><i class="fas fa-search me-1"></i> Filter</button>
            <a href="{{ route('accountant.account_receivable') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
            <a href="{{ route('accountant.account_receivable.create') }}" class="btn btn-sm px-3 ms-auto" style="background:#0369a1;color:white;border-radius:8px;font-weight:600">
                <i class="fas fa-plus me-1"></i> New Invoice
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-file-invoice-dollar me-2" style="color:#0369a1"></i>Invoices ({{ $invoices->total() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Due Date</th><th>Total (RM)</th><th>Balance (RM)</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td><strong style="color:#0369a1">{{ $inv->invoice_number }}</strong></td>
                    <td>{{ $inv->customer->name ?? '-' }}</td>
                    <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                    <td>
                        {{ $inv->due_date->format('d/m/Y') }}
                        @if($inv->isOverdue()) <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.65rem;margin-left:4px">OVERDUE</span> @endif
                    </td>
                    <td><strong>{{ number_format($inv->total_amount, 2) }}</strong></td>
                    <td><strong>{{ number_format($inv->balance_due, 2) }}</strong></td>
                    <td>
                        @php
                        $colors = ['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected','overdue'=>'badge-rejected'];
                        @endphp
                        <span class="status-badge {{ $colors[$inv->status] ?? 'badge-draft' }}">{{ ucfirst($inv->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('accountant.account_receivable.show', $inv->id) }}" class="btn btn-sm px-2" style="background:#dbeafe;color:#1e40af;border-radius:6px;font-size:.72rem">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-file-invoice fa-2x d-block mb-2"></i>No invoices found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="p-3">{{ $invoices->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
