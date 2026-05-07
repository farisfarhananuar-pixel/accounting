@extends('layouts.app')
@section('title','AP Monitor') @section('page_title','AP Monitor') @section('page_subtitle','Account Payable overview (read-only)')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="row g-3 mb-4"><div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-receipt"></i></div><div class="stat-value text-amber">RM {{ number_format($totalAP,2) }}</div><div class="stat-label">Total Outstanding AP</div></div></div></div>
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-receipt me-2" style="color:#d97706"></i>Bills ({{ $bills->total() }})</h6></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Bill No.</th><th>Vendor</th><th>Bill Date</th><th>Due</th><th>Total (RM)</th><th>Balance (RM)</th><th>Status</th></tr></thead>
    <tbody>@forelse($bills as $bill)<tr><td><strong style="color:#d97706">{{ $bill->bill_number }}</strong></td><td>{{ $bill->vendor->name ?? '-' }}</td><td>{{ $bill->bill_date->format('d/m/Y') }}</td><td>{{ $bill->due_date->format('d/m/Y') }}</td><td>{{ number_format($bill->total_amount,2) }}</td><td><strong>{{ number_format($bill->balance_due,2) }}</strong></td>
    <td>@php $c=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp<span class="status-badge {{ $c[$bill->status]??'badge-draft' }}">{{ ucfirst($bill->status) }}</span></td></tr>
    @empty<tr><td colspan="7" class="text-center py-4" style="color:#9ca3af">No bills found</td></tr>@endforelse</tbody></table></div>
    @if($bills->hasPages())<div class="p-3">{{ $bills->withQueryString()->links() }}</div>@endif
</div>
@endsection
