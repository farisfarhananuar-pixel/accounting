@extends('layouts.app')
@section('title','AR Monitor') @section('page_title','AR Monitor') @section('page_subtitle','Account Receivable overview (read-only)')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="row g-3 mb-4"><div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-file-invoice-dollar"></i></div><div class="stat-value" style="color:#0369a1">RM {{ number_format($totalAR,2) }}</div><div class="stat-label">Total Outstanding AR</div></div></div></div>
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-file-invoice-dollar me-2" style="color:#0369a1"></i>Invoices ({{ $invoices->total() }})</h6></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Due</th><th>Total (RM)</th><th>Balance (RM)</th><th>Status</th></tr></thead>
    <tbody>@forelse($invoices as $inv)<tr><td><strong style="color:#0369a1">{{ $inv->invoice_number }}</strong></td><td>{{ $inv->customer->name ?? '-' }}</td><td>{{ $inv->invoice_date->format('d/m/Y') }}</td><td>{{ $inv->due_date->format('d/m/Y') }}</td><td>{{ number_format($inv->total_amount,2) }}</td><td><strong>{{ number_format($inv->balance_due,2) }}</strong></td>
    <td>@php $c=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp<span class="status-badge {{ $c[$inv->status]??'badge-draft' }}">{{ ucfirst($inv->status) }}</span></td></tr>
    @empty<tr><td colspan="7" class="text-center py-4" style="color:#9ca3af">No invoices found</td></tr>@endforelse</tbody></table></div>
    @if($invoices->hasPages())<div class="p-3">{{ $invoices->withQueryString()->links() }}</div>@endif
</div>
@endsection
