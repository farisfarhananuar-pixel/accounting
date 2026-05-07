{{-- financial_position.blade.php --}}
@extends('layouts.app')
@section('title','Financial Position')
@section('page_title','Statement of Financial Position')
@section('page_subtitle','Balance Sheet')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.profit_loss') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-chart-line"></i></span> Profit & Loss</a>
<a href="{{ route('accountant.financial_position') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-file-alt"></i></span> Financial Position</a>
@endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-box"></i></div><div class="stat-value text-green">RM {{ number_format($assets['total'],2) }}</div><div class="stat-label">Total Assets</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-hand-holding-usd"></i></div><div class="stat-value text-red">RM {{ number_format($liabilities['total'],2) }}</div><div class="stat-label">Total Liabilities</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-landmark"></i></div><div class="stat-value" style="color:#0369a1">RM {{ number_format($equity['total'],2) }}</div><div class="stat-label">Total Equity</div></div></div>
</div>
<div class="row g-4">
    <div class="col-md-6">
        <div class="data-table"><div class="table-header"><h6 style="color:var(--green-main)">ASSETS</h6></div>
        <table class="table"><tbody>
        @foreach($assets['accounts']->groupBy('account_category') as $cat => $accs)
        <tr><td colspan="2" style="background:#f9fafb;font-weight:700;font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px">{{ str_replace('_',' ',$cat) }}</td></tr>
        @foreach($accs as $acc)<tr><td style="padding-left:24px">{{ $acc->account_name }}</td><td class="text-end">{{ number_format($acc->balance,2) }}</td></tr>@endforeach
        @endforeach
        </tbody><tfoot><tr style="background:#d1fae5"><td style="font-weight:800;color:#065f46">TOTAL ASSETS</td><td class="text-end" style="font-weight:800;color:#065f46">RM {{ number_format($assets['total'],2) }}</td></tr></tfoot></table></div>
    </div>
    <div class="col-md-6">
        <div class="data-table"><div class="table-header"><h6 style="color:#dc2626">LIABILITIES & EQUITY</h6></div>
        <table class="table"><tbody>
        <tr><td colspan="2" style="background:#f9fafb;font-weight:700;font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px">LIABILITIES</td></tr>
        @foreach($liabilities['accounts'] as $acc)<tr><td style="padding-left:24px">{{ $acc->account_name }}</td><td class="text-end">{{ number_format($acc->balance,2) }}</td></tr>@endforeach
        <tr><td colspan="2" style="background:#f9fafb;font-weight:700;font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;padding-top:10px">EQUITY</td></tr>
        @foreach($equity['accounts'] as $acc)<tr><td style="padding-left:24px">{{ $acc->account_name }}</td><td class="text-end">{{ number_format($acc->balance,2) }}</td></tr>@endforeach
        </tbody><tfoot>
        <tr><td style="font-weight:700;color:#dc2626">Total Liabilities</td><td class="text-end" style="font-weight:700;color:#dc2626">RM {{ number_format($liabilities['total'],2) }}</td></tr>
        <tr><td style="font-weight:700;color:#0369a1">Total Equity</td><td class="text-end" style="font-weight:700;color:#0369a1">RM {{ number_format($equity['total'],2) }}</td></tr>
        <tr style="background:#fee2e2"><td style="font-weight:800;color:#991b1b">TOTAL LIABILITIES + EQUITY</td><td class="text-end" style="font-weight:800;color:#991b1b">RM {{ number_format($liabilities['total']+$equity['total'],2) }}</td></tr>
        </tfoot></table></div>
    </div>
</div>
@endsection
