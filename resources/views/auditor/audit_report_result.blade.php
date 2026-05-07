@extends('layouts.app')
@section('title','Audit Report Result') @section('page_title','Audit Report') @section('page_subtitle', ucfirst(str_replace('_',' ',$type)) . ' — ' . \Carbon\Carbon::parse($dateFrom)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($dateTo)->format('d M Y'))
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection
@section('content')

{{-- Report Header --}}
<div class="chart-card mb-4" style="background:linear-gradient(135deg,var(--green-dark),var(--green-main));color:white">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 style="color:white;font-weight:800;margin-bottom:4px">{{ auth()->user()->company->name }}</h4>
            <h6 style="color:rgba(255,255,255,.85);margin-bottom:4px">{{ ucfirst(str_replace('_',' ',$type)) }}</h6>
            <small style="opacity:.75">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d F Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d F Y') }}</small>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
            <button onclick="window.print()" class="btn btn-sm px-3" style="background:rgba(255,255,255,.2);color:white;border:1px solid rgba(255,255,255,.3);border-radius:8px;font-weight:600">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('auditor.audit_report') }}" class="btn btn-sm px-3" style="background:rgba(255,255,255,.15);color:white;border:1px solid rgba(255,255,255,.3);border-radius:8px;font-weight:600">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

{{-- Journals Section --}}
@if(isset($data['journals']))
<div class="data-table mb-4">
    <div class="table-header"><h6><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Journal Entries ({{ $data['journals']->count() }})</h6>
    <div class="d-flex gap-2">
        <span class="status-badge badge-approved">{{ $data['journals']->where('status','approved')->count() }} Approved</span>
        <span class="status-badge badge-rejected">{{ $data['journals']->where('status','rejected')->count() }} Rejected</span>
        <span class="status-badge badge-pending">{{ $data['journals']->where('status','pending')->count() }} Pending</span>
    </div></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Entry No.</th><th>Date</th><th>Description</th><th>By</th><th>Debit (RM)</th><th>Status</th></tr></thead>
        <tbody>@forelse($data['journals'] as $j)
        <tr><td><strong style="color:var(--green-main)">{{ $j->entry_number }}</strong></td><td>{{ $j->entry_date->format('d/m/Y') }}</td><td>{{ Str::limit($j->description,40) }}</td><td><small>{{ $j->creator->name ?? '-' }}</small></td><td>{{ number_format($j->total_debit,2) }}</td><td>{!! $j->status_badge !!}</td></tr>
        @empty<tr><td colspan="6" class="text-center py-3" style="color:#9ca3af">No journal entries in this period</td></tr>@endforelse</tbody>
    </table></div>
</div>
@endif

{{-- Invoices Section --}}
@if(isset($data['invoices']))
<div class="data-table mb-4">
    <div class="table-header"><h6 style="color:#0369a1"><i class="fas fa-file-invoice-dollar me-2"></i>Invoices / AR ({{ $data['invoices']->count() }})</h6>
    <span style="font-weight:700;color:#0369a1">Total: RM {{ number_format($data['invoices']->sum('total_amount'),2) }}</span></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Total (RM)</th><th>Status</th></tr></thead>
        <tbody>@forelse($data['invoices'] as $inv)
        <tr><td><strong style="color:#0369a1">{{ $inv->invoice_number }}</strong></td><td>{{ $inv->customer->name ?? '-' }}</td><td>{{ $inv->invoice_date->format('d/m/Y') }}</td><td>{{ number_format($inv->total_amount,2) }}</td>
        <td>@php $c=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp<span class="status-badge {{ $c[$inv->status]??'badge-draft' }}">{{ ucfirst($inv->status) }}</span></td></tr>
        @empty<tr><td colspan="5" class="text-center py-3" style="color:#9ca3af">No invoices in this period</td></tr>@endforelse</tbody>
    </table></div>
</div>
@endif

{{-- Bills Section --}}
@if(isset($data['bills']))
<div class="data-table mb-4">
    <div class="table-header"><h6 style="color:#d97706"><i class="fas fa-receipt me-2"></i>Bills / AP ({{ $data['bills']->count() }})</h6>
    <span style="font-weight:700;color:#d97706">Total: RM {{ number_format($data['bills']->sum('total_amount'),2) }}</span></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Bill No.</th><th>Vendor</th><th>Date</th><th>Total (RM)</th><th>Status</th></tr></thead>
        <tbody>@forelse($data['bills'] as $bill)
        <tr><td><strong style="color:#d97706">{{ $bill->bill_number }}</strong></td><td>{{ $bill->vendor->name ?? '-' }}</td><td>{{ $bill->bill_date->format('d/m/Y') }}</td><td>{{ number_format($bill->total_amount,2) }}</td>
        <td>@php $c=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp<span class="status-badge {{ $c[$bill->status]??'badge-draft' }}">{{ ucfirst($bill->status) }}</span></td></tr>
        @empty<tr><td colspan="5" class="text-center py-3" style="color:#9ca3af">No bills in this period</td></tr>@endforelse</tbody>
    </table></div>
</div>
@endif

{{-- Audit Trails Section --}}
@if(isset($data['auditTrails']))
<div class="data-table mb-4">
    <div class="table-header"><h6><i class="fas fa-shoe-prints me-2" style="color:var(--green-main)"></i>User Activity ({{ $data['auditTrails']->count() }} actions)</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>Time</th></tr></thead>
        <tbody>@forelse($data['auditTrails']->take(50) as $trail)
        <tr><td><small><strong>{{ $trail->user->name ?? 'System' }}</strong></small></td>
        <td>@php $ac=['login'=>['#d1fae5','#065f46'],'logout'=>['#f3f4f6','#374151'],'create'=>['#dbeafe','#1e40af'],'update'=>['#fef3c7','#92400e'],'delete'=>['#fee2e2','#991b1b'],'approve'=>['#d1fae5','#065f46'],'reject'=>['#fee2e2','#991b1b']]; $c=$ac[$trail->action]??['#f3f4f6','#374151']; @endphp
        <span style="background:{{ $c[0] }};color:{{ $c[1] }};padding:2px 8px;border-radius:20px;font-size:.68rem;font-weight:700">{{ strtoupper($trail->action) }}</span></td>
        <td><small class="text-muted">{{ str_replace('_',' ',ucfirst($trail->module)) }}</small></td>
        <td><small>{{ Str::limit($trail->description,40) }}</small></td>
        <td><small class="text-muted">{{ $trail->created_at->format('d/m H:i') }}</small></td></tr>
        @empty<tr><td colspan="5" class="text-center py-3" style="color:#9ca3af">No activity in this period</td></tr>@endforelse</tbody>
    </table></div>
    @if($data['auditTrails']->count() > 50)<div class="p-3 text-center" style="color:#9ca3af;font-size:.8rem"><i class="fas fa-info-circle me-1"></i>Showing first 50 of {{ $data['auditTrails']->count() }} records. Print for full report.</div>@endif
</div>
@endif

{{-- Financial Integrity Section --}}
@if(isset($data['unbalanced']))
<div class="data-table mb-4">
    <div class="table-header"><h6 style="color:{{ $data['unbalanced']->count()?'#dc2626':'var(--green-main)' }}"><i class="fas fa-shield-alt me-2"></i>Financial Integrity ({{ $data['unbalanced']->count() }} issues found)</h6></div>
    @if($data['unbalanced']->count())
    <div class="table-responsive"><table class="table"><thead><tr><th>Entry No.</th><th>Description</th><th>Debit</th><th>Credit</th><th>Difference</th></tr></thead>
    <tbody>@foreach($data['unbalanced'] as $e)<tr><td><strong style="color:#dc2626">{{ $e->entry_number }}</strong></td><td>{{ $e->description }}</td><td>{{ number_format($e->total_debit,2) }}</td><td>{{ number_format($e->total_credit,2) }}</td><td style="color:#dc2626;font-weight:700">{{ number_format(abs($e->total_debit-$e->total_credit),2) }}</td></tr>@endforeach</tbody></table></div>
    @else<div class="p-4 text-center" style="color:var(--green-main)"><i class="fas fa-check-circle fa-2x d-block mb-2"></i>All journal entries are balanced. No integrity issues.</div>@endif
</div>
@endif

<div class="chart-card text-center" style="background:#f9fafb">
    <small class="text-muted">Report generated by {{ auth()->user()->name }} on {{ now()->format('d F Y, H:i:s') }}</small>
</div>
@endsection
