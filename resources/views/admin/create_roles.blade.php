@extends('layouts.app')
@section('title','Create Roles') @section('page_title','Create Roles') @section('page_subtitle','Manage all user roles and accounts')
@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('admin.users') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Manage Users</a>
<a href="{{ route('admin.create_roles') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-user-shield"></i></span> Create Roles</a>
@endsection
@section('content')
<div class="row g-3 mb-4">
    @php
    $roles=[['admin','Administrator','⚙️','#fee2e2','#991b1b','Full system access, manage all users'],['manager','Manager','👔','#fef3c7','#92400e','Approve transactions, view all reports'],['executive_accountant','Executive Accountant','🧮','#d1fae5','#065f46','Create entries, invoices, bills'],['auditor','Auditor','🔍','#dbeafe','#1e40af','Read-only audit access, generate reports']];
    @endphp
    @foreach($roles as $r)
    @php $count = $users->where('role',$r[0])->count(); @endphp
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div style="font-size:2rem;margin-bottom:10px">{{ $r[2] }}</div>
            <div style="font-weight:800;font-size:1.4rem;color:{{ $r[4] }}">{{ $count }}</div>
            <div style="font-weight:700;font-size:.82rem;color:#374151;margin-bottom:4px">{{ $r[1] }}</div>
            <div style="font-size:.72rem;color:#6b7280">{{ $r[5] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-user-shield me-2" style="color:var(--green-main)"></i>All Users — Role Overview ({{ $users->count() }})</h6>
        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-green px-3"><i class="fas fa-plus me-1"></i> Add User</a>
    </div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Email</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
        @forelse($users as $user)
        <tr>
            <td><strong>{{ $user->name }}</strong></td>
            <td><code style="background:#f0fdf9;color:var(--green-main);padding:2px 8px;border-radius:4px;font-size:.78rem">{{ $user->username }}</code></td>
            <td>
                @php $rc=['admin'=>['#fee2e2','#991b1b'],'manager'=>['#fef3c7','#92400e'],'executive_accountant'=>['#d1fae5','#065f46'],'auditor'=>['#dbeafe','#1e40af']]; $rb=$rc[$user->role]??['#f3f4f6','#374151']; @endphp
                <span style="background:{{ $rb[0] }};color:{{ $rb[1] }};padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700">{{ $user->role_label }}</span>
            </td>
            <td><small class="text-muted">{{ $user->email }}</small></td>
            <td><span class="status-badge {{ $user->is_active?'badge-approved':'badge-rejected' }}">{{ $user->is_active?'Active':'Inactive' }}</span></td>
            <td><small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-4" style="color:#9ca3af">No users found</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
