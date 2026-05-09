{{-- fixed_asset_management.blade.php --}}
@extends('layouts.app')
@section('title','Fixed Asset Management')
@section('page_title','Fixed Asset Management')
@section('page_subtitle','Track and manage fixed assets')
@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.fixed_asset') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-building"></i></span> Fixed Assets</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
@endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-purple-soft"><i class="fas fa-building"></i></div><div class="stat-value" style="color:#7c3aed">{{ $assets->count() }}</div><div class="stat-label">Total Fixed Asset Accounts</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-dollar-sign"></i></div><div class="stat-value text-green">RM {{ number_format($assets->sum('current_balance'),2) }}</div><div class="stat-label">Total Book Value</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-chart-bar"></i></div><div class="stat-value text-amber">{{ $assets->where('current_balance','>',0)->count() }}</div><div class="stat-label">Active Assets</div></div></div>
</div>
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-building me-2" style="color:#7c3aed"></i>Fixed Assets Register</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Code</th><th>Asset Name</th><th>Category</th><th class="text-end">Book Value (RM)</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($assets as $asset)
        <tr>
            <td><strong style="color:#7c3aed">{{ $asset->account_code }}</strong></td>
            <td>{{ $asset->account_name }}</td>
            <td><small class="text-muted">{{ str_replace('_',' ',ucfirst($asset->account_category)) }}</small></td>
            <td class="text-end"><strong>{{ number_format($asset->current_balance,2) }}</strong></td>
            <td><span class="status-badge {{ $asset->is_active?'badge-approved':'badge-rejected' }}">{{ $asset->is_active?'Active':'Inactive' }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-building fa-2x d-block mb-2"></i>No fixed assets found</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
