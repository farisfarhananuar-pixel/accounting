{{-- bank_reconcilation.blade.php --}}
@extends('layouts.app')
@section('title','Bank Reconciliation')
@section('page_title','Bank Reconciliation')
@section('page_subtitle','Reconcile bank accounts')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.bank_reconciliation') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-university"></i></span> Bank Reconciliation</a>
@endsection
@section('content')
<div class="chart-card">
    <h6 style="color:var(--green-dark);font-weight:700;margin-bottom:20px"><i class="fas fa-university me-2" style="color:var(--green-main)"></i>Bank Accounts</h6>
    @if($bankAccs->count())
    <div class="row g-3">
        @foreach($bankAccs as $bank)
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-blue-soft"><i class="fas fa-piggy-bank"></i></div>
                <div class="stat-value" style="color:#0369a1;font-size:1.3rem">RM {{ number_format($bank->current_balance,2) }}</div>
                <div class="stat-label">{{ $bank->account_name }}</div>
                <div class="stat-change text-muted" style="color:#9ca3af"><small>Code: {{ $bank->account_code }}</small></div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 p-4" style="background:#f0fdf9;border-radius:12px;border:1px solid #a7f3d0">
        <h6 style="color:var(--green-dark);font-weight:700"><i class="fas fa-info-circle me-2"></i>Reconciliation Steps</h6>
        <ol style="color:#374151;font-size:.85rem;line-height:2">
            <li>Compare system balance with your bank statement balance</li>
            <li>Identify any outstanding deposits or payments not yet cleared</li>
            <li>Check for bank charges or interest not yet recorded</li>
            <li>Create journal entries for any differences found</li>
            <li>Confirm final balance matches bank statement</li>
        </ol>
    </div>
    @else
    <div class="text-center py-5" style="color:#9ca3af"><i class="fas fa-university fa-2x d-block mb-2"></i>No bank accounts found. Add bank accounts in Chart of Accounts.</div>
    @endif
</div>
@endsection
