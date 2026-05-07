@extends('layouts.app')
@section('title','AR / AP View') @section('page_title','AR / AP View') @section('page_subtitle','View invoices and bills — read only')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-file-invoice-dollar"></i></div><div class="stat-value" style="color:#0369a1">RM {{ number_format($totalAR,2) }}</div><div class="stat-label">Outstanding AR</div></div></div>
    <div class="col-md-6"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-receipt"></i></div><div class="stat-value text-amber">RM {{ number_format($totalAP,2) }}</div><div class="stat-label">Outstanding AP</div></div></div>
</div>

{{-- Tabs --}}
<div class="chart-card mb-4">
    <div class="d-flex gap-2">
        <a href="?tab=ar" class="btn btn-sm px-4" style="{{ $tab=='ar'?'background:#0369a1;color:white':'background:#eff6ff;color:#0369a1' }};border-radius:8px;font-weight:600">
            <i class="fas fa-file-invoice-dollar me-1"></i> Account Receivable ({{ $invoices->total() }})
        </a>
        <a href="?tab=ap" class="btn btn-sm px-4" style="{{ $tab=='ap'?'background:#d97706;color:white':'background:#fef3c7;color:#d97706' }};border-radius:8px;font-weight:600">
            <i class="fas fa-receipt me-1"></i> Account Payable ({{ $bills->total() }})
        </a>
    </div>
</div>

@if($tab == 'ar')
<div class="data-table">
    <div class="table-header"><h6 style="color:#0369a1"><i class="fas fa-file-invoice-dollar me-2"></i>Invoices ({{ $invoices->total() }})</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Due Date</th><th>Total (RM)</th><th>Balance (RM)</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($invoices as $inv)
        <tr>
            <td><strong style="color:#0369a1">{{ $inv->invoice_number }}</strong></td>
            <td>{{ $inv->customer->name ?? '-' }}</td>
            <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
            <td>{{ $inv->due_date->format('d/m/Y') }}@if($inv->isOverdue())<span class="badge ms-1" style="background:#fee2e2;color:#991b1b;font-size:.6rem">OD</span>@endif</td>
            <td>{{ number_format($inv->total_amount,2) }}</td>
            <td><strong>{{ number_format($inv->balance_due,2) }}</strong></td>
            <td>@php $c=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp<span class="status-badge {{ $c[$inv->status]??'badge-draft' }}">{{ ucfirst($inv->status) }}</span></td>
        </tr>
        @empty<tr><td colspan="7" class="text-center py-4" style="color:#9ca3af">No invoices found</td></tr>@endforelse
        </tbody>
    </table></div>
    @if($invoices->hasPages())<div class="p-3">{{ $invoices->appends(['tab'=>'ar'])->links() }}</div>@endif
</div>
@else
<div class="data-table">
    <div class="table-header"><h6 style="color:#d97706"><i class="fas fa-receipt me-2"></i>Bills ({{ $bills->total() }})</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Bill No.</th><th>Vendor</th><th>Date</th><th>Due Date</th><th>Total (RM)</th><th>Balance (RM)</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($bills as $bill)
        <tr>
            <td><strong style="color:#d97706">{{ $bill->bill_number }}</strong></td>
            <td>{{ $bill->vendor->name ?? '-' }}</td>
            <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
            <td>{{ $bill->due_date->format('d/m/Y') }}</td>
            <td>{{ number_format($bill->total_amount,2) }}</td>
            <td><strong>{{ number_format($bill->balance_due,2) }}</strong></td>
            <td>@php $c=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp<span class="status-badge {{ $c[$bill->status]??'badge-draft' }}">{{ ucfirst($bill->status) }}</span></td>
        </tr>
        @empty<tr><td colspan="7" class="text-center py-4" style="color:#9ca3af">No bills found</td></tr>@endforelse
        </tbody>
    </table></div>
    @if($bills->hasPages())<div class="p-3">{{ $bills->appends(['tab'=>'ap'])->links() }}</div>@endif
</div>
@endif
@endsection
