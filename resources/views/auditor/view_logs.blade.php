@extends('layouts.app')
@section('title','View Logs') @section('page_title','Login Logs') @section('page_subtitle','All login and logout activity')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection

@section('content')
{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-sign-in-alt"></i></div>
            <div class="stat-value text-green">{{ $totalLogins }}</div>
            <div class="stat-label">Total Successful Logins</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-red-soft"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value text-red">{{ $failedToday }}</div>
            <div class="stat-label">Failed Attempts Today</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-blue-soft"><i class="fas fa-network-wired"></i></div>
            <div class="stat-value" style="color:#0369a1">{{ $uniqueIPs }}</div>
            <div class="stat-label">Unique IP Addresses</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search username..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="success" {{ request('status')=='success'?'selected':'' }}>Success</option>
                <option value="failed" {{ request('status')=='failed'?'selected':'' }}>Failed</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="role" class="form-select form-select-sm">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                <option value="manager" {{ request('role')=='manager'?'selected':'' }}>Manager</option>
                <option value="executive_accountant" {{ request('role')=='executive_accountant'?'selected':'' }}>Accountant</option>
                <option value="auditor" {{ request('role')=='auditor'?'selected':'' }}>Auditor</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-green btn-sm w-100"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-list-ul me-2" style="color:var(--green-main)"></i>Login Logs ({{ $logs->total() }})</h6>
        <a href="{{ route('auditor.view_logs') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>Username</th><th>Role</th><th>Status</th><th>IP Address</th><th>Browser/Device</th><th>Date & Time</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:28px;height:28px;border-radius:6px;background:{{ $log->status=='success'?'linear-gradient(135deg,var(--green-main),var(--green-dark))':'linear-gradient(135deg,#dc2626,#991b1b)' }};display:flex;align-items:center;justify-content:center;color:white;font-size:.65rem;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($log->username_attempted, 0, 2)) }}
                            </div>
                            <strong style="font-size:.85rem">{{ $log->username_attempted }}</strong>
                        </div>
                    </td>
                    <td>
                        @if($log->role)
                        @php $rc=['admin'=>['#fee2e2','#991b1b'],'manager'=>['#fef3c7','#92400e'],'executive_accountant'=>['#d1fae5','#065f46'],'auditor'=>['#dbeafe','#1e40af']]; $rb=$rc[$log->role]??['#f3f4f6','#374151']; @endphp
                        <span style="background:{{ $rb[0] }};color:{{ $rb[1] }};padding:2px 8px;border-radius:20px;font-size:.7rem;font-weight:700">{{ ucfirst(str_replace('_',' ',$log->role)) }}</span>
                        @else <small class="text-muted">—</small> @endif
                    </td>
                    <td>
                        <span class="status-badge {{ $log->status=='success'?'badge-approved':'badge-rejected' }}">
                            <i class="fas fa-{{ $log->status=='success'?'check':'times' }} me-1"></i>{{ ucfirst($log->status) }}
                        </span>
                    </td>
                    <td><code style="background:#f0fdf9;color:var(--green-main);padding:2px 8px;border-radius:4px;font-size:.78rem">{{ $log->ip_address ?? 'N/A' }}</code></td>
                    <td><small class="text-muted">{{ Str::limit($log->user_agent ?? 'Unknown', 40) }}</small></td>
                    <td>
                        <div style="font-size:.82rem;font-weight:600;color:#374151">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                        <div style="font-size:.7rem;color:#9ca3af">{{ $log->created_at->diffForHumans() }}</div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-list fa-2x d-block mb-2"></i>No logs found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="p-3">{{ $logs->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
