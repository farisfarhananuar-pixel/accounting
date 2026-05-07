@extends('layouts.app')
@section('title','Create Roles') @section('page_title','Create Roles') @section('page_subtitle','Add Accountant or Auditor accounts')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="chart-card">
            <h6 style="color:var(--green-dark);font-weight:700;margin-bottom:20px"><i class="fas fa-user-plus me-2" style="color:var(--green-main)"></i>Create New Account</h6>
            @if($errors->any())<div class="alert" style="background:#fef2f2;color:#dc2626;border-radius:8px;padding:10px;font-size:.82rem;margin-bottom:16px"><i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('manager.create_roles.store') }}">
                @csrf
                <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="executive_accountant">Executive Accountant</option>
                        <option value="auditor">Auditor</option>
                    </select></div>
                <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Ahmad bin Ali" value="{{ old('name') }}" required></div>
                <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. accountant01" value="{{ old('username') }}" required></div>
                <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="user@company.com" value="{{ old('email') }}" required></div>
                <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="01X-XXXXXXXX" value="{{ old('phone') }}"></div>
                <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required></div>
                <div class="mb-4"><label class="form-label" style="font-size:.82rem;font-weight:600">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required></div>
                <button type="submit" class="btn btn-green w-100"><i class="fas fa-user-plus me-2"></i>Create Account</button>
            </form>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="data-table">
            <div class="table-header"><h6><i class="fas fa-users me-2" style="color:var(--green-main)"></i>Accountant & Auditor Accounts ({{ $users->count() }})</h6></div>
            <div class="table-responsive"><table class="table">
                <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Email</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td><code style="background:#f0fdf9;color:var(--green-main);padding:2px 8px;border-radius:4px;font-size:.8rem">{{ $user->username }}</code></td>
                    <td>@php $rc=['executive_accountant'=>['badge-approved','Accountant'],'auditor'=>['badge-paid','Auditor']]; $rb=$rc[$user->role]??['badge-draft','Unknown']; @endphp
                        <span class="status-badge {{ $rb[0] }}">{{ $rb[1] }}</span></td>
                    <td><small class="text-muted">{{ $user->email }}</small></td>
                    <td><span class="status-badge {{ $user->is_active?'badge-approved':'badge-rejected' }}">{{ $user->is_active?'Active':'Inactive' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4" style="color:#9ca3af">No accounts created yet</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>
</div>
@endsection
