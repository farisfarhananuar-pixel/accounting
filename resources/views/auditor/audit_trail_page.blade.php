@extends('layouts.app')
@section('title','Audit Trail') @section('page_title','Audit Trail') @section('page_subtitle','Full system activity log — journal, AR/AP, approvals, login/logout')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection

@section('content')
{{-- Filter --}}
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Action</label>
            <select name="action" class="form-select form-select-sm">
                <option value="">All Actions</option>
                @foreach(['login','logout','create','update','delete','approve','reject','submit','generate_report'] as $act)
                <option value="{{ $act }}" {{ request('action')==$act?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$act)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Module</label>
            <select name="module" class="form-select form-select-sm">
                <option value="">All Modules</option>
                @foreach(['auth','journal_entry','invoice','bill','user','chart_of_account','audit_report'] as $mod)
                <option value="{{ $mod }}" {{ request('module')==$mod?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$mod)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">User</label>
            <select name="user_id" class="form-select form-select-sm">
                <option value="">All Users</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Date From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Date To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-green btn-sm flex-fill"><i class="fas fa-search me-1"></i>Filter</button>
            <a href="{{ route('auditor.audit_trail') }}" class="btn btn-sm btn-outline-secondary px-2">✕</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-shoe-prints me-2" style="color:var(--green-main)"></i>Audit Trail ({{ $trails->total() }} records)</h6>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary px-3"><i class="fas fa-print me-1"></i>Print</button>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>#</th><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>IP Address</th><th>Date & Time</th></tr>
            </thead>
            <tbody>
                @forelse($trails as $trail)
                <tr>
                    <td><small class="text-muted">{{ $trail->id }}</small></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:28px;height:28px;border-radius:6px;background:linear-gradient(135deg,var(--green-main),var(--green-dark));display:flex;align-items:center;justify-content:center;color:white;font-size:.65rem;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($trail->user->name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:.8rem;font-weight:700">{{ $trail->user->name ?? 'System' }}</div>
                                <div style="font-size:.68rem;color:#9ca3af">{{ $trail->user->role_label ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                        $ac=['login'=>['#d1fae5','#065f46'],'logout'=>['#f3f4f6','#374151'],'create'=>['#dbeafe','#1e40af'],'update'=>['#fef3c7','#92400e'],'delete'=>['#fee2e2','#991b1b'],'approve'=>['#d1fae5','#065f46'],'reject'=>['#fee2e2','#991b1b'],'submit'=>['#ede9fe','#5b21b6'],'generate_report'=>['#f0fdf4','#15803d'],'password_reset'=>['#fff7ed','#c2410c']];
                        $c=$ac[$trail->action]??['#f3f4f6','#374151'];
                        @endphp
                        <span style="background:{{ $c[0] }};color:{{ $c[1] }};padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.3px">{{ $trail->action }}</span>
                    </td>
                    <td><small style="background:#f3f4f6;padding:2px 8px;border-radius:4px;font-size:.75rem">{{ str_replace('_',' ',ucfirst($trail->module)) }}</small></td>
                    <td><small>{{ Str::limit($trail->description, 50) }}</small></td>
                    <td><code style="background:#f0fdf9;color:var(--green-main);padding:2px 6px;border-radius:4px;font-size:.73rem">{{ $trail->ip_address ?? 'N/A' }}</code></td>
                    <td>
                        <div style="font-size:.8rem;font-weight:600;color:#374151">{{ $trail->created_at->format('d/m/Y H:i') }}</div>
                        <div style="font-size:.68rem;color:#9ca3af">{{ $trail->created_at->diffForHumans() }}</div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-shoe-prints fa-2x d-block mb-2"></i>No audit trail records found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($trails->hasPages())
    <div class="p-3">{{ $trails->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
