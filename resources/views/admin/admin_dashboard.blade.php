@extends('layouts.app')
@section('title','Admin Dashboard') @section('page_title','Admin Dashboard') @section('page_subtitle','System administration overview')
@section('sidebar_nav')
<span class="nav-section-title">Main Menu</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item-link {{ request()->routeIs('admin.dashboard')?'active':'' }}"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<span class="nav-section-title">User Management</span>
<a href="{{ route('admin.users') }}" class="nav-item-link {{ request()->routeIs('admin.users')?'active':'' }}"><span class="nav-icon"><i class="fas fa-users"></i></span> Manage Users</a>
<a href="{{ route('admin.create_roles') }}" class="nav-item-link {{ request()->routeIs('admin.create_roles')?'active':'' }}"><span class="nav-icon"><i class="fas fa-user-shield"></i></span> Create Roles</a>
@endsection
@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Company Info Banner --}}
<div class="chart-card mb-4" style="background:linear-gradient(135deg,var(--green-dark),var(--green-main));color:white">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 style="margin:0;font-weight:800;color:white">{{ $company->name }}</h4>
            <p style="margin:4px 0 0;opacity:.85;font-size:.85rem"><i class="fas fa-id-card me-2"></i>Reg: {{ $company->registration_number ?? 'N/A' }}</p>
            <p style="margin:2px 0 0;opacity:.75;font-size:.82rem"><i class="fas fa-envelope me-1"></i>{{ $company->email }} &nbsp;|&nbsp; <i class="fas fa-phone me-1"></i>{{ $company->phone }}</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span style="background:rgba(255,255,255,0.2);padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:700;border:1px solid rgba(255,255,255,0.3)">
                <i class="fas fa-check-circle me-1" style="color:#4cde9e"></i> {{ ucfirst($company->subscription_status) }}
            </span>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-green-soft"><i class="fas fa-users"></i></div><div class="stat-value text-green">{{ $totalUsers }}</div><div class="stat-label">Active Users</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-book"></i></div><div class="stat-value" style="color:#0369a1">{{ $totalJournals }}</div><div class="stat-label">Journal Entries</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-purple-soft"><i class="fas fa-file-invoice-dollar"></i></div><div class="stat-value" style="color:#7c3aed">{{ $totalInvoices }}</div><div class="stat-label">Total Invoices</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-receipt"></i></div><div class="stat-value text-amber">{{ $totalBills }}</div><div class="stat-label">Total Bills</div></div></div>
</div>

<div class="row g-4">
    {{-- Role Breakdown --}}
    <div class="col-12 col-md-5">
        <div class="chart-card">
            <div class="card-header-custom"><span class="card-title">👥 Users by Role</span></div>
            @php $roleData=['admin'=>['🔴','Admin','danger'],'manager'=>['🟡','Manager','warning'],'executive_accountant'=>['🟢','Accountant','success'],'auditor'=>['🔵','Auditor','info']]; @endphp
            @foreach($roleData as $role => $info)
            @php $count = $roleBreakdown[$role] ?? 0; @endphp
            <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #f3f4f6">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size:1.2rem">{{ $info[0] }}</span>
                    <span style="font-size:.85rem;font-weight:600;color:#374151">{{ $info[1] }}</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div style="width:100px;background:#f3f4f6;border-radius:20px;height:8px;overflow:hidden">
                        <div style="width:{{ $totalUsers > 0 ? ($count/$totalUsers)*100 : 0 }}%;background:var(--green-main);height:100%;border-radius:20px"></div>
                    </div>
                    <span style="font-weight:800;color:var(--green-main);min-width:20px;text-align:right">{{ $count }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Login Logs --}}
    <div class="col-12 col-md-7">
        <div class="data-table">
            <div class="table-header"><h6><i class="fas fa-history me-2" style="color:var(--green-main)"></i>Recent Login Activity</h6><a href="{{ route('admin.users') }}" class="btn btn-sm btn-green px-3">Manage Users</a></div>
            <div class="table-responsive"><table class="table">
                <thead><tr><th>User</th><th>Role</th><th>Status</th><th>IP Address</th><th>Time</th></tr></thead>
                <tbody>
                @forelse($recentLogins as $log)
                <tr>
                    <td><strong>{{ $log->username_attempted }}</strong></td>
                    <td><small class="text-muted">{{ $log->role ? ucfirst(str_replace('_',' ',$log->role)) : '-' }}</small></td>
                    <td><span class="status-badge {{ $log->status=='success'?'badge-approved':'badge-rejected' }}">{{ ucfirst($log->status) }}</span></td>
                    <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                    <td><small class="text-muted">{{ $log->created_at->diffForHumans() }}</small></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4" style="color:#9ca3af">No login activity yet</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
