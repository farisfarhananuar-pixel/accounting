{{-- tax_calculations.blade.php --}}
@extends('layouts.app')
@section('title','Tax Calculations')
@section('page_title','Tax Calculations')
@section('page_subtitle','SST and Corporate Tax estimates')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.tax_calculations') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-percentage"></i></span> Tax Calculations</a>
<a href="{{ route('accountant.financial_statements') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Financial Statements</a>
@endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-chart-line"></i></div><div class="stat-value text-green">RM {{ number_format($netProfit,2) }}</div><div class="stat-label">Net Profit ({{ $year }})</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-percent"></i></div><div class="stat-value text-amber">RM {{ number_format($sstBase,2) }}</div><div class="stat-label">SST Taxable Sales</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-file-invoice-dollar"></i></div><div class="stat-value" style="color:#0369a1">RM {{ number_format($sstAmount,2) }}</div><div class="stat-label">SST Payable (6%)</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-building"></i></div><div class="stat-value text-red">RM {{ number_format($corpTax,2) }}</div><div class="stat-label">Corporate Tax ({{ $taxRate*100 }}%)</div></div></div>
</div>
<div class="chart-card">
    <h6 style="color:var(--green-dark);font-weight:700;margin-bottom:20px"><i class="fas fa-calculator me-2"></i>Tax Summary — Year {{ $year }}</h6>
    <table class="table">
        <tbody>
        <tr><td>Net Profit Before Tax</td><td class="text-end fw-bold text-green">RM {{ number_format($netProfit,2) }}</td></tr>
        <tr><td>Corporate Tax Rate</td><td class="text-end fw-bold">{{ $taxRate*100 }}% {{ $taxRate==0.17?'(SME rate ≤ RM 600K)':'(Standard rate > RM 600K)' }}</td></tr>
        <tr><td>Estimated Corporate Tax</td><td class="text-end fw-bold text-red">RM {{ number_format($corpTax,2) }}</td></tr>
        <tr style="background:#f9fafb"><td>Net Profit After Tax</td><td class="text-end fw-bold" style="color:var(--green-main)">RM {{ number_format($netProfit-$corpTax,2) }}</td></tr>
        <tr><td colspan="2"><hr><small class="text-muted">SST Section</small></td></tr>
        <tr><td>Total Taxable Sales (SST 6%)</td><td class="text-end fw-bold">RM {{ number_format($sstBase,2) }}</td></tr>
        <tr><td>SST Amount Payable</td><td class="text-end fw-bold text-amber">RM {{ number_format($sstAmount,2) }}</td></tr>
        </tbody>
    </table>
    <div class="alert mt-3" style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;color:#92400e;font-size:.82rem">
        <i class="fas fa-info-circle me-2"></i><strong>Disclaimer:</strong> These are estimates only. Please consult a qualified tax advisor for official tax filings. Corporate tax rates as per LHDN Malaysia guidelines.
    </div>
</div>
@endsection
