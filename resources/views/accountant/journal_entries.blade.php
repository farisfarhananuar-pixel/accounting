@extends('layouts.app')
@section('title','Journal Entries')
@section('page_title','Journal Entries')
@section('page_subtitle','Manage all journal entries')

@section('sidebar_nav')
<span class="nav-section-title">Main Menu</span>
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<span class="nav-section-title">Transactions</span>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries</a>
<a href="{{ route('accountant.account_receivable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> Account Receivable</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-receipt"></i></span> Account Payable</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
<span class="nav-section-title">Reports</span>
<a href="{{ route('accountant.trial_balance') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-balance-scale"></i></span> Trial Balance</a>
<a href="{{ route('accountant.profit_loss') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-chart-line"></i></span> Profit & Loss</a>
<a href="{{ route('accountant.financial_statements') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Financial Statements</a>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:12px;border:none;background:#d1fae5;color:#065f46" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter Bar --}}
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
            <label class="form-label fw-600" style="font-size:.8rem">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Entry no. or description..." value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label fw-600" style="font-size:.8rem">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label fw-600" style="font-size:.8rem">Date From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label fw-600" style="font-size:.8rem">Date To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-green btn-sm px-3"><i class="fas fa-search me-1"></i> Filter</button>
            <a href="{{ route('accountant.journal_entries') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
            <a href="{{ route('accountant.journal_entries.create') }}" class="btn btn-sm px-3 ms-auto" style="background:#7c3aed;color:white;border-radius:8px;font-weight:600">
                <i class="fas fa-plus me-1"></i> New
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Journal Entries ({{ $entries->total() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Entry No.</th><th>Date</th><th>Description</th><th>Reference</th>
                    <th>Debit (RM)</th><th>Credit (RM)</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $e)
                <tr>
                    <td><strong style="color:var(--green-main)">{{ $e->entry_number }}</strong></td>
                    <td>{{ $e->entry_date->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($e->description, 35) }}</td>
                    <td><small class="text-muted">{{ $e->reference ?? '-' }}</small></td>
                    <td><strong>{{ number_format($e->total_debit, 2) }}</strong></td>
                    <td><strong>{{ number_format($e->total_credit, 2) }}</strong></td>
                    <td>{!! $e->status_badge !!}</td>
                    <td>
                        <div class="d-flex gap-1">
                            @if(in_array($e->status, ['draft','rejected']))
                            <form method="POST" action="{{ route('accountant.journal_entries.submit', $e->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-sm px-2" style="background:#d1fae5;color:#065f46;border-radius:6px;font-size:.72rem;font-weight:700" title="Submit for Approval">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                            @endif
                            @if($e->status === 'draft')
                            <a href="{{ route('accountant.journal_entries.edit', $e->id) }}" class="btn btn-sm px-2" style="background:#dbeafe;color:#1e40af;border-radius:6px;font-size:.72rem" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('accountant.journal_entries.delete', $e->id) }}" onsubmit="return confirm('Delete this entry?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm px-2" style="background:#fee2e2;color:#991b1b;border-radius:6px;font-size:.72rem" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                            @if($e->status === 'rejected')
                            <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.7rem;padding:4px 8px;border-radius:6px" title="{{ $e->rejection_reason }}">
                                <i class="fas fa-info-circle me-1"></i>Rejected
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5" style="color:#9ca3af">
                    <i class="fas fa-book fa-2x d-block mb-2"></i>No journal entries found
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())
    <div class="p-3">{{ $entries->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
