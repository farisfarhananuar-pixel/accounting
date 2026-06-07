@extends('layouts.app')
@section('title','Bill Detail')
@section('page_title','Bill Detail')
@section('page_subtitle','View bill information')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-file-invoice"></i></span> Account Payable</a>
@endsection
@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('accountant.account_payable') }}" class="btn btn-outline-secondary px-3"><i class="fas fa-arrow-left me-2"></i>Back</a>
    <button onclick="window.print()" class="btn btn-outline-secondary px-3"><i class="fas fa-print me-2"></i>Print</button>
</div>
<div class="chart-card">
    <div class="row mb-4">
        <div class="col-md-6">
            <h5 style="color:var(--green-dark);font-weight:800">{{ auth()->user()->company->name }}</h5>
            <p class="text-muted mb-0">{{ auth()->user()->company->address }}</p>
            <p class="text-muted">{{ auth()->user()->company->phone }} | {{ auth()->user()->company->email }}</p>
        </div>
        <div class="col-md-6 text-end">
            <h4 style="color:var(--green-main);font-weight:800">BILL</h4>
            <p class="mb-1"><strong>{{ $bill->bill_number }}</strong></p>
            <p class="mb-1 text-muted">Date: {{ $bill->bill_date->format('d F Y') }}</p>
            <p class="mb-0 text-muted">Due: {{ $bill->due_date->format('d F Y') }}</p>
        </div>
    </div>
    <hr>
    <div class="row mb-4">
        <div class="col-md-6">
            <strong style="font-size:.8rem;color:#6b7280;text-transform:uppercase">From Vendor:</strong>
            <h6 style="margin-top:6px;color:var(--green-dark)">{{ $bill->vendor->name }}</h6>
            <p class="text-muted mb-0">{{ $bill->vendor->email }}</p>
            <p class="text-muted">{{ $bill->vendor->phone }}</p>
        </div>
        <div class="col-md-6 text-end">
            @php $colors=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','paid'=>'badge-paid','rejected'=>'badge-rejected']; @endphp
            <span class="status-badge {{ $colors[$bill->status]??'badge-draft' }}" style="font-size:.85rem;padding:6px 16px">{{ strtoupper($bill->status) }}</span>
        </div>
    </div>
    <table class="table" style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
        <thead><tr><th>Description</th><th class="text-center">Qty</th><th class="text-end">Unit Price (RM)</th><th class="text-center">Tax %</th><th class="text-end">Amount (RM)</th></tr></thead>
        <tbody>
            @foreach($bill->lines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td class="text-center">{{ $line->quantity }}</td>
                <td class="text-end">{{ number_format($line->unit_price,2) }}</td>
                <td class="text-center">{{ $line->tax_rate }}%</td>
                <td class="text-end"><strong>{{ number_format($line->amount,2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="4" class="text-end">Subtotal:</td><td class="text-end">RM {{ number_format($bill->subtotal,2) }}</td></tr>
            <tr><td colspan="4" class="text-end">Tax:</td><td class="text-end">RM {{ number_format($bill->tax_amount,2) }}</td></tr>
            <tr style="background:#f9fafb"><td colspan="4" class="text-end fw-bold">TOTAL:</td><td class="text-end" style="font-weight:800;color:var(--green-main)">RM {{ number_format($bill->total_amount,2) }}</td></tr>
            <tr><td colspan="4" class="text-end">Paid:</td><td class="text-end text-green">RM {{ number_format($bill->paid_amount,2) }}</td></tr>
            <tr style="background:#fee2e2"><td colspan="4" class="text-end fw-bold">BALANCE DUE:</td><td class="text-end" style="font-weight:800;color:#dc2626">RM {{ number_format($bill->balance_due,2) }}</td></tr>
        </tfoot>
    </table>
    @if($bill->notes)<div class="mt-3 p-3" style="background:#f9fafb;border-radius:8px"><strong style="font-size:.8rem">Notes:</strong><p class="mb-0 text-muted" style="font-size:.85rem">{{ $bill->notes }}</p></div>@endif
</div>
@endsection
