@extends('layouts.app')
@section('title','Profit & Loss')
@section('page_title','Profit & Loss Statement')
@section('page_subtitle','Income statement for the period')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.trial_balance') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-balance-scale"></i></span> Trial Balance</a>
<a href="{{ route('accountant.profit_loss') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-chart-line"></i></span> Profit & Loss</a>
<a href="{{ route('accountant.financial_position') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Financial Position</a>
@endsection
@section('content')
{{-- Filter --}}
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><label class="form-label" style="font-size:.8rem;font-weight:600">Year</label>
            <select name="year" class="form-select form-select-sm">
                @for($y=now()->year; $y>=now()->year-4; $y--)<option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>@endfor
            </select></div>
        <div class="col-md-3"><label class="form-label" style="font-size:.8rem;font-weight:600">Month (optional)</label>
            <select name="month" class="form-select form-select-sm">
                <option value="">All Year</option>
                @foreach(range(1,12) as $m)<option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ date('F',mktime(0,0,0,$m,1)) }}</option>@endforeach
            </select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-green btn-sm px-3 w-100"><i class="fas fa-sync me-1"></i> Apply</button></div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-arrow-up"></i></div><div class="stat-value text-green">RM {{ number_format($revenue['total'],2) }}</div><div class="stat-label">Total Revenue</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-arrow-down"></i></div><div class="stat-value text-red">RM {{ number_format($expenses['total'],2) }}</div><div class="stat-label">Total Expenses</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon {{ $netProfit>=0?'bg-green-soft':'bg-red-soft' }}"><i class="fas fa-chart-line"></i></div><div class="stat-value" style="color:{{ $netProfit>=0?'var(--green-main)':'#dc2626' }}">RM {{ number_format(abs($netProfit),2) }}</div><div class="stat-label">{{ $netProfit>=0?'Net Profit':'Net Loss' }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="data-table"><div class="table-header"><h6 style="color:var(--green-main)"><i class="fas fa-plus-circle me-2"></i>Revenue</h6></div>
        <table class="table"><thead><tr><th>Account</th><th class="text-end">Amount (RM)</th></tr></thead>
        <tbody>@foreach($revenue['accounts'] as $acc)<tr><td>{{ $acc->account_code }} — {{ $acc->account_name }}</td><td class="text-end"><strong style="color:var(--green-main)">{{ number_format($acc->balance,2) }}</strong></td></tr>@endforeach</tbody>
        <tfoot><tr style="background:#d1fae5"><td style="font-weight:800;color:#065f46">TOTAL REVENUE</td><td class="text-end" style="font-weight:800;color:#065f46">RM {{ number_format($revenue['total'],2) }}</td></tr></tfoot></table></div>
    </div>
    <div class="col-md-6">
        <div class="data-table"><div class="table-header"><h6 style="color:#dc2626"><i class="fas fa-minus-circle me-2"></i>Expenses</h6></div>
        <table class="table"><thead><tr><th>Account</th><th class="text-end">Amount (RM)</th></tr></thead>
        <tbody>@foreach($expenses['accounts'] as $acc)<tr><td>{{ $acc->account_code }} — {{ $acc->account_name }}</td><td class="text-end"><strong style="color:#dc2626">{{ number_format($acc->balance,2) }}</strong></td></tr>@endforeach</tbody>
        <tfoot><tr style="background:#fee2e2"><td style="font-weight:800;color:#991b1b">TOTAL EXPENSES</td><td class="text-end" style="font-weight:800;color:#991b1b">RM {{ number_format($expenses['total'],2) }}</td></tr></tfoot></table></div>
    </div>
</div>

<div class="chart-card mt-4">
    <div class="d-flex justify-content-between align-items-center p-2">
        <h5 style="margin:0;font-weight:800;color:var(--green-dark)">NET {{ $netProfit>=0?'PROFIT':'LOSS' }}</h5>
        <h4 style="margin:0;font-weight:800;color:{{ $netProfit>=0?'var(--green-main)':'#dc2626' }}">RM {{ number_format(abs($netProfit),2) }}</h4>
    </div>
</div>
@endsection
