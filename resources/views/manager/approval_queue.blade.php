@extends('layouts.app')
@section('title','Approval Queue')
@section('page_title','Approval Queue')
@section('page_subtitle','All items pending your approval')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-book"></i></div><div class="stat-value text-green">{{ $journals->count() }}</div><div class="stat-label">Journal Entries</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-file-invoice-dollar"></i></div><div class="stat-value" style="color:#0369a1">{{ $invoices->count() }}</div><div class="stat-label">Invoices (AR)</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-receipt"></i></div><div class="stat-value text-amber">{{ $bills->count() }}</div><div class="stat-label">Bills (AP)</div></div></div>
</div>
@if($total == 0)
<div class="chart-card text-center py-5"><i class="fas fa-check-circle fa-3x d-block mb-3" style="color:var(--green-main)"></i><h5 style="color:var(--green-dark)">All Clear!</h5><p class="text-muted">No pending approvals at the moment.</p></div>
@else
<div class="chart-card mb-3" style="background:linear-gradient(135deg,#fef3c7,#fffbeb);border:1px solid #fcd34d">
    <div class="d-flex justify-content-between align-items-center">
        <div><h6 style="color:#92400e;margin:0;font-weight:700"><i class="fas fa-clock me-2"></i>{{ $total }} item(s) awaiting approval</h6></div>
        <a href="{{ route('manager.approve_reject') }}" class="btn btn-sm px-4" style="background:#d97706;color:white;border-radius:8px;font-weight:600"><i class="fas fa-check-double me-2"></i>Process All</a>
    </div>
</div>

@if($journals->count())
<div class="data-table mb-3">
    <div class="table-header"><h6 style="color:var(--green-main)"><i class="fas fa-book me-2"></i>Journal Entries ({{ $journals->count() }})</h6><a href="{{ route('manager.approve_reject') }}?type=journal" class="btn btn-sm btn-green px-3">Review</a></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Entry No.</th><th>Date</th><th>Description</th><th>Submitted By</th><th>Amount (RM)</th></tr></thead>
    <tbody>@foreach($journals->take(5) as $j)<tr><td><strong style="color:var(--green-main)">{{ $j->entry_number }}</strong></td><td>{{ $j->entry_date->format('d/m/Y') }}</td><td>{{ Str::limit($j->description,35) }}</td><td><small>{{ $j->creator->name ?? '-' }}</small></td><td><strong>{{ number_format($j->total_debit,2) }}</strong></td></tr>@endforeach</tbody>
    </table></div>
</div>
@endif
@if($invoices->count())
<div class="data-table mb-3">
    <div class="table-header"><h6 style="color:#0369a1"><i class="fas fa-file-invoice-dollar me-2"></i>Invoices ({{ $invoices->count() }})</h6><a href="{{ route('manager.approve_reject') }}?type=invoice" class="btn btn-sm px-3" style="background:#0369a1;color:white;border-radius:8px;font-weight:600">Review</a></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Invoice No.</th><th>Customer</th><th>Amount (RM)</th><th>Due Date</th></tr></thead>
    <tbody>@foreach($invoices->take(5) as $inv)<tr><td><strong style="color:#0369a1">{{ $inv->invoice_number }}</strong></td><td>{{ $inv->customer->name ?? '-' }}</td><td><strong>{{ number_format($inv->total_amount,2) }}</strong></td><td>{{ $inv->due_date->format('d/m/Y') }}</td></tr>@endforeach</tbody>
    </table></div>
</div>
@endif
@endif
@endsection
