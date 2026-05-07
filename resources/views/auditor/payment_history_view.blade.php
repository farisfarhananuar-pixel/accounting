@extends('layouts.app')
@section('title','Payment History') @section('page_title','Payment History') @section('page_subtitle','View all received and made payments')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-arrow-down"></i></div><div class="stat-value text-green">RM {{ number_format($totalPaidAR,2) }}</div><div class="stat-label">Total Received (AR)</div></div></div>
    <div class="col-md-6"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-arrow-up"></i></div><div class="stat-value text-red">RM {{ number_format($totalPaidAP,2) }}</div><div class="stat-label">Total Paid (AP)</div></div></div>
</div>

{{-- Filter --}}
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label" style="font-size:.78rem;font-weight:600">Date From</label><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
        <div class="col-md-3"><label class="form-label" style="font-size:.78rem;font-weight:600">Date To</label><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
        <div class="col-md-3 d-flex gap-2 align-items-end"><button type="submit" class="btn btn-green btn-sm px-3"><i class="fas fa-search me-1"></i>Filter</button><a href="{{ route('auditor.payment_history') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a></div>
    </form>
</div>

{{-- Received Payments (AR) --}}
<h6 style="color:var(--green-dark);font-weight:700;margin-bottom:12px"><i class="fas fa-arrow-down me-2" style="color:var(--green-main)"></i>Received Payments — Customer Invoices</h6>
<div class="data-table mb-4">
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Invoice No.</th><th>Customer</th><th>Invoice Date</th><th>Total (RM)</th><th>Paid By</th><th>Paid Date</th></tr></thead>
        <tbody>
        @forelse($paidInvoices as $inv)
        <tr>
            <td><strong style="color:var(--green-main)">{{ $inv->invoice_number }}</strong></td>
            <td>{{ $inv->customer->name ?? '-' }}</td>
            <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
            <td><strong style="color:var(--green-main)">{{ number_format($inv->total_amount,2) }}</strong></td>
            <td><small>{{ $inv->creator->name ?? '-' }}</small></td>
            <td><small class="text-muted">{{ $inv->updated_at->format('d/m/Y') }}</small></td>
        </tr>
        @empty<tr><td colspan="6" class="text-center py-4" style="color:#9ca3af">No paid invoices found</td></tr>@endforelse
        </tbody>
    </table></div>
    @if($paidInvoices->hasPages())<div class="p-3">{{ $paidInvoices->withQueryString()->links() }}</div>@endif
</div>

{{-- Made Payments (AP) --}}
<h6 style="color:var(--green-dark);font-weight:700;margin-bottom:12px"><i class="fas fa-arrow-up me-2" style="color:#dc2626"></i>Made Payments — Vendor Bills</h6>
<div class="data-table">
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Bill No.</th><th>Vendor</th><th>Bill Date</th><th>Total (RM)</th><th>Processed By</th><th>Paid Date</th></tr></thead>
        <tbody>
        @forelse($paidBills as $bill)
        <tr>
            <td><strong style="color:#d97706">{{ $bill->bill_number }}</strong></td>
            <td>{{ $bill->vendor->name ?? '-' }}</td>
            <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
            <td><strong style="color:#dc2626">{{ number_format($bill->total_amount,2) }}</strong></td>
            <td><small>{{ $bill->creator->name ?? '-' }}</small></td>
            <td><small class="text-muted">{{ $bill->updated_at->format('d/m/Y') }}</small></td>
        </tr>
        @empty<tr><td colspan="6" class="text-center py-4" style="color:#9ca3af">No paid bills found</td></tr>@endforelse
        </tbody>
    </table></div>
    @if($paidBills->hasPages())<div class="p-3">{{ $paidBills->withQueryString()->links() }}</div>@endif
</div>
@endsection
