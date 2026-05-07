@extends('layouts.app')
@section('title','Financial Audit') @section('page_title','Audit Financial Report') @section('page_subtitle','Data integrity and financial compliance check')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection

@section('content')
{{-- Integrity Alert --}}
@if($unbalanced > 0)
<div class="alert mb-4" style="background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;color:#991b1b;padding:14px 18px">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Data Integrity Warning!</strong> {{ $unbalanced }} journal {{ Str::plural('entry',$unbalanced) }} found with unbalanced debit/credit. Immediate review required.
</div>
@else
<div class="alert mb-4" style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:12px;color:#065f46;padding:14px 18px">
    <i class="fas fa-check-circle me-2"></i>
    <strong>All Clear!</strong> All journal entries are balanced. No data integrity issues detected.
</div>
@endif

{{-- Journal Stats --}}
<h6 style="color:var(--green-dark);font-weight:700;margin-bottom:12px"><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Journal Entry Summary</h6>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value text-green">{{ $totalApproved }}</div>
            <div class="stat-label">Approved Entries</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-amber-soft"><i class="fas fa-clock"></i></div>
            <div class="stat-value text-amber">{{ $totalPending }}</div>
            <div class="stat-label">Pending Entries</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-red-soft"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value text-red">{{ $totalRejected }}</div>
            <div class="stat-label">Rejected Entries</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon {{ $unbalanced>0?'bg-red-soft':'bg-green-soft' }}"><i class="fas fa-balance-scale"></i></div>
            <div class="stat-value" style="color:{{ $unbalanced>0?'#dc2626':'var(--green-main)' }}">{{ $unbalanced }}</div>
            <div class="stat-label">Unbalanced Entries</div>
        </div>
    </div>
</div>

{{-- AR Stats --}}
<h6 style="color:var(--green-dark);font-weight:700;margin-bottom:12px"><i class="fas fa-file-invoice-dollar me-2" style="color:#0369a1"></i>Account Receivable Summary</h6>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-blue-soft"><i class="fas fa-hashtag"></i></div>
            <div class="stat-value" style="color:#0369a1">{{ $totalInvoices }}</div>
            <div class="stat-label">Total Invoices</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-blue-soft"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="stat-value" style="color:#0369a1">RM {{ number_format($invoiceAmt,2) }}</div>
            <div class="stat-label">Approved Invoice Value</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-check"></i></div>
            <div class="stat-value text-green">RM {{ number_format($invoicePaid,2) }}</div>
            <div class="stat-label">Total Collected</div>
        </div>
    </div>
</div>

{{-- AP Stats --}}
<h6 style="color:var(--green-dark);font-weight:700;margin-bottom:12px"><i class="fas fa-receipt me-2" style="color:#d97706"></i>Account Payable Summary</h6>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-amber-soft"><i class="fas fa-hashtag"></i></div>
            <div class="stat-value text-amber">{{ $totalBills }}</div>
            <div class="stat-label">Total Bills</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-amber-soft"><i class="fas fa-receipt"></i></div>
            <div class="stat-value text-amber">RM {{ number_format($billAmt,2) }}</div>
            <div class="stat-label">Approved Bill Value</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-check"></i></div>
            <div class="stat-value text-green">RM {{ number_format($billPaid,2) }}</div>
            <div class="stat-label">Total Paid</div>
        </div>
    </div>
</div>

{{-- Summary Table --}}
<div class="chart-card">
    <h6 style="color:var(--green-dark);font-weight:700;margin-bottom:16px"><i class="fas fa-clipboard-check me-2"></i>Audit Compliance Summary</h6>
    <table class="table">
        <thead><tr><th>Check Item</th><th>Status</th><th>Details</th></tr></thead>
        <tbody>
            <tr>
                <td><strong>Journal Entry Balance</strong></td>
                <td><span class="status-badge {{ $unbalanced==0?'badge-approved':'badge-rejected' }}">{{ $unbalanced==0?'PASS':'FAIL' }}</span></td>
                <td><small class="text-muted">{{ $unbalanced==0?'All entries balanced':'Found '.$unbalanced.' unbalanced entries' }}</small></td>
            </tr>
            <tr>
                <td><strong>Approval Workflow</strong></td>
                <td><span class="status-badge {{ $totalPending==0?'badge-approved':'badge-pending' }}">{{ $totalPending==0?'COMPLETE':'PENDING' }}</span></td>
                <td><small class="text-muted">{{ $totalPending }} entries still pending approval</small></td>
            </tr>
            <tr>
                <td><strong>AR Collection Rate</strong></td>
                @php $arRate = $invoiceAmt > 0 ? ($invoicePaid/$invoiceAmt)*100 : 0; @endphp
                <td><span class="status-badge {{ $arRate>=80?'badge-approved':($arRate>=50?'badge-pending':'badge-rejected') }}">{{ number_format($arRate,1) }}%</span></td>
                <td><small class="text-muted">RM {{ number_format($invoicePaid,2) }} collected of RM {{ number_format($invoiceAmt,2) }}</small></td>
            </tr>
            <tr>
                <td><strong>AP Payment Rate</strong></td>
                @php $apRate = $billAmt > 0 ? ($billPaid/$billAmt)*100 : 0; @endphp
                <td><span class="status-badge {{ $apRate>=80?'badge-approved':($apRate>=50?'badge-pending':'badge-rejected') }}">{{ number_format($apRate,1) }}%</span></td>
                <td><small class="text-muted">RM {{ number_format($billPaid,2) }} paid of RM {{ number_format($billAmt,2) }}</small></td>
            </tr>
            <tr>
                <td><strong>Rejected Transactions</strong></td>
                <td><span class="status-badge {{ $totalRejected==0?'badge-approved':'badge-pending' }}">{{ $totalRejected }} Rejected</span></td>
                <td><small class="text-muted">{{ $totalRejected==0?'No rejected entries':'Review rejected entries for patterns' }}</small></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
