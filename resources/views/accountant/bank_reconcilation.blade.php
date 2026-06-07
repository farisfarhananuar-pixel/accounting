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
    <div class="row g-3 mb-4">
        @foreach($bankAccs as $bank)
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-blue-soft"><i class="fas fa-piggy-bank"></i></div>
                <div class="stat-value" style="color:#0369a1;font-size:1.3rem">RM {{ number_format($bank->computed_balance,2) }}</div>
                <div class="stat-label">{{ $bank->account_name }}</div>
                <div class="stat-change text-muted" style="color:#9ca3af"><small>Code: {{ $bank->account_code }}</small></div>
            </div>
        </div>
        @endforeach
    </div>

    @foreach($bankAccs as $bank)
    <div class="mb-4">
        <h6 style="color:var(--green-dark);font-weight:700;margin-bottom:12px">
            <i class="fas fa-list me-2" style="color:var(--green-main)"></i>
            Recent Transactions — {{ $bank->account_name }}
        </h6>
        @if($bank->recent_transactions->count())
        <div class="table-responsive">
            <table class="table table-sm" style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
                <thead style="background:#f0fdf9">
                    <tr>
                        <th>Entry No.</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th class="text-end">Debit (RM)</th>
                        <th class="text-end">Credit (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bank->recent_transactions as $line)
                    <tr>
                        <td><small>{{ $line->journalEntry->entry_number }}</small></td>
                        <td><small>{{ $line->journalEntry->entry_date->format('d/m/Y') }}</small></td>
                        <td><small>{{ $line->journalEntry->description }}</small></td>
                        <td class="text-end"><small>{{ $line->debit > 0 ? number_format($line->debit,2) : '-' }}</small></td>
                        <td class="text-end"><small>{{ $line->credit > 0 ? number_format($line->credit,2) : '-' }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background:#f9fafb">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Book Balance (Approved):</td>
                        <td colspan="2" class="text-end fw-bold" style="color:var(--green-main)">RM {{ number_format($bank->computed_balance,2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <p class="text-muted" style="font-size:.85rem">No approved transactions for this account yet.</p>
        @endif
    </div>
    @endforeach

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
