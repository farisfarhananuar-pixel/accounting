@extends('layouts.app')
@section('title','Financial Statements')
@section('page_title','Financial Statements')
@section('page_subtitle','Profit & Loss and Financial Position')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.financial_statements') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Financial Statements</a>
<a href="{{ route('accountant.profit_loss') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-chart-line"></i></span> Profit & Loss</a>
<a href="{{ route('accountant.financial_position') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Financial Position</a>
@endsection
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <button onclick="window.print()" class="btn btn-outline-secondary px-4"><i class="fas fa-print me-2"></i> Print All</button>
</div>

{{-- P&L Summary --}}
<div class="chart-card mb-4">
    <h5 style="color:var(--green-dark);font-weight:800;text-align:center;margin-bottom:4px">{{ auth()->user()->company->name }}</h5>
    <h6 style="color:#6b7280;text-align:center;margin-bottom:24px">Statement of Profit or Loss — Year {{ $year }}</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-arrow-up"></i></div><div class="stat-value text-green">RM {{ number_format($revenue['total'],2) }}</div><div class="stat-label">Total Revenue</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-arrow-down"></i></div><div class="stat-value text-red">RM {{ number_format($expenses['total'],2) }}</div><div class="stat-label">Total Expenses</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon {{ $netProfit>=0?'bg-green-soft':'bg-red-soft' }}"><i class="fas fa-chart-line"></i></div><div class="stat-value" style="color:{{ $netProfit>=0?'var(--green-main)':'#dc2626' }}">RM {{ number_format(abs($netProfit),2) }}</div><div class="stat-label">{{ $netProfit>=0?'Net Profit':'Net Loss' }}</div></div></div>
    </div>
</div>

{{-- Balance Sheet Summary --}}
<div class="chart-card">
    <h6 style="color:#6b7280;text-align:center;margin-bottom:24px">Statement of Financial Position — As at {{ now()->format('d F Y') }}</h6>
    <div class="row g-4">
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-box"></i></div><div class="stat-value text-green">RM {{ number_format($assets['total'],2) }}</div><div class="stat-label">Total Assets</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-hand-holding-usd"></i></div><div class="stat-value text-red">RM {{ number_format($liabilities['total'],2) }}</div><div class="stat-label">Total Liabilities</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-landmark"></i></div><div class="stat-value" style="color:#0369a1">RM {{ number_format($equity['total'],2) }}</div><div class="stat-label">Total Equity</div></div></div>
    </div>
    <div class="mt-3 text-center">
        <a href="{{ route('accountant.profit_loss') }}" class="btn btn-green px-4 me-2"><i class="fas fa-chart-line me-2"></i> Full P&L</a>
        <a href="{{ route('accountant.financial_position') }}" class="btn px-4" style="background:#0369a1;color:white;border-radius:8px;font-weight:600"><i class="fas fa-file-alt me-2"></i> Full Balance Sheet</a>
    </div>
</div>
@endsection
