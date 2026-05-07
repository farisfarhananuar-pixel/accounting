@extends('layouts.app')
@section('title','Reports Monitor')
@section('page_title','Reports Monitor')
@section('page_subtitle','Financial overview')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-arrow-up"></i></div><div class="stat-value text-green">RM {{ number_format($totalRevenue,2) }}</div><div class="stat-label">Total Revenue</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-arrow-down"></i></div><div class="stat-value text-red">RM {{ number_format($totalExpenses,2) }}</div><div class="stat-label">Total Expenses</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon {{ $netProfit>=0?'bg-green-soft':'bg-red-soft' }}"><i class="fas fa-chart-line"></i></div><div class="stat-value" style="color:{{ $netProfit>=0?'var(--green-main)':'#dc2626' }}">RM {{ number_format(abs($netProfit),2) }}</div><div class="stat-label">Net {{ $netProfit>=0?'Profit':'Loss' }}</div></div></div>
</div>
<div class="row g-3">
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-purple-soft"><i class="fas fa-check-circle"></i></div><div class="stat-value" style="color:#7c3aed">{{ $approvedJournals }}</div><div class="stat-label">Approved Journal Entries</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-file-invoice-dollar"></i></div><div class="stat-value" style="color:#0369a1">RM {{ number_format($totalInvoices,2) }}</div><div class="stat-label">Total Invoice Value</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-receipt"></i></div><div class="stat-value text-amber">RM {{ number_format($totalBills,2) }}</div><div class="stat-label">Total Bill Value</div></div></div>
</div>
@endsection
