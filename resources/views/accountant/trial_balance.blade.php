{{-- resources/views/accountant/trial_balance.blade.php --}}
@extends('layouts.app')
@section('title','Trial Balance')
@section('page_title','Trial Balance')
@section('page_subtitle','Verify all account balances')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.trial_balance') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-balance-scale"></i></span> Trial Balance</a>
<a href="{{ route('accountant.profit_loss') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-chart-line"></i></span> Profit & Loss</a>
<a href="{{ route('accountant.financial_position') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Financial Position</a>
<a href="{{ route('accountant.financial_statements') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Financial Statements</a>
@endsection
@section('content')
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-balance-scale me-2" style="color:var(--green-main)"></i>Trial Balance — {{ now()->format('d F Y') }}</h6>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary px-3"><i class="fas fa-print me-1"></i> Print</button>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Code</th><th>Account Name</th><th>Type</th><th class="text-end">Debit (RM)</th><th class="text-end">Credit (RM)</th></tr></thead>
            <tbody>
                @foreach($accounts as $acc)
                <tr>
                    <td><strong style="color:var(--green-main)">{{ $acc->account_code }}</strong></td>
                    <td>{{ $acc->account_name }}</td>
                    <td><small class="text-muted">{{ ucfirst($acc->account_type) }}</small></td>
                    <td class="text-end">{{ $acc->total_debit > 0 ? number_format($acc->total_debit,2) : '-' }}</td>
                    <td class="text-end">{{ $acc->total_credit > 0 ? number_format($acc->total_credit,2) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f9fafb;font-weight:800">
                    <td colspan="3" style="font-size:.9rem;color:var(--green-dark)">TOTAL</td>
                    <td class="text-end" style="color:var(--green-main)">RM {{ number_format($totalDebit,2) }}</td>
                    <td class="text-end" style="color:{{ abs($totalDebit-$totalCredit)<0.01?'var(--green-main)':'#dc2626' }}">RM {{ number_format($totalCredit,2) }}</td>
                </tr>
                @if(abs($totalDebit-$totalCredit)<0.01)
                <tr><td colspan="5" class="text-center py-2" style="background:#d1fae5;color:#065f46;font-weight:700"><i class="fas fa-check-circle me-2"></i>Trial Balance is BALANCED ✓</td></tr>
                @else
                <tr><td colspan="5" class="text-center py-2" style="background:#fee2e2;color:#991b1b;font-weight:700"><i class="fas fa-exclamation-triangle me-2"></i>WARNING: Out of balance by RM {{ number_format(abs($totalDebit-$totalCredit),2) }}</td></tr>
                @endif
            </tfoot>
        </table>
    </div>
</div>
@endsection
